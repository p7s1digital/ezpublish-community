<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Migrations\Version,
    Doctrine\DBAL\Schema\Schema;

/**
 * Loads the import package version 1.1.
 *
 * Imports and installs content class packages.
 *
 * @author Max Keil <m.keil@all2e.com>
 * @author Peter Hoffmann <peter.hoffmann@magicinternet.de>
 * @author Christopher Queiser <christopher.queiser@magicinetrnet.de>
 */
abstract class eZPublishPackageMigration extends AbstractMigration
{

    // ezpublish remote ids of several myvideo-groups
    protected $groupNames = array(
        'EDITORS' => 'myvideo-user_group-3',
        'ADMINISTRATORS' => 'myvideo-user_group-2',
        'USERS' => 'myvideo-user_group-1',
    );

    /**
     * Settings for the eZ publish script instance.
     *
     * @var array
     */
    protected $settings = array(
        'description' => 'Introduces the video import classes.',
        'use-modules' => true,
        'use-session' => false,
        'use-extensions' => true,
        'site-access' => 'ezflow_site_clean_admin'
    );

    /**
     * The eZScript instance to initialize eZ publish.
     *
     * @var \eZScript
     */
    protected $script = null;

    /**
     * Declaration used for ContentAttribute Datatype ezxmlstring
     *
     * @var string
     */
    protected $xmlDeclaration = '<?xml version="1.0" encoding="utf-8"?>
        <section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"
                xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/"
                xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    /**
     * Initializes and returns the eZ script instance.
     *
     * @param \eZScript $script An eZScript instance.
     *
     * @return \eZScript
     */
    public function getEzScriptInstance(\eZScript $script = null)
    {
        if ($script === null) {
            $script = \eZScript::instance($this->settings);
            $script->startup();
            $script->initialize();
        }

        // Initialize user.
        $loggedInUserID = 14;
        $user = \eZUser::fetch($loggedInUserID);
        \eZUser::setCurrentlyLoggedInUser($user, $loggedInUserID);

        return $script;

    }

