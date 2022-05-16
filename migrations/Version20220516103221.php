<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220516103221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE key_history (id INT AUTO_INCREMENT NOT NULL, server_id INT DEFAULT NULL, api_key VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_456C54751844E6B7 (server_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE live (id INT AUTO_INCREMENT NOT NULL, server_id INT DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_530F2CAF1844E6B7 (server_id), INDEX IDX_530F2CAFD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE name_history (id INT AUTO_INCREMENT NOT NULL, server_id INT DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_2FD135F51844E6B7 (server_id), INDEX IDX_2FD135F5D17F50A6 (uuid), INDEX IDX_2FD135F5F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE online_history (id INT AUTO_INCREMENT NOT NULL, server_id INT DEFAULT NULL, uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, start_session DATETIME NOT NULL, end_session DATETIME DEFAULT NULL, seconds INT DEFAULT NULL, INDEX IDX_162CA3B51844E6B7 (server_id), INDEX IDX_162CA3B5D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT AUTO_INCREMENT NOT NULL, port INT NOT NULL, web_query_port INT NOT NULL, container_name VARCHAR(255) NOT NULL, api_key VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, started DATETIME DEFAULT NULL, synchronized TINYINT(1) NOT NULL, UNIQUE INDEX container_name_index (container_name), UNIQUE INDEX port_index (port), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server_history (id INT AUTO_INCREMENT NOT NULL, server_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_55C46BF71844E6B7 (server_id), INDEX IDX_55C46BF747CC8C92 (action), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE key_history ADD CONSTRAINT FK_456C54751844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF1844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE name_history ADD CONSTRAINT FK_2FD135F51844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE online_history ADD CONSTRAINT FK_162CA3B51844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE server_history ADD CONSTRAINT FK_55C46BF71844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE key_history DROP FOREIGN KEY FK_456C54751844E6B7');
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF1844E6B7');
        $this->addSql('ALTER TABLE name_history DROP FOREIGN KEY FK_2FD135F51844E6B7');
        $this->addSql('ALTER TABLE online_history DROP FOREIGN KEY FK_162CA3B51844E6B7');
        $this->addSql('ALTER TABLE server_history DROP FOREIGN KEY FK_55C46BF71844E6B7');
        $this->addSql('DROP TABLE key_history');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE name_history');
        $this->addSql('DROP TABLE online_history');
        $this->addSql('DROP TABLE server');
        $this->addSql('DROP TABLE server_history');
    }
}
