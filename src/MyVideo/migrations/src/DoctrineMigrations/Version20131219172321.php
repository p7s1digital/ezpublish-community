<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

class Version20131219172321 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_contest_page', 'myvideo_contest_page-1.2-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Contest Page - more flexible contests';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_contest_page');
    }
}
