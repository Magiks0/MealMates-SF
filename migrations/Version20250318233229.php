<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318233229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availabilities (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, day_of_week VARCHAR(255) NOT NULL, min_time TIME DEFAULT NULL, max_time TIME DEFAULT NULL, INDEX IDX_D7FC41EF9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preferences (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preferences_user (preferences_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9277F8A47CCD6FB7 (preferences_id), INDEX IDX_9277F8A4A76ED395 (user_id), PRIMARY KEY(preferences_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EF9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE preferences_user ADD CONSTRAINT FK_9277F8A47CCD6FB7 FOREIGN KEY (preferences_id) REFERENCES preferences (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE preferences_user ADD CONSTRAINT FK_9277F8A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EF9D86650F');
        $this->addSql('ALTER TABLE preferences_user DROP FOREIGN KEY FK_9277F8A47CCD6FB7');
        $this->addSql('ALTER TABLE preferences_user DROP FOREIGN KEY FK_9277F8A4A76ED395');
        $this->addSql('DROP TABLE availabilities');
        $this->addSql('DROP TABLE preferences');
        $this->addSql('DROP TABLE preferences_user');
    }
}
