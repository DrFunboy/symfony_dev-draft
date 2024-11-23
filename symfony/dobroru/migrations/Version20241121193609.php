<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121193609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE developer_draft (developer_id INT NOT NULL, draft_id INT NOT NULL, INDEX IDX_A9F456C364DD9267 (developer_id), INDEX IDX_A9F456C3E2F3C5D1 (draft_id), PRIMARY KEY(developer_id, draft_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE developer_draft ADD CONSTRAINT FK_A9F456C364DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_draft ADD CONSTRAINT FK_A9F456C3E2F3C5D1 FOREIGN KEY (draft_id) REFERENCES draft (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE developer_draft DROP FOREIGN KEY FK_A9F456C364DD9267');
        $this->addSql('ALTER TABLE developer_draft DROP FOREIGN KEY FK_A9F456C3E2F3C5D1');
        $this->addSql('DROP TABLE developer_draft');
    }
}
