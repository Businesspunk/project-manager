<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220614165531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE work_projects_tasks_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE work_projects_tasks (id INT NOT NULL, project_id VARCHAR(255) NOT NULL, author_id VARCHAR(255) NOT NULL, parent_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, plan_date DATE DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, priority SMALLINT NOT NULL, progress SMALLINT NOT NULL, status VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E42D1865166D1F9C ON work_projects_tasks (project_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865F675F31B ON work_projects_tasks (author_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865727ACA70 ON work_projects_tasks (parent_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865AA9E377A ON work_projects_tasks (date)');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.plan_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE work_projects_tasks_members (task_id INT NOT NULL, member_id VARCHAR(255) NOT NULL, PRIMARY KEY(task_id, member_id))');
        $this->addSql('CREATE INDEX IDX_9BB438038DB60186 ON work_projects_tasks_members (task_id)');
        $this->addSql('CREATE INDEX IDX_9BB438037597D3FE ON work_projects_tasks_members (member_id)');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865F675F31B FOREIGN KEY (author_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865727ACA70 FOREIGN KEY (parent_id) REFERENCES work_projects_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks_members ADD CONSTRAINT FK_9BB438038DB60186 FOREIGN KEY (task_id) REFERENCES work_projects_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks_members ADD CONSTRAINT FK_9BB438037597D3FE FOREIGN KEY (member_id) REFERENCES work_members_members (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work_projects_tasks DROP CONSTRAINT FK_E42D1865727ACA70');
        $this->addSql('ALTER TABLE work_projects_tasks_members DROP CONSTRAINT FK_9BB438038DB60186');
        $this->addSql('DROP SEQUENCE work_projects_tasks_seq CASCADE');
        $this->addSql('DROP TABLE work_projects_tasks');
        $this->addSql('DROP TABLE work_projects_tasks_members');
    }
}
