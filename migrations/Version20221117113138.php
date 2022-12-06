<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221117113138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E9B6F3CE2');
        $this->addSql('DROP INDEX IDX_D8F0A91E9B6F3CE2 ON trick');
        $this->addSql('ALTER TABLE trick CHANGE cadegory_id_id cadegory_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EBE86DF93 FOREIGN KEY (cadegory_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D8F0A91EBE86DF93 ON trick (cadegory_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EBE86DF93');
        $this->addSql('DROP INDEX IDX_D8F0A91EBE86DF93 ON trick');
        $this->addSql('ALTER TABLE trick CHANGE cadegory_id cadegory_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E9B6F3CE2 FOREIGN KEY (cadegory_id_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D8F0A91E9B6F3CE2 ON trick (cadegory_id_id)');
    }
}
