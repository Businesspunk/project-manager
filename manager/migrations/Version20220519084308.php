<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519084308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE work_projects_memberships (id UUID NOT NULL, project_id VARCHAR(255) DEFAULT NULL, member_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_41F4DD78166D1F9C ON work_projects_memberships (project_id)');
        $this->addSql('CREATE INDEX IDX_41F4DD787597D3FE ON work_projects_memberships (member_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41F4DD78166D1F9C7597D3FE ON work_projects_memberships (project_id, member_id)');
        $this->addSql('CREATE TABLE work_projects_memberships_departments (membership_id UUID NOT NULL, department_id VARCHAR(255) NOT NULL, PRIMARY KEY(membership_id, department_id))');
        $this->addSql('CREATE INDEX IDX_CBB6C9071FB354CD ON work_projects_memberships_departments (membership_id)');
        $this->addSql('CREATE INDEX IDX_CBB6C907AE80F5DF ON work_projects_memberships_departments (department_id)');
        $this->addSql('CREATE TABLE work_projects_memberships_roles (membership_id UUID NOT NULL, role_id VARCHAR(255) NOT NULL, PRIMARY KEY(membership_id, role_id))');
        $this->addSql('CREATE INDEX IDX_806384881FB354CD ON work_projects_memberships_roles (membership_id)');
        $this->addSql('CREATE INDEX IDX_80638488D60322AC ON work_projects_memberships_roles (role_id)');
        $this->addSql('ALTER TABLE work_projects_memberships ADD CONSTRAINT FK_41F4DD78166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_memberships ADD CONSTRAINT FK_41F4DD787597D3FE FOREIGN KEY (member_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_memberships_departments ADD CONSTRAINT FK_CBB6C9071FB354CD FOREIGN KEY (membership_id) REFERENCES work_projects_memberships (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_memberships_departments ADD CONSTRAINT FK_CBB6C907AE80F5DF FOREIGN KEY (department_id) REFERENCES work_projects_departments (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_memberships_roles ADD CONSTRAINT FK_806384881FB354CD FOREIGN KEY (membership_id) REFERENCES work_projects_memberships (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_memberships_roles ADD CONSTRAINT FK_80638488D60322AC FOREIGN KEY (role_id) REFERENCES work_projects_roles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work_projects_memberships_departments DROP CONSTRAINT FK_CBB6C9071FB354CD');
        $this->addSql('ALTER TABLE work_projects_memberships_roles DROP CONSTRAINT FK_806384881FB354CD');
        $this->addSql('DROP TABLE work_projects_memberships');
        $this->addSql('DROP TABLE work_projects_memberships_departments');
        $this->addSql('DROP TABLE work_projects_memberships_roles');
    }
}
