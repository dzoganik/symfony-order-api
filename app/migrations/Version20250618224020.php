<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250618224020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `order` (
                id INT AUTO_INCREMENT NOT NULL,
                customer_id INT DEFAULT NULL,
                status VARCHAR(50) NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_F52993989395C3F3 (customer_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE order_item (
                id INT AUTO_INCREMENT NOT NULL,
                related_order_id INT DEFAULT NULL,
                product_name VARCHAR(255) NOT NULL,
                quantity INT NOT NULL,
                unit_price NUMERIC(10, 2) NOT NULL,
                INDEX IDX_52EA1F092B1C2395 (related_order_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `order`
            ADD CONSTRAINT FK_F52993989395C3F3
            FOREIGN KEY (customer_id) REFERENCES user (id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE order_item
            ADD CONSTRAINT FK_52EA1F092B1C2395
            FOREIGN KEY (related_order_id)
            REFERENCES `order` (id)
            ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F092B1C2395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `order`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_item
        SQL);
    }
}
