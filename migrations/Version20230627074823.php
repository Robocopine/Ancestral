<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230627074823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9D0AFA354');
        $this->addSql('DROP INDEX IDX_8A8E26E9D0AFA354 ON conversation');
        $this->addSql('ALTER TABLE conversation DROP moderator_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation ADD moderator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9D0AFA354 FOREIGN KEY (moderator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8A8E26E9D0AFA354 ON conversation (moderator_id)');
    }
}
