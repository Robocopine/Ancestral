<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525095306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE illustration (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD illustration_id INT NOT NULL, DROP illustration');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD5926566C FOREIGN KEY (illustration_id) REFERENCES illustration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5926566C ON product (illustration_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD5926566C');
        $this->addSql('DROP TABLE illustration');
        $this->addSql('DROP INDEX UNIQ_D34A04AD5926566C ON product');
        $this->addSql('ALTER TABLE product ADD illustration VARCHAR(255) NOT NULL, DROP illustration_id');
    }
}
