<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240708234231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_CE729CDEFB88E14F ON rapport_veterinaire (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDEFB88E14F');
        $this->addSql('DROP INDEX IDX_CE729CDEFB88E14F ON rapport_veterinaire');
        $this->addSql('ALTER TABLE rapport_veterinaire DROP utilisateur_id');
    }
}
