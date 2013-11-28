<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130919123653 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideotracking ADD custom_pixels varchar(1000);");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideotracking DROP IF EXISTS custom_pixels");
    }
}
