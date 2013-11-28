<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130919083916 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideosimadconfig ADD dfpsite varchar(255);");
        $this->addSql("ALTER TABLE myvideosimadconfig ADD dfpzone varchar(255);");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideosimadconfig DROP IF EXISTS dfpsite;");
        $this->addSql("ALTER TABLE myvideosimadconfig DROP IF EXISTS dfpzone;");
    }
}