    protected function initScripting()
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance();
    }

    protected function shutdownScripting()
    {
        //shutdown script
        $this->script->shutdown();
    }


    /**
     * Installs a new package.
     *
     * @param string $name The name for the package.
     * @param string $file The location of the package.
     *
     * @return void
     */
    public function installPackage($name, $file)
    {
        $script = $this->getEzScriptInstance();
        $file = __DIR__ . '/../packages/' . $file;
        // Abort migration if package does not exists.
        $this->abortIf(!file_exists($file), sprintf('File %s does not exists.', $file));

        $user = \eZUser::currentUser();
        $package = \nmbPackage::fetch($name);

        if ($package !== false) {
            $package->remove();
        }

        $package = \nmbPackage::import($file, $name, true, 'local');

        $installParameters = array(
            'site_access_map' => array('*' => false),
            'top_nodes_map' => array('*' => 2),
            'design_map' => array('*' => false),
            'restore_dates' => true,
            'user_id' => $user->attribute('contentobject_id'),
            'non-interactive' => true,
            'language_map' => $package->defaultLanguageMap()
        );

        $package->install($installParameters);

        $script->shutdown();

        // Dummy to make Doctrine work.
        $this->addSql(sprintf("SELECT 'install %s';", $name));

        // delete obsolete content class attributes
        $rows = $this->getAllObsoleteContentClassAttributeIds();
        $this->deleteObsoleteContentClassAttribute($rows);

    }


    /**
     * Replaces packages.
     *
     * The package with $name is replaced with the new package. Therefore the old one
     * is removed and the new one is installed.
     *
     * @param string $name           The name of the old / new package.
     * @param string $file           The file of the new package.
     * @param string $newPackageName Sets the name of the new package.
     *
     * @return void
     */
    public function replacePackageWith($name, $file, $newPackageName = null)
    {
        $this->removePackage($name);

        if ($newPackageName !== null) {
            $name = $newPackageName;
        }

        $this->installPackage($name, $file);

    }


    /**
     * Removes a package with a specific name.
     *
     * @param string $name Package name.
     *
     * @return void
     */
    public function removePackage($name)
    {
        $script = $this->getEzScriptInstance();

        $package = \nmbPackage::fetch($name);

        if ($package !== false) {
            $package->remove();
        }

        $script->shutdown();

        // Dummy to make Doctrine work.
        $this->addSql(sprintf("SELECT 'remove %s';", $name));

    }

    /**
     * Convenience method that accepts an array describing a content class package:
     * - content_class_id: unique identifier of content class being installed
     * - file: file name of .ezpkg file
     * - description: readable name
     *
     * Forwards to $this->installPackage() and prints message.
     * @param array $package
     */
    protected function installContentClass(array $package)
    {
        $this->installPackage($package['content_class_id'], $package['file']);
        $this->addSql(
            sprintf("SELECT 'Success: {$package['description']} - Added content class';")
        );

    }

    /**
     * Convenience method that accepts an array describing a content class package:
     * - content_class_id: unique identifier of content class being installed
     * - file: file name of .ezpkg file
     * - description: readable name
     *
     * Forwards to $this->removePackage() and prints message.
     * @param array $package
     */
    protected function removeContentClass(array $package)
    {
        $this->removePackage($package['content_class_id']);
        $this->addSql(
            sprintf("SELECT 'Success: {$package['description']} - Removed content class';")
        );
    }


    /**
     * Creates a content object. Returns the object.
     *
     * Options are:
     *   - remote_id, id to identifie the node.
     *   - fields, an associative array that holds the values for content object.
     *   - node_id, the place where to save the content.
     *
     * @param array $options An array of options.
     *
     * @return \ezContentObject
     */
    public function createNode(array $options)
    {
        $options = array_merge(
            array(), $options
        );

        // Create new ContentObject.
        $contentOptions = new \SQLIContentOptions(
            array(
                'class_identifier' => $options['class_identifier'],
            )
        );

        if (isset($options['remote_id'])) {
            $contentOptions->remote_id = $options['remote_id'];
        }

        if (isset($options['section_id'])) {
            $contentOptions->section_id = $options['section_id'];
        }

        $content = \SQLIContent::create($contentOptions);

        foreach ($options['fields'] as $property => $value) {
            $content->fields->$property = (string)$value;
        }

        // Put it under media folder.
        $content->addLocation(\SQLILocation::fromNodeID($options['node_id']));
        $publisher = \SQLIContentPublisher::getInstance();
        $publisher->publish($content);

        return $content->getRawContentObject();

    }


    /**
     * Gets the node id of a content object.
     *
     * @param \eZContentObject $contentObject A content object.
     *
     * @return integer
     */
    public function getNodeIdFromContentObject(\eZContentObject $contentObject)
    {
        $assignedNodes = $contentObject->assignedNodes();
        list($node) = $assignedNodes;
        $node_id = $node->attribute('node_id');

        return $node_id;

    }


    /**
     * Create a new empty layout object.
     *
     * The layout can be exported to xml for the fromString Method within the page datatype.
     * <?xml version="1.0"?>
     * <page>
     *   <zone_layout>1ZoneNMBLayout</zone_layout>
     *   <zone id="id_a75355ebbe36b2ed4d4adf130b2c929b">
     *     <zone_identifier>left</zone_identifier>
     *   </zone>
     * </page>
     *
     * @param string $zoneLayout
     * @param \eZINI|null $zoneINI
     *
     * @return \eZPage
     */
    public function createEmptyLayout($zoneLayout = '', \eZINI $zoneINI = null)
    {


        // Verify that we have an zone.ini instance.
        if ($zoneINI == null) {
            $zoneINI = \eZINI::instance('zone.ini');
        }

        // Create new eZPage Object for layout.
        $page = new \eZPage();
        $zones = array();
        if ($zoneLayout !== '') {
            if ($zoneINI->hasVariable($zoneLayout, 'Zones')) {
                $zones = $zoneINI->variable($zoneLayout, 'Zones');
            }

            $page->setAttribute('zone_layout', $zoneLayout);
            // Loop through the found zones and append them to page.
            foreach ($zones as $zoneIdentifier) {
                $newZone = $page->addZone(new \eZPageZone());
                $newZone->setAttribute('id', md5(mt_rand() . microtime() . $page->getZoneCount()));
                $newZone->setAttribute('zone_identifier', $zoneIdentifier);
            }
        }

        // Return the eZPage Object.
        return $page;

    }

    public function createOrUpdateNode($nodeOption)
    {
        // Search for the object by path. While the remoteID is not stable through different machines.
        $existingNode = \eZContentObjectTreeNode::fetchByURLPath($nodeOption['path']);
        // Special handling for exising objects.
        if ($existingNode instanceof \eZContentObjectTreeNode) {
            // If the node already exists get the object from it.
            $contentObject = $existingNode->object();
            // Check if the class identifier is the same.
            if ($contentObject->contentClassIdentifier() != $nodeOption['class_identifier']) {
                return;
            }
            // Check if the remoteID differ from existing.
            if ($contentObject->remoteID() != $nodeOption['remote_id']) {
                // Set the new remote id from options.
                $contentObject->setAttribute('remote_id', $nodeOption['remote_id']);
                // Store the remote ID.
                $contentObject->store(array('remote_id'));
            }
        }
        // Do create or update the nodes.
        $contentObject = $this->createNode($nodeOption);
        // Do debug output.
        $this->addSql(sprintf("SELECT 'OBJECT CREATED Path:%s';", $nodeOption['path']));

        unset($existingNode, $contentObject);
    }


    /**
     * Removing package-files and uninstall all package-items
     *
     * @param $name
     * @param boolean $info toggle notice output
     */
    protected function uninstallPackage($name, $info = false)
    {
        $removed = false;

        $script = $this->getEzScriptInstance();

        // get package
        $package = \nmbPackage::fetch($name);

        if ($package !== false) {
            $delete = $package->uninstall();
            if ($delete !== false) {
                // removing files
                $package->remove();
                $removed = true;
            } else {
                if ($info) {
                    $this->addSql(
                        sprintf("SELECT 'Error: Cannot uninstall package \"%s\"';", $name)
                    );
                }
            }
        } else {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot fetch package \"%s\"';", $name)
                );
            }
        }

        $script->shutdown();

        return $removed;
    }

    /**
     * creating and publishing users
     * users are assigned to user groups defined in group_remote_id
     *
     * @param array $user
     * @param boolean $info toggle notice output
     * @return bool|\eZContentObject
     */
    protected function createUser($user, $info = false)
    {
        // exit conditions on empty obligatory arguments
        if (!isset($user['group_remote_id']) ||
            !isset($user['remote_id']) ||
            !isset($user['attributes'])
        ) {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot create user without obligatory parameters';")
                );
            }
            return false;
        }

        // exit conditions on empty credentials
        if (!isset($user['attributes']['login']) ||
            !isset($user['attributes']['email']) ||
            !isset($user['attributes']['password'])
        ) {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot create user without credentials';")
                );
            }
            return false;
        }

        // get nodeobject by remote id of usergroup
        $parent = \eZContentObject::fetchByRemoteID($user['group_remote_id']);

        // exit condition on empty user_group
        if (!$parent) {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot fetch user-group with remote_id \"%s\" ';", $user['group_remote_id'])
                );
            }
            return false;
        }

        $params = array(
            'class_identifier' => 'user',
            'parent_node_id' => $parent->attribute('main_node_id'),
            'section_id' => $parent->attribute('section_id'),
            'name' => $user['attributes']['login']
        );
        $user = array_merge($user, $params);

        // create node object
        $userObject = $this->createTreeNode($user, $info);

        // create user object in created node
        if ($userObject) {
            $ezUser = \eZUser::create($userObject->ID);
            $ezUser->setInformation(
                $userObject->ID,
                $user['attributes']['login'],
                $user['attributes']['email'],
                $user['attributes']['password']
            );
            $ezUser->store();
        }

        return $userObject;
    }

    /**
     * @param array $roleDescription an array of role descriptors: name, permssions, groups
     * @param array $package an array describing the content class package to attach to
     * @param \ezContentObject $node an optional tree node that access to will be limited
     * @return mixed
     */
    public function createRole(array $roleDescription, array $package, \ezContentObject $node = null)
    {
        $contentClassId = $package['content_class_id'];

        // some properties may be defined as comma-separated list
        if (is_string($roleDescription['groups'])) {
            $roleDescription['groups'] = explode(',', $roleDescription['groups']);
        }
        if (is_string($roleDescription['permissions'])) {
            $roleDescription['permissions'] = explode(',', $roleDescription['permissions']);
        }

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier($contentClassId);

        // create the limitations for the actions
        $limitationsList = array(
            // we limit actions to the one content class; the array can contain multiple IDs of content classes
            'Class' => array(
                $contentClass->ID
            ),
        );

        if ($node) {
            // we need the content tree node, because we need it PATH-STRING
            $mainTreeNode = \eZContentObjectTreeNode::fetch($node->mainNodeID());

            // we limit actions to the one location; the array can contain multiple PATH-STRINGs of content tree nodes
            $limitationsList['Subtree'] = array(
                $mainTreeNode->attribute('path_string')
            );
        }

        // create the role
        $contentRole = \eZRole::create($roleDescription['name']);
        // IMPORTANT! we need to store the role before we can assign policies, otherwise we will not see any policies
        $contentRole->store();
        // add the desired policies with limitations we've just created and store AGAIN
        foreach ($roleDescription['permissions'] as $permission) {
            $contentRole->appendPolicy('content', $permission, $limitationsList);
        }
        $contentRole->store();

        $this->addSql(
            "SELECT 'Created Role \"" . $roleDescription['name'] . "\"';"
        );

        // assign the role to a user group
        foreach ($roleDescription['groups'] as $groupRemoteId) {

            // allow for readable names of groups
            if (array_key_exists($groupRemoteId, $this->groupNames)) {
                $groupRemoteId = $this->groupNames[$groupRemoteId];
            }

            $userGroupNode = \eZContentObject::fetchByRemoteID($groupRemoteId);
            $contentRole->assignToUser($userGroupNode->ID);

            // and store AGAIN (mainly to clear caches for user and role ... as a side effect)
            $contentRole->store();

            $this->addSql(
                "SELECT 'Role \"" . $roleDescription['name'] . "\" assigned to user group \"" . $groupRemoteId . "\"';"
            );
        }

        return $contentRole;
    }

    /**
     * @param array $roleDescription an array of role descriptors: name
     * @return bool
     */
    protected function removeRole(array $roleDescription)
    {
        $name = $roleDescription['name'];
        $contentRole = \eZRole::fetchByName($name);

        if ($contentRole) {
            $contentRole->remove();
            $this->addSql("SELECT 'Removed Role \"" . $name . "\"';");
            return true;
        } else {
            $this->addSql("SELECT 'Could not remove Role \"" . $name . "\" because it does not exist';");
            return false;
        }

    }

    /**
     * creating and publishing treenodes
     * parent_node_id and class_identifier only on toplevel node definition required
     * treenode configuration depends on attribute definition of specific type of contentclass.
     * example for type folder:
     *  'parent_node_id' => [int|string],
     *  'class_identifier' => [string],
     *  'remote_id' => [string],
     *  'attributes' => array(
     *      'name' => [string],
     *      'short_name' => [string],
     *      'description' => [string],
     *      'short_description' => [string]
     *  ),
     *  //optional:
     *  'child_nodes' => array(
     *      array(
     *          'remote_id' => [string],
     *          'attributes' => array( ),
     *          'child_nodes' => array( )
     *      ),
     *      ...
     *  )
     *
     * @param array $treeNode
     * @param boolean $info toggle notice output
     * @return \eZContentObject|\false
     */
    protected function createTreeNode($treeNode, $info = false)
    {
        //get tree node parameter for creation
        $param = $this->getTreeCreateParam($treeNode, $info);
        // create node object
        $obj = \eZContentFunctions::createAndPublishObject($param);
        //checking creation of object
        if ($obj) {
            // successfully created object
            if ($info) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Create %s \"%s\"';",
                        $treeNode['class_identifier'],
                        $obj->attribute('name')
                    )
                );
            }

            // check if node is available
            $exists = $this->getTreeNode($obj->attribute('main_node_id'), $info);
            if ($exists) {
                if ($info) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Success: Tree node \"%s\" is available.';",
                            $obj->attribute('name')
                        )
                    );
                }

                //check on child nodes in node definition
                if (isset($treeNode['child_nodes'])) {
                    $cId = $treeNode['class_identifier'];
                    $pId = $obj->attribute('main_node_id');
                    $sId = $obj->attribute('section_id');

                    foreach ($treeNode['child_nodes'] AS $node) {
                        // set class identifier from parent node if not set
                        if (!isset($node['class_identifier'])) {
                            $node['class_identifier'] = $cId;
                        }
                        // set new parent node id if not set in node definition
                        if (!isset($node['parent_node_id'])) {
                            $node['parent_node_id'] = $pId;
                        }
                        // set new parent node id if not set in node definition
                        if (!isset($node['section_id'])) {
                            $node['section_id'] = $sId;
                        }
                        $this->createTreeNode($node, $info);
                    }
                }
            } else {
                if ($info) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Error: Tree node \"%s\" was created but is not available.';",
                            $obj->attribute('name')
                        )
                    );
                }
                return false;
            }
        } else {
            if ($info) {
                if (!isset($treeNode['attributes']['name'])) {
                    $treeNode['attributes']['name'] = $treeNode['remote_id'];
                }
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Tree node \"%s\" of type \"%s\" could not be created.';",
                        $treeNode['attributes']['name'],
                        $treeNode['class_identifier']
                    )
                );
            }
        }

        return $obj;
    }


    /**
     * @param $remoteId
     * @param boolean $info toggle notice output
     * @return bool|array
     */
    protected function removeTreeNode($remoteId, $info = false)
    {
        $result = false;

        //get tree node parameter for deletion
        $param = $this->getTreeRemoveParam($remoteId, $info);

        if ($param) {
            //removing tree (without moving to trash)
            $result = \eZContentObjectTreeNode::removeSubtrees(array($param['main_node_id']), false);

            if ($result) {
                if ($info) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Delete %s \"%s\"';",
                            $param['class_identifier'],
                            $param['name']
                        )
                    );
                }

                // check if node was removed
                $exist = $this->getTreeNode($param['main_node_id'], $info);
                if ($exist) {
                    if ($info) {
                        $this->addSql(
                            sprintf(
                                "SELECT 'Error: Tree node \"%s\" of type \"%s\" was deleted but is still available.';",
                                $param['name'],
                                $param['class_identifier']
                            )
                        );
                    }
                    return false;
                } else {
                    if ($info) {
                        $this->addSql(
                            sprintf(
                                "SELECT 'Success: %s %s not available.';",
                                $param['class_identifier'],
                                $param['name']
                            )
                        );
                    }
                }
            } else {
                if ($info) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Error: Cannot delete %s %s';",
                            $param['class_identifier'],
                            $param['name']
                        )
                    );
                }
            }
        }

        return $result;
    }


    /**
     * @param $treeNodeId
     * @param boolean $info toggle notice output
     * @return bool|\eZContentObjectTreeNode
     */
    protected function getTreeNode($treeNodeId, $info = false)
    {
        $treeNode = false;

        if (!empty($treeNodeId)) {
            if ((int)$treeNodeId != 0) {
                $type = "node";
                $treeNode = \eZContentObjectTreeNode::fetch($treeNodeId);
            } else {
                $type = "remote";
                $parentNode = \eZContentObject::fetchByRemoteID($treeNodeId);
                $treeNodeId = $parentNode->attribute('main_node_id');
                $treeNode = \eZContentObjectTreeNode::fetch($treeNodeId);
            }
        }

        if ($info) {
            if ($treeNode) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Info: Tree node \"%s\" with given id \"%s\" found';",
                        $treeNode->attribute('name'),
                        $treeNodeId
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Info: Tree node with %s_id \"%s\" not found';",
                        $type,
                        $treeNodeId
                    )
                );
            }
        }

        return $treeNode;
    }

    /**
     * Convenience method that appends multiple tree nodes and generates verbose transcripts.
     * @param array $nodes
     * @return null
     */
    public function createTreeNodes(array $nodes) {
        foreach ($nodes as $node) {
            $this->createTreeNode($node, true);
        }
    }

    /**
     * Convenience method that appends multiple tree nodes and generates verbose transcripts.
     * @param array $nodes
     * @return null
     */
    public function removeTreeNodes(array $nodes) {
        foreach ($nodes as $node) {
            $this->removeTreeNode($node['remote_id'], true);
        }
    }

    /*protected function storeObjectRelations( \ezContentObject $obj, $treeNode )
    {
        $dataMap = $obj->dataMap();

        foreach ( $treeNode['attributes'] as $name => $content )
        {
            if(isset($dataMap[$name])) {
                $attribute = $dataMap[$name];
                $dataType = $attribute->attribute( 'data_type_string' );
                switch($dataType)
                {
                    case 'ezobjectrelationlist':
                        $relationContent = explode( ',', $content );
                        if ( count( $relationContent ) )
                        {
                            $objectIDs = array();
                            foreach( $relationContent as $refId )
                            {
                                $relContentObject = \eZContentObject::fetchByRemoteID( $refId );
                                if ( $relContentObject )
                                {
                                    $objectIDs[] = $relContentObject->ID;
                                }
                            }
                            $attribute->fromString( implode( '-', $objectIDs ) );
                        }
                        break;
                    default:
                        break;
                }
                $attribute->store();
            }
        }
    }*

    /**
     *
     * @param $treeParam
     * @param boolean $info toggle notice output
     * @return array|bool
     */
    protected function getTreeCreateParam($treeParam, $info = false)
    {
        // exit condition on empty parameters in tree node definition
        if (!isset($treeParam['parent_node_id']) ||
            !isset($treeParam['class_identifier']) ||
            !isset($treeParam['attributes'])
        ) {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot create treeNode without obligatory parameter.';")
                );
            }
            return false;
        }

        // set admin as creator if not set
        if (!isset($treeParam['creator_id'])) {
            $treeParam['creator_id'] = 14;
        }

        // set section from parent node if not set
        if (!isset($treeParam['section_id'])) {
            $tnode = $this->getTreeNode($treeParam['parent_node_id'], $info);
            if ($tnode) {
                $treeParam['parent_node_id'] = $tnode->attribute('object')->attribute('main_node_id');
                //var_dump($tnode->attribute( 'object' )->attribute( 'main_node_id' ));
                $treeParam['section_id'] = $tnode->attribute('object')->attribute('section_id');
            } else {
                if ($info) {
                    $this->addSql(
                        sprintf(
                            "SELECT 'Error: Cannot fetch section_id from parent object with id \"%s\"';",
                            $treeParam['parent_node_id']
                        )
                    );
                }
                return false;
            }
        }

        // get attribute types of contentclass
        $class = \eZContentClass::fetchByIdentifier($treeParam['class_identifier']);

        if ($class) {
            $dataMap = $class->dataMap();
        } else {
            if ($info) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Cannot fetch class with given class id \"%s\"';",
                        $treeParam['class_identifier']
                    )
                );
            }
            return false;
        }

        //rewrite attributes with type ezxmltext
        foreach ($treeParam['attributes'] AS $name => $content) {
            if (isset($dataMap[$name])) {
                /* @var \ezContentClassAttribute $attribute */
                $attribute = $dataMap[$name];
                //var_dump($attribute);
                $dataType = $attribute->attribute('data_type_string');
                switch ($dataType) {
                    case 'ezxmltext':
                        $treeParam['attributes'][$name] = $this->xmlDeclaration .
                            '<paragraph>' . $content . '</paragraph></section>';
                        break;

                    case 'ezobjectrelationlist':
                        $relationContent = explode(',', $content);
                        if (count($relationContent)) {
                            $objectIDs = array();
                            foreach ($relationContent as $refId) {
                                $relContentObject = \eZContentObject::fetchByRemoteID($refId);
                                if ($relContentObject) {
                                    $objectIDs[] = $relContentObject->ID;
                                }
                            }
                            $treeParam['attributes'][$name] = implode('-', $objectIDs);
                        }
                        break;
                        break;

                    default:
                        break;
                }
            }
        }

        // setting visibility of child nodes if not set(default true)
        if (!isset($treeParam['attributes']['show_children'])) {
            $treeParam['attributes']['show_children'] = true;
        }

        return $treeParam;
    }


    /**
     * @param string $remoteId
     * @param boolean $info toggle notice output
     * @return array|bool
     */
    protected function getTreeRemoveParam($remoteId, $info = false)
    {
        // exit condition on empty remote id
        if (empty($remoteId)) {
            if ($info) {
                $this->addSql(
                    sprintf("SELECT 'Error: Cannot remove tree node without obligatory parameter remote_id';")
                );
            }
            return false;
        }

        // fetching object by defined remote id
        $treeObj = \eZContentObject::fetchByRemoteID($remoteId);

        if (!$treeObj) {
            if ($info) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Cannot fetch treeNode with remote_id \"%s\" ';",
                        $remoteId
                    )
                );
            }
            return false;
        }

        // setting parameter used to remove tree node
        $cId = $treeObj->attribute('class_identifier');
        $nId = $treeObj->attribute('main_node_id');
        $name = $treeObj->attribute('name');

        if (!empty($cId) && !empty($nId)) {
            $rParam = array();
            $rParam['name'] = !empty($name) ? $name : "[EMPTY NAME]";
            $rParam['class_identifier'] = $cId;
            $rParam['main_node_id'] = $nId;
            return $rParam;
        } else {
            return false;
        }
    }

    /**
     * @param \ezContentObject $object
     * @param integer $stateId
     * @param boolean $info toggle notice output
     * @return array|bool
     */
    protected function setContentObjectState($object, $stateId, $info = false)
    {
        $db = \eZDB::instance();
        $db->begin();

        $db->query("INSERT INTO ezcobj_state_link (contentobject_state_id, contentobject_id)
                         VALUES($stateId, $object->ID)");

        $db->commit();

        if ($info) {
            $this->addSql(
                sprintf(
                    "SELECT 'Setting new state \"%s\" for object \"%s\"';",
                    $stateId,
                    $object->ID
                )
            );
        }
    }

    /**
     * @param \ezContentObject $object
     * @param boolean $info toggle notice output
     * @return array|bool
     */
    protected function publishObject($object, $info = false)
    {
        \eZOperationHandler::execute('content', 'publish',
            array(
                'object_id' => $object->ID,
                'version' => $object->attribute('current_version')
            )
        );

        if ($info) {
            $this->addSql(
                sprintf(
                    "SELECT 'Publishing version \"%s\" of object \"%s\"';",
                    $object->attribute('current_version'),
                    $object->ID
                )
            );
        }
    }

    /**
     * Get all obsolete entries of the contentclassattribute_id
     *
     * @return array
     */
    protected function getAllObsoleteContentClassAttributeIds()
    {
        $stmt = $this->connection->executeQuery('SELECT contentclassattribute_id  FROM ezcontentobject_attribute WHERE contentclassattribute_id NOT IN ( SELECT id FROM ezcontentclass_attribute) GROUP BY contentclassattribute_id');

        $rows = array();
        while ($row = $stmt->fetch()) {
            $rows[] = $row['contentclassattribute_id'];
        }

        $this->addSql(
            sprintf(
                "SELECT 'Found %s obsolete content class attributes!';",
                count($rows)
            )
        );

        return $rows;
    }

    /**
     * Delete all the obsolete entries of the contentclassattribute_id
     *
     * @param array $ids
     */
    protected function deleteObsoleteContentClassAttribute(array $ids)
    {
        foreach ($ids as $id) {
            $sql = sprintf(
                "DELETE FROM ezcontentobject_attribute WHERE contentclassattribute_id = %s;",
                $id
            );

            $this->connection->executeQuery($sql);
        }

        $this->addSql(
            sprintf(
                "SELECT '%s rows deleted!';",
                count($ids)
            )
        );

        $count = count($this->connection->fetchAll('SELECT contentclassattribute_id  FROM ezcontentobject_attribute WHERE contentclassattribute_id NOT IN ( SELECT id FROM ezcontentclass_attribute) GROUP BY contentclassattribute_id'));

        if ($count != 0) {
            \eZDebug::writeError('Found obsolete content class attributes. Unable to delete.');
        }
    }
}
