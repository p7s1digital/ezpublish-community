<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131122000000 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->addSql("ALTER TABLE myvideotracking
          ADD COLUMN ivw_two_category varchar(4),
          ADD COLUMN ivw_two_channel varchar(4),
          ADD COLUMN ivw_two_format varchar(255);
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->addSql("ALTER TABLE myvideotracking
          DROP COLUMN ivw_two_category, ivw_two_channel, ivw_two_format;
        ");
    }
}
