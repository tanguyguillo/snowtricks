<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019141200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD token VARCHAR(255) NOT NULL, ADD check_token TINYINT(1) NOT NULL, ADD date DATE NOT NULL, DROP role_user, CHANGE email_user email_user VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64912A5F6CC ON user (email_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64912A5F6CC ON `user`');
        $this->addSql('ALTER TABLE `user` ADD role_user VARCHAR(10) NOT NULL, DROP roles, DROP token, DROP check_token, DROP date, CHANGE email_user email_user VARCHAR(255) DEFAULT NULL');
    }
}
