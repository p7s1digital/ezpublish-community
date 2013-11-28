<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130716181411 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideometadata RENAME meta_title TO title;");
        $this->addSql("ALTER TABLE myvideometadata RENAME meta_keywords TO keywords;");
        $this->addSql("ALTER TABLE myvideometadata RENAME meta_description TO description;");
        $this->addSql("ALTER TABLE myvideometadata RENAME meta_robots TO robots;");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideometadata RENAME title TO meta_title;");
        $this->addSql("ALTER TABLE myvideometadata RENAME keywords TO meta_keywords;");
        $this->addSql("ALTER TABLE myvideometadata RENAME description TO meta_description;");
        $this->addSql("ALTER TABLE myvideometadata RENAME robots TO meta_robots;");
    }
}
