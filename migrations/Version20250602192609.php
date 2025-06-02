<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602192609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE average_rating (id SERIAL NOT NULL, episode INT NOT NULL, rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE episode (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, air_date DATE NOT NULL, episode VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, api_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DDAA1CDA54963938 ON episode (api_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE episode_rating (id SERIAL NOT NULL, episode INT NOT NULL, sentinel_score DOUBLE PRECISION NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE average_rating
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE episode
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE episode_rating
        SQL);
    }
}
