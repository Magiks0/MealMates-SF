<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403084406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF51A5D0B');
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, day_of_week VARCHAR(255) NOT NULL, min_time TIME DEFAULT NULL, max_time TIME DEFAULT NULL, INDEX IDX_3FB7A2BFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dietary (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_dietary (product_id INT NOT NULL, dietary_id INT NOT NULL, INDEX IDX_D4FD4EA74584665A (product_id), INDEX IDX_D4FD4EA784E73D00 (dietary_id), PRIMARY KEY(product_id, dietary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_dietary (user_id INT NOT NULL, dietary_id INT NOT NULL, INDEX IDX_5D294B1DA76ED395 (user_id), INDEX IDX_5D294B1D84E73D00 (dietary_id), PRIMARY KEY(user_id, dietary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_dietary ADD CONSTRAINT FK_D4FD4EA74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_dietary ADD CONSTRAINT FK_D4FD4EA784E73D00 FOREIGN KEY (dietary_id) REFERENCES dietary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_dietary ADD CONSTRAINT FK_5D294B1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_dietary ADD CONSTRAINT FK_5D294B1D84E73D00 FOREIGN KEY (dietary_id) REFERENCES dietary (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE dietetic');
        $this->addSql('DROP INDEX IDX_D34A04ADF51A5D0B ON product');
        $this->addSql('ALTER TABLE product DROP dietetic_id');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD adress VARCHAR(255) DEFAULT NULL, ADD image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dietetic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFA76ED395');
        $this->addSql('ALTER TABLE product_dietary DROP FOREIGN KEY FK_D4FD4EA74584665A');
        $this->addSql('ALTER TABLE product_dietary DROP FOREIGN KEY FK_D4FD4EA784E73D00');
        $this->addSql('ALTER TABLE user_dietary DROP FOREIGN KEY FK_5D294B1DA76ED395');
        $this->addSql('ALTER TABLE user_dietary DROP FOREIGN KEY FK_5D294B1D84E73D00');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE dietary');
        $this->addSql('DROP TABLE product_dietary');
        $this->addSql('DROP TABLE user_dietary');
        $this->addSql('ALTER TABLE user DROP username, DROP adress, DROP image_url');
        $this->addSql('ALTER TABLE product ADD dietetic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF51A5D0B FOREIGN KEY (dietetic_id) REFERENCES dietetic (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADF51A5D0B ON product (dietetic_id)');
    }
}
