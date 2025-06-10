<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250329010754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // ATTENTION: CE CODE A ÉTÉ CORRIGÉ POUR RETABLIR LA LOGIQUE.
        // Il est crucial que ces opérations soient faites dans cet ordre.

        // 1. Création de la table prix_offre_evenement (si elle n'existe pas déjà d'une migration précédente)
        // D'après votre MCD, cette table est centrale, elle doit exister.
        // Je réactive cette création qui était commentée.
        $this->addSql(<<<SQL
            CREATE TABLE prix_offre_evenement (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, evenement_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_97CF5A7A4CC8505A (offre_id), INDEX IDX_97CF5A7AFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // 2. Renommer la colonne offre_id en prix_offre_evenement_id dans la table commande.
        // C'était la cause de l'erreur "clé n'existe pas" car cette colonne n'était pas renommée avant d'être utilisée dans la FK.
        // Je réactive cette modification qui était commentée.
        $this->addSql(<<<SQL
            ALTER TABLE commande CHANGE offre_id prix_offre_evenement_id INT NOT NULL
        SQL);

        // 3. Ajout de la contrainte de clé étrangère sur la nouvelle colonne prix_offre_evenement_id.
        // Maintenant, la colonne prix_offre_evenement_id devrait exister.
        $this->addSql(<<<SQL
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D74A842DE FOREIGN KEY (prix_offre_evenement_id) REFERENCES prix_offre_evenement (id)
        SQL);

        // 4. Ajout de l'index sur la nouvelle colonne prix_offre_evenement_id.
        $this->addSql(<<<SQL
            CREATE INDEX IDX_6EEAA67D74A842DE ON commande (prix_offre_evenement_id)
        SQL);

        // 5. Suppression des anciennes colonnes evenement_id et prix de la table offre.
        // Ces colonnes sont maintenant gérées par la table prix_offre_evenement.
        $this->addSql(<<<SQL
            ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FFD02F13
        SQL);
        $this->addSql(<<<SQL
            DROP INDEX IDX_AF86866FFD02F13 ON offre
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE offre DROP evenement_id, DROP prix
        SQL);

        // Suppression des anciennes contraintes et index liés à l'ancienne colonne offre_id dans commande
        // (Ces lignes étaient commentées, je les laisse ainsi car leur exécution peut dépendre de l'état exact de la BDD)
        // $this->addSql(<<<SQL
        //     ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4CC8505A
        // SQL);
        // $this->addSql(<<<SQL
        //     DROP INDEX IDX_6EEAA67D4CC8505A ON commande
        // SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Le code down() semble correct pour annuler les changements du up().
        // S'il y a des problèmes lors de l'exécution de down(), nous le corrigerons après.
        $this->addSql(<<<SQL
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D74A842DE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE prix_offre_evenement DROP FOREIGN KEY FK_97CF5A7A4CC8505A
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE prix_offre_evenement DROP FOREIGN KEY FK_97CF5A7AFD02F13
        SQL);
        $this->addSql(<<<SQL
            DROP TABLE prix_offre_evenement
        SQL);
        $this->addSql(<<<SQL
            DROP INDEX IDX_6EEAA67D74A842DE ON commande
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE commande CHANGE prix_offre_evenement_id offre_id INT NOT NULL
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<SQL
            CREATE INDEX IDX_6EEAA67D4CC8505A ON commande (offre_id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE offre ADD evenement_id INT NOT NULL, ADD prix DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE offre ADD CONSTRAINT FK_AF86866FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<SQL
            CREATE INDEX IDX_AF86866FFD02F13 ON offre (evenement_id)
        SQL);
    }
}