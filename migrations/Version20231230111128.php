<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231230111128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD bio VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD website_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD date_of_birth DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP bio');
        $this->addSql('ALTER TABLE "user" DROP website_url');
        $this->addSql('ALTER TABLE "user" DROP location');
        $this->addSql('ALTER TABLE "user" DROP date_of_birth');
    }
}
