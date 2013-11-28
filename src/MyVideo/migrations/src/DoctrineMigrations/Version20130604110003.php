<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130604110003 extends eZPublishPackageMigration
{


    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_api_content', 'myvideo_api_content-1.0-1.ezpkg');

        $classID = \eZContentClass::fetchByIdentifier('myvideo_api_text')->ID;
        $objects = \eZContentObject::fetchSameClassList($classID);

        $script = $this->getEzScriptInstance();

        foreach ($objects AS $object) {
            //publish object
            $this->publishObject( $object );
        }

        $classID = \eZContentClass::fetchByIdentifier('myvideo_api_tv_home')->ID;
        $objects = \eZContentObject::fetchSameClassList($classID);

        foreach ($objects AS $object) {
            //publish object
            $this->publishObject( $object );
        }

        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo API Folders and Content imported;'" )
        );

        $script->shutdown();
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_api_content');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo API Folders and Content removed;'" )
        );

    }
}
