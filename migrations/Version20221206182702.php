<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206182702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE members (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, username VARCHAR(255) NOT NULL, last_message VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', other_username VARCHAR(255) NOT NULL, INDEX IDX_45A0D2FFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_messages (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, sender VARCHAR(255) NOT NULL, receiver VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content VARCHAR(255) NOT NULL, INDEX IDX_7C94C13BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE members ADD CONSTRAINT FK_45A0D2FFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE private_messages ADD CONSTRAINT FK_7C94C13BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE chat_event DROP FOREIGN KEY FK_3AD3E73C71F7E88B');
        $this->addSql('DROP TABLE chat_event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_event (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, content VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sender VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3AD3E73C71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE chat_event ADD CONSTRAINT FK_3AD3E73C71F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FFA76ED395');
        $this->addSql('ALTER TABLE private_messages DROP FOREIGN KEY FK_7C94C13BA76ED395');
        $this->addSql('DROP TABLE members');
        $this->addSql('DROP TABLE private_messages');
    }
}
