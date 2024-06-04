<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604173855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C57EEE8BD30');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C579777D11E');
        $this->addSql('DROP INDEX IDX_2A1D5C579777D11E ON menu_category');
        $this->addSql('DROP INDEX IDX_2A1D5C57EEE8BD30 ON menu_category');
        $this->addSql('ALTER TABLE menu_category ADD menu_id INT NOT NULL, ADD category_id INT NOT NULL, DROP menu_id_id, DROP category_id_id');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C57CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C5712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_2A1D5C57CCD7E912 ON menu_category (menu_id)');
        $this->addSql('CREATE INDEX IDX_2A1D5C5712469DE2 ON menu_category (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C57CCD7E912');
        $this->addSql('ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C5712469DE2');
        $this->addSql('DROP INDEX IDX_2A1D5C57CCD7E912 ON menu_category');
        $this->addSql('DROP INDEX IDX_2A1D5C5712469DE2 ON menu_category');
        $this->addSql('ALTER TABLE menu_category ADD menu_id_id INT NOT NULL, ADD category_id_id INT NOT NULL, DROP menu_id, DROP category_id');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C57EEE8BD30 FOREIGN KEY (menu_id_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C579777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_2A1D5C579777D11E ON menu_category (category_id_id)');
        $this->addSql('CREATE INDEX IDX_2A1D5C57EEE8BD30 ON menu_category (menu_id_id)');
    }
}
