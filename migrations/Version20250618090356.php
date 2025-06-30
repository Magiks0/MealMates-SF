<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618090356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, buyer_id INT DEFAULT NULL, seller_id INT DEFAULT NULL, product_id INT DEFAULT NULL, INDEX IDX_6117D13B6C755722 (buyer_id), INDEX IDX_6117D13B8DE820D9 (seller_id), UNIQUE INDEX UNIQ_6117D13B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B6C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B8DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD stripe_product_id VARCHAR(255) NOT NULL, ADD stripe_price_id VARCHAR(255) NOT NULL, ADD published TINYINT(1) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B6C755722
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8DE820D9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP stripe_product_id, DROP stripe_price_id, DROP published
        SQL);
    }
}
