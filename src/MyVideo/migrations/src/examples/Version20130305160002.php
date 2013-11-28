<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration class initializing groups and users
 */
class Version20130305160002 extends eZPublishPackageMigration
{
    /**
     * group definition
     * @var array
     */
    protected $groups = array(
        'parent_node_id' => 5,
        'class_identifier' => 'user_group',
        'remote_id' => 'myvideo-user_group-1',
        'attributes' => array(
            'name' => 'MyVideo Nutzer',
            'description' => 'Hier werden alle Nutzer des MyVideo-CMS angelegt und gepflegt.'
        ),
        'child_nodes' => array(
            array(
                'remote_id' => 'myvideo-user_group-2',
                'roles' => array(
                    'MyVideo Administration'
                ),
                'attributes' => array(
                    'name' => 'MyVideo Administratoren',
                    'description' => 'Hier liegen alle MyVideo Administrator Benutzer.'
                )
            ),
            array(
                'remote_id' => 'myvideo-user_group-3',
                'roles' => array(
                    'MyVideo Administration'
                ),
                'attributes' => array(
                    'name' => 'MyVideo Redaktion',
                    'description' => 'Hier liegen alle MyVideo Redaktion Benutzer.'
                )
            )
        )
    );

    /**
     * user definitions
     * @var array
     */
    protected $users = array(
        array(
            'group_remote_id' => 'myvideo-user_group-2',
            'remote_id' => 'myvideo-user-1',
            'attributes' => array(
                'first_name' => 'Standardbenutzer',
                'last_name' => 'Administration',
                'login' => 'administrator',
                'email' => 'administrator@localhost',
                'password' => 'administrator'
            )
        ),
        array(
            'group_remote_id' => 'myvideo-user_group-3',
            'remote_id' => 'myvideo-user-2',
            'attributes' => array(
                'first_name' => 'Standardbenutzer',
                'last_name' => 'Redaktion',
                'login' => 'redaktion',
                'email' => 'redaktion@localhost',
                'password' => 'redaktion'
            )
        )
    );


    /**
     * upgrade ez-db with import of groups and users
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        //create user groups
        $success = $this->createTreeNode( $this->groups );

        if ( $success ) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: User group \"%s\" imported.';",
                    $this->groups['attributes']['name']
                )
            );

            //import users
            foreach ( $this->users AS $user) {
                $success = $this->createUser( $user );
                if ( $success ) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Success: User \"%s\" imported.';",
                            $user['attributes']['login']
                        )
                    );
                } else {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Error: User \"%s\" was not imported.';",
                            $user['attributes']['login']
                        )
                    );
                }
            }
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: User group  \"%s\" was not imported.';",
                    $this->groups['attributes']['name']
                )
            );
        }

        $this->script->shutdown();
    }


    /**
     * downgrade ez-db with removal of groups and users
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        // delete user groups and users
        $success = $this->removeTreeNode($this->groups['remote_id'] );

        if ( $success ) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: User group \"%s\" with users removed.';",
                    $this->groups['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: User group \"%s\" was not removed.';",
                    $this->groups['attributes']['name']
                )
            );
        }

        $this->script->shutdown();
    }


}
