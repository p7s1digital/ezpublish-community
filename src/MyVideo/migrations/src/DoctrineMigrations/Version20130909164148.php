<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130909164148 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS myvideosimadconfig (
            contentobject_id integer default NULL,
            channel varchar(255),
            subchannel1 varchar(255),
            subchannel2 varchar(255),
            PRIMARY KEY (contentobject_id)
          );
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS myvideosimadconfig");
    }
}
