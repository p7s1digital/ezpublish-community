<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration of basic MyVideo content classes
 */
class Version20130305160000 extends eZPublishPackageMigration
{
    /**
     * upgrade ez-db with import of content classes
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // installing package
        $this->installPackage('myvideo_contentclasses', 'myvideo_contentclasses-1.0-1.ezpkg');
    }

    /**
     * downgrade ez-db with removal of content classes
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // removal of package from filesystem and db
        // items of package are dropped
        $result = $this->uninstallPackage('myvideo_contentclasses');

        if( $result !== false ) {
            $this->addSql(
                sprintf( "SELECT 'Success: MyVideo content-class-package removed';" )
            );

            // remove automatically created content-class group
            $classGroup = \eZContentClassGroup::fetchByName('MyVideoContentTypes');
            if( $classGroup !== false ) {
                $classGroup->remove();
            }
        } else {
            $this->addSql(
                sprintf( "SELECT 'Error: Cannot remove MyVideo content-class-package';" )
            );
        }
    }

}
