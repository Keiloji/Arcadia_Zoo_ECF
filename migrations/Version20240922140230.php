<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240922140230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE security (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, username VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, INDEX IDX_1D1C63B3D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDEA76ED395');
        $this->addSql('DROP INDEX IDX_CE729CDEA76ED395 ON rapport_veterinaire');
        $this->addSql('ALTER TABLE rapport_veterinaire CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_CE729CDEFB88E14F ON rapport_veterinaire (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDEFB88E14F');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D60322AC');
        $this->addSql('DROP TABLE security');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX IDX_CE729CDEFB88E14F ON rapport_veterinaire');
        $this->addSql('ALTER TABLE rapport_veterinaire CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE729CDEA76ED395 ON rapport_veterinaire (user_id)');
    }
}
