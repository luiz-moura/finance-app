<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401220608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE category ALTER id DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('ALTER TABLE transaction ALTER id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_64C19C15E237E06');
        $this->addSql('CREATE SEQUENCE category_id_seq');
        $this->addSql('SELECT setval(\'category_id_seq\', (SELECT MAX(id) FROM category))');
        $this->addSql('ALTER TABLE category ALTER id SET DEFAULT nextval(\'category_id_seq\')');
        $this->addSql('CREATE SEQUENCE budget_id_seq');
        $this->addSql('SELECT setval(\'budget_id_seq\', (SELECT MAX(id) FROM budget))');
        $this->addSql('ALTER TABLE budget ALTER id SET DEFAULT nextval(\'budget_id_seq\')');
        $this->addSql('CREATE SEQUENCE transaction_id_seq');
        $this->addSql('SELECT setval(\'transaction_id_seq\', (SELECT MAX(id) FROM transaction))');
        $this->addSql('ALTER TABLE transaction ALTER id SET DEFAULT nextval(\'transaction_id_seq\')');
    }
}
