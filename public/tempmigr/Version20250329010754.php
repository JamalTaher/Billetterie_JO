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
        // this up() migration is auto-generated, please modify it to your needs
        // Suppression de la tentative de recréer la table prix_offre_evenement
        /*
        $this->addSql(<<<SQL
            CREATE TABLE prix_offre_evenement (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, evenement_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_97CF5A7A4CC8505A (offre_id), INDEX IDX_97CF5A7AFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        */
        // Suppression des tentatives de recréer les contraintes de clés étrangères sur prix_offre_evenement
        /*
        $this->addSql(<<<SQL
            ALTER TABLE prix_offre_evenement ADD CONSTRAINT FK_97CF5A7A4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE prix_offre_evenement ADD CONSTRAINT FK_97CF5A7AFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)
        SQL);
        */
        // Suppression de la tentative de supprimer la clé étrangère FK_6EEAA67D4CC8505A (si elle n'existe pas)
        /*
        $this->addSql(<<<SQL
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4CC8505A
        SQL);
        */
        // Suppression de la tentative de supprimer l'index IDX_6EEAA67D4CC8505A (s'il n'existe pas)
        /*
        $this->addSql(<<<SQL
            DROP INDEX IDX_6EEAA67D4CC8505A ON commande
        SQL);
        */
        // Suppression de la tentative de modifier la colonne offre_id (car elle n'existe plus)
        /*
        $this->addSql(<<<SQL
            ALTER TABLE commande CHANGE offre_id prix_offre_evenement_id INT NOT NULL
        SQL);
        */
        $this->addSql(<<<SQL
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D74A842DE FOREIGN KEY (prix_offre_evenement_id) REFERENCES prix_offre_evenement (id)
        SQL);
        $this->addSql(<<<SQL
            CREATE INDEX IDX_6EEAA67D74A842DE ON commande (prix_offre_evenement_id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FFD02F13
        SQL);
        $this->addSql(<<<SQL
            DROP INDEX IDX_AF86866FFD02F13 ON offre
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE offre DROP evenement_id, DROP prix
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify to your needs
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