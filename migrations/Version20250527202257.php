<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527202257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, user1_id INT DEFAULT NULL, user2_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_659DF2AA56AE248B (user1_id), INDEX IDX_659DF2AA441B8B65 (user2_id), INDEX IDX_659DF2AA4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, chat_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B6BD307FF675F31B (author_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), INDEX IDX_B6BD307F1A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA56AE248B FOREIGN KEY (user1_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA441B8B65 FOREIGN KEY (user2_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE username username VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA56AE248B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA441B8B65
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1A9A7125
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE chat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
