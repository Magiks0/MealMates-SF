<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701185929 extends AbstractMigration
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
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product DROP FOREIGN KEY FK_8E1EAAC34584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product DROP FOREIGN KEY FK_8E1EAAC3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite_product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability CHANGE min_time min_time TIME NOT NULL, CHANGE max_time max_time TIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA441B8B65
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA56AE248B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AA441B8B65 ON chat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AA56AE248B ON chat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD buyer_id INT DEFAULT NULL, ADD seller_id INT DEFAULT NULL, DROP user1_id, DROP user2_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA6C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA8DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AA6C755722 ON chat (buyer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AA8DE820D9 ON chat (seller_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` CHANGE status purchase_status VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite_product (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8E1EAAC3A76ED395 (user_id), INDEX IDX_8E1EAAC34584665A (product_id), UNIQUE INDEX user_product_unique (user_id, product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC34584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_search DROP FOREIGN KEY FK_D0F6A0BCA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE saved_search
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability CHANGE min_time min_time TIME DEFAULT NULL, CHANGE max_time max_time TIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA6C755722
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA8DE820D9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AA6C755722 ON chat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AA8DE820D9 ON chat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD user1_id INT DEFAULT NULL, ADD user2_id INT DEFAULT NULL, DROP buyer_id, DROP seller_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA441B8B65 FOREIGN KEY (user2_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA56AE248B FOREIGN KEY (user1_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AA441B8B65 ON chat (user2_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AA56AE248B ON chat (user1_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` CHANGE purchase_status status VARCHAR(255) NOT NULL
        SQL);
    }
}
