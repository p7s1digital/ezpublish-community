<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130627111123 extends eZPublishPackageMigration
{
    protected $nodes = array(
        // folder "MyVideo Partner Aktionen" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-suche',
            'attributes' => array(
                'name' => 'MyVideo Suche',
                'short_name' => 'MyVideo Suche',
                'description' => 'Suchergebnissseite von MyVideo.',
                'short_description' => 'Suchergebnissseite von MyVideo.'
            )
        )
    );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_search', 'myvideo_search-1.0-1.ezpkg');

        $this->addSql("CREATE TABLE IF NOT EXISTS myvideometadata (
            contentobject_id integer default NULL,
            meta_title varchar(255),
            meta_keywords varchar(255),
            meta_description varchar(255),
            meta_robots varchar(255),
            PRIMARY KEY (contentobject_id)
          );
        ");

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach ($this->nodes AS $node) {
            //create and publish tree node
            $success = $this->createTreeNode($node);

            if ($success) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                        $node['attributes']['name'],
                        $node['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                        $node['attributes']['name'],
                        $node['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_search');

        $this->addSql("DROP TABLE IF EXISTS myvideometadata");

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach ($this->nodes AS $node) {
            // remove folders
            $success = $this->removeTreeNode($node['remote_id']);

            if ($success) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                        $node['attributes']['name']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                        $node['attributes']['name']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
