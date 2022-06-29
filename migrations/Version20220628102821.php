<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628102821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE champ_like (id INT AUTO_INCREMENT NOT NULL, champion_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_F487B3CDFA7FD7EB (champion_id), INDEX IDX_F487B3CDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE champ_like ADD CONSTRAINT FK_F487B3CDFA7FD7EB FOREIGN KEY (champion_id) REFERENCES champions (id)');
        $this->addSql('ALTER TABLE champ_like ADD CONSTRAINT FK_F487B3CDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE champ_like');
    }
}
