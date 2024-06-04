<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604174350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E839777D11E');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E838E255BBD');
        $this->addSql('DROP INDEX IDX_2E013E839777D11E ON food_category');
        $this->addSql('DROP INDEX IDX_2E013E838E255BBD ON food_category');
        $this->addSql('ALTER TABLE food_category ADD category_id INT NOT NULL, ADD food_id INT NOT NULL, DROP category_id_id, DROP food_id_id');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E8312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E83BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_2E013E8312469DE2 ON food_category (category_id)');
        $this->addSql('CREATE INDEX IDX_2E013E83BA8E87C4 ON food_category (food_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E8312469DE2');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E83BA8E87C4');
        $this->addSql('DROP INDEX IDX_2E013E8312469DE2 ON food_category');
        $this->addSql('DROP INDEX IDX_2E013E83BA8E87C4 ON food_category');
        $this->addSql('ALTER TABLE food_category ADD category_id_id INT NOT NULL, ADD food_id_id INT NOT NULL, DROP category_id, DROP food_id');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E839777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E838E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_2E013E839777D11E ON food_category (category_id_id)');
        $this->addSql('CREATE INDEX IDX_2E013E838E255BBD ON food_category (food_id_id)');
    }
}
