<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230703085745 extends AbstractMigration
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
        $this->addSql('ALTER TABLE conversation ADD is_active TINYINT(1) NOT NULL, DROP moderator_id');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('DROP INDEX IDX_B6BD307FE92F8F78 ON message');
        $this->addSql('ALTER TABLE message DROP recipient_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD recipient_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON message (recipient_id)');
        $this->addSql('ALTER TABLE conversation ADD moderator_id INT DEFAULT NULL, DROP is_active');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9D0AFA354 FOREIGN KEY (moderator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8A8E26E9D0AFA354 ON conversation (moderator_id)');
    }
}
