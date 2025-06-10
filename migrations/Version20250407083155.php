<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407083155 extends AbstractMigration
{
    public function getDescription(): string
    {
        // Description mise à jour pour refléter l'action réelle
        return 'Ajoute les colonnes de vérification d\'email à la table utilisateur.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // CORRECTION: Au lieu de RENOMMER des colonnes qui n'existent pas,
        // nous les ajoutons directement avec les noms finaux pour la vérification d'email.
        // Si ces colonnes existent déjà sous le nom 'reset_token', alors il faudrait une logique de renommage conditionnel
        // ou s'assurer que la migration qui les a CREES est avant celle-ci.
        // Pour l'environnement de test (base vide), la création est la meilleure approche.
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD email_verification_code VARCHAR(255) DEFAULT NULL, ADD email_verification_requested_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Le down() doit supprimer les colonnes que le up() a ajoutées/modifiées.
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP email_verification_code, DROP email_verification_requested_at
        SQL);
        // Les lignes suivantes seraient utiles si les colonnes 'reset_token' existaient AVANT cette migration et qu'on les avait renommées.
        // Puisque nous les ajoutons dans le up(), le down() doit juste les supprimer.
        // $this->addSql(<<<'SQL'
        //     ALTER TABLE utilisateur CHANGE email_verification_code reset_token VARCHAR(255) DEFAULT NULL, CHANGE email_verification_requested_at reset_token_created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        // SQL);
    }
}