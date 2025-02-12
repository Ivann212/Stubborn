<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212103645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_size_stock DROP FOREIGN KEY FK_981CB7C498DA827');
        $this->addSql('DROP TABLE size');
        $this->addSql('ALTER TABLE product_size_stock MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX IDX_981CB7C498DA827 ON product_size_stock');
        $this->addSql('DROP INDEX `primary` ON product_size_stock');
        $this->addSql('ALTER TABLE product_size_stock ADD size VARCHAR(10) NOT NULL, ADD quantity INT NOT NULL, DROP id, DROP size_id, DROP stock');
        $this->addSql('ALTER TABLE product_size_stock ADD PRIMARY KEY (product_id, size)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE size (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_size_stock ADD id INT AUTO_INCREMENT NOT NULL, ADD stock INT NOT NULL, DROP size, CHANGE quantity size_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE product_size_stock ADD CONSTRAINT FK_981CB7C498DA827 FOREIGN KEY (size_id) REFERENCES size (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_981CB7C498DA827 ON product_size_stock (size_id)');
    }
}
