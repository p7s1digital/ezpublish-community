<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

class Version20131030145135 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.5-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Format Page - Added eyecatcher field';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_format_page');
    }
}
