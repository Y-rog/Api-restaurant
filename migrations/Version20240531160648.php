<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531160648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, guest_number SMALLINT NOT NULL, order_date DATE NOT NULL, order_hour DATETIME NOT NULL, allergy VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, INDEX IDX_E00CEDDEB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, price SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food_category (id INT AUTO_INCREMENT NOT NULL, category_id_id INT NOT NULL, food_id_id INT NOT NULL, INDEX IDX_2E013E839777D11E (category_id_id), INDEX IDX_2E013E838E255BBD (food_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, price SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, INDEX IDX_7D053A93B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_category (id INT AUTO_INCREMENT NOT NULL, menu_id_id INT NOT NULL, category_id_id INT NOT NULL, INDEX IDX_2A1D5C57EEE8BD30 (menu_id_id), INDEX IDX_2A1D5C579777D11E (category_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, INDEX IDX_16DB4F89B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description LONGTEXT NOT NULL, am_opening_time JSON NOT NULL, pm_opening_time JSON NOT NULL, max_guest SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_EB95123F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, guest_number SMALLINT NOT NULL, allergy VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E839777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E838E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C57EEE8BD30 FOREIGN KEY (menu_id_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C579777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB1E7706E');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E839777D11E');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E838E255BBD');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93B1E7706E');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C57EEE8BD30');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C579777D11E');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B1E7706E');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F7E3C61F9');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_category');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_category');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE user');
    }
}
