<?php

namespace Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171105130615 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, qty INT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AFBEFB4D8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_order (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, seller_id INT NOT NULL, order_status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_order_item ADD CONSTRAINT FK_AFBEFB4D8D9F6D38 FOREIGN KEY (order_id) REFERENCES sale_order (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_order_item DROP FOREIGN KEY FK_AFBEFB4D8D9F6D38');
        $this->addSql('DROP TABLE sale_order_item');
        $this->addSql('DROP TABLE sale_order');
    }
}
