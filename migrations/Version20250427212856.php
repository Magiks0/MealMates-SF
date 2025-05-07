<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20250414194034.php
final class Version20250414194034 extends AbstractMigration
========
final class Version20250427212856 extends AbstractMigration
>>>>>>>> master:migrations/Version20250427212856.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250414194034.php
        $this->addSql('ALTER TABLE user ADD address VARCHAR(255) DEFAULT NULL');
========
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
>>>>>>>> master:migrations/Version20250427212856.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250414194034.php
        $this->addSql('ALTER TABLE user DROP address');
========
        $this->addSql('DROP TABLE address');
>>>>>>>> master:migrations/Version20250427212856.php
    }
}
