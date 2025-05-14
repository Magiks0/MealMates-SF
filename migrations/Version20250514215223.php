<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514215223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD alert_sent TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE INDEX idx_product_peremption ON product (peremption_date)');
        $this->addSql('ALTER TABLE saved_search CHANGE name name VARCHAR(100) NOT NULL, CHANGE filters filters JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_product_peremption ON product');
        $this->addSql('ALTER TABLE product DROP alert_sent');
        $this->addSql('ALTER TABLE saved_search CHANGE name name VARCHAR(100) DEFAULT NULL, CHANGE filters filters JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
