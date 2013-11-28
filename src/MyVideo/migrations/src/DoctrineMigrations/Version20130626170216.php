<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130626170216 extends eZPublishPackageMigration
{

    protected $nodes = array(
        // folder "MyVideo Partner Aktionen" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-nice-url',
            'attributes' => array(
                'name' => 'MyVideo Nice Url',
                'short_name' => 'MyVideo Nice Url',
                'description' => 'Verwaltung der Nice Urls.',
                'short_description' => 'Nice Urls.'
            )
        )
    );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_nice_url', 'myvideo_nice_url-1.0-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Nice Url - initial creation.';")
        );
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
        $this->removePackage('myvideo_nice_url');
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
