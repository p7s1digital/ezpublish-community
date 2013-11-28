<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration adding MyVideo navigation
 */
class Version20130305160011 extends eZPublishPackageMigration
{
    /**
     * upgrade ez-db with navigation folder structure
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_navigation', 'myvideo_navigation-1.0-1.ezpkg');
    }

    /**
     * downgrade ez-db with removal of navigation folder structure
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // removal of package from filesystem and db
        // items of package are dropped
        $result = $this->uninstallPackage('myvideo_navigation');

        if( $result !== false ) {
            $this->addSql(
                sprintf( "SELECT 'Success: MyVideo navigation removed';" )
            );
        } else {
            $this->addSql(
                sprintf( "SELECT 'Error: Cannot uninstall MyVideo navigation';" )
            );
        }
    }
}
