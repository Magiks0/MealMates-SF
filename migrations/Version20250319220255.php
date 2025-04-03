<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319220255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EF9D86650F');
        $this->addSql('DROP INDEX IDX_D7FC41EF9D86650F ON availabilities');
        $this->addSql('ALTER TABLE availabilities CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D7FC41EFA76ED395 ON availabilities (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EFA76ED395');
        $this->addSql('DROP INDEX IDX_D7FC41EFA76ED395 ON availabilities');
        $this->addSql('ALTER TABLE availabilities CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EF9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D7FC41EF9D86650F ON availabilities (user_id_id)');
    }
}
