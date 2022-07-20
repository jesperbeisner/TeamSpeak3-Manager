<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720052727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Init migration.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE key_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, server_id INTEGER DEFAULT NULL, api_key VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_456C54751844E6B7 ON key_history (server_id)');
        $this->addSql('CREATE TABLE live (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, server_id INTEGER DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_530F2CAF1844E6B7 ON live (server_id)');
        $this->addSql('CREATE INDEX IDX_530F2CAFD17F50A6 ON live (uuid)');
        $this->addSql('CREATE TABLE name_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, server_id INTEGER DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2FD135F51844E6B7 ON name_history (server_id)');
        $this->addSql('CREATE INDEX IDX_2FD135F5D17F50A6 ON name_history (uuid)');
        $this->addSql('CREATE INDEX IDX_2FD135F5F85E0677 ON name_history (username)');
        $this->addSql('CREATE TABLE online_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, server_id INTEGER DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, start_session DATETIME NOT NULL, end_session DATETIME DEFAULT NULL, seconds INTEGER DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_162CA3B51844E6B7 ON online_history (server_id)');
        $this->addSql('CREATE INDEX IDX_162CA3B5D17F50A6 ON online_history (uuid)');
        $this->addSql('CREATE TABLE server (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, port INTEGER NOT NULL, web_query_port INTEGER NOT NULL, container_name VARCHAR(255) NOT NULL, api_key VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, started DATETIME DEFAULT NULL, synchronized BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX container_name_index ON server (container_name)');
        $this->addSql('CREATE UNIQUE INDEX port_index ON server (port)');
        $this->addSql('CREATE TABLE server_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, server_id INTEGER DEFAULT NULL, "action" VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_55C46BF71844E6B7 ON server_history (server_id)');
        $this->addSql('CREATE INDEX IDX_55C46BF747CC8C92 ON server_history ("action")');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE key_history');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE name_history');
        $this->addSql('DROP TABLE online_history');
        $this->addSql('DROP TABLE server');
        $this->addSql('DROP TABLE server_history');
    }
}
