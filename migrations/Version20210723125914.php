<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210723125914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the relation between Client and EndUser';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_user ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE end_user ADD CONSTRAINT FK_A3515A0D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_A3515A0D19EB6921 ON end_user (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_user DROP FOREIGN KEY FK_A3515A0D19EB6921');
        $this->addSql('DROP INDEX IDX_A3515A0D19EB6921 ON end_user');
        $this->addSql('ALTER TABLE end_user DROP client_id');
    }
}
