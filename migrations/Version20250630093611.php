<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630093611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, is_read TINYINT(1) NOT NULL, notified_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product DROP FOREIGN KEY FK_8E1EAAC3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product DROP FOREIGN KEY FK_8E1EAAC34584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite_product
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite_product (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8E1EAAC3A76ED395 (user_id), INDEX IDX_8E1EAAC34584665A (product_id), UNIQUE INDEX user_product_unique (user_id, product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC34584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
    }
}
