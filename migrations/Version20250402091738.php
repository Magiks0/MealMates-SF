<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20250402091738.php
final class Version20250402091738 extends AbstractMigration
========
final class Version20250402091130 extends AbstractMigration
>>>>>>>> master:migrations/Version20250402091130.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250402091738.php
        $this->addSql('ALTER TABLE user ADD location VARCHAR(255) DEFAULT NULL');
========
        $this->addSql('ALTER TABLE user CHANGE note note INT DEFAULT NULL');
>>>>>>>> master:migrations/Version20250402091130.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250402091738.php
        $this->addSql('ALTER TABLE user DROP location');
========
        $this->addSql('ALTER TABLE user CHANGE note note INT NOT NULL');
>>>>>>>> master:migrations/Version20250402091130.php
    }
}
