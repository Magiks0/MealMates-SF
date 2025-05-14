<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507114202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, day_of_week VARCHAR(255) NOT NULL, min_time TIME DEFAULT NULL, max_time TIME DEFAULT NULL, INDEX IDX_3FB7A2BFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dietary (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, original_name VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8C9F36104584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, adress VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_5E9E89CB4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type_id INT DEFAULT NULL, address_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, quantity INT NOT NULL, peremption_date DATE NOT NULL, price DOUBLE PRECISION NOT NULL, donation TINYINT(1) NOT NULL, collection_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D34A04ADA76ED395 (user_id), INDEX IDX_D34A04ADC54C8C93 (type_id), INDEX IDX_D34A04ADF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_dietary (product_id INT NOT NULL, dietary_id INT NOT NULL, INDEX IDX_D4FD4EA74584665A (product_id), INDEX IDX_D4FD4EA784E73D00 (dietary_id), PRIMARY KEY(product_id, dietary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, note DOUBLE PRECISION DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_dietary (user_id INT NOT NULL, dietary_id INT NOT NULL, INDEX IDX_5D294B1DA76ED395 (user_id), INDEX IDX_5D294B1D84E73D00 (dietary_id), PRIMARY KEY(user_id, dietary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE file ADD CONSTRAINT FK_8C9F36104584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_dietary ADD CONSTRAINT FK_D4FD4EA74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_dietary ADD CONSTRAINT FK_D4FD4EA784E73D00 FOREIGN KEY (dietary_id) REFERENCES dietary (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_dietary ADD CONSTRAINT FK_5D294B1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_dietary ADD CONSTRAINT FK_5D294B1D84E73D00 FOREIGN KEY (dietary_id) REFERENCES dietary (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE file DROP FOREIGN KEY FK_8C9F36104584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_dietary DROP FOREIGN KEY FK_D4FD4EA74584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_dietary DROP FOREIGN KEY FK_D4FD4EA784E73D00
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_dietary DROP FOREIGN KEY FK_5D294B1DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_dietary DROP FOREIGN KEY FK_5D294B1D84E73D00
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE address
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE availability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dietary
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE file
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_dietary
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_dietary
        SQL);
    }
}
