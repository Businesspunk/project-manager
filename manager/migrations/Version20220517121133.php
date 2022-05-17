<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517121133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE work_projects_departments (id VARCHAR(255) NOT NULL, project_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D10022DA166D1F9C ON work_projects_departments (project_id)');
        $this->addSql('ALTER TABLE work_projects_departments ADD CONSTRAINT FK_D10022DA166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE work_projects_departments');
    }
}
