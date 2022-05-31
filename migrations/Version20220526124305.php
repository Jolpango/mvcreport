<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220526124305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT name, password, balance, admin FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, balance INTEGER NOT NULL, admin BOOLEAN NOT NULL, PRIMARY KEY(name))');
        $this->addSql('INSERT INTO user (name, password, balance, admin) SELECT name, password, balance, admin FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT name, password, balance, admin FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, balance INTEGER NOT NULL, admin BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (name, password, balance, admin) SELECT name, password, balance, admin FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}
