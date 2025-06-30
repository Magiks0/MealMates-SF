<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527203250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE saved_search (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, criteria JSON NOT NULL COMMENT '(DC2Type:json)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_D0F6A0BCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_search ADD CONSTRAINT FK_D0F6A0BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_search DROP FOREIGN KEY FK_D0F6A0BCA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE saved_search
        SQL);
    }
}
