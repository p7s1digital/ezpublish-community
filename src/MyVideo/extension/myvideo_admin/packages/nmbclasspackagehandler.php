<?php
/**
 * nmbClassPackageHandler - override class for the default ez package handler. Allows to handle contentclass import
 * without to remove the objects.
 *
 * @package
 * @author Max Keil <m.keil@all2e.com>
 * @date 29.06.12 16:50
 */
class nmbClassPackageHandler extends eZContentClassPackageHandler
{

    /*!
     Creates a new contentclass as defined in the xml structure.
    */
    public function install($package, $installType, $parameters, $name, $os, $filename, $subdirectory, $content, &$installParameters, &$installData)
    {
        $serializedNameListNode = $content->getElementsByTagName('serialized-name-list')->item(0);
        $serializedNameList = $serializedNameListNode ? $serializedNameListNode->textContent : false;
        $classNameList = new eZContentClassNameList($serializedNameList);
        if ($classNameList->isEmpty()) {
            $classNameList->initFromString($content->getElementsByTagName('name')->item(0)->textContent); // for backward compatibility( <= 3.8 )
        }
        $classNameList->validate();

        $serializedDescriptionListNode = $content->getElementsByTagName('serialized-description-list')->item(0);
        $serializedDescriptionList = $serializedDescriptionListNode ? $serializedDescriptionListNode->textContent : false;
        $classDescriptionList = new eZSerializedObjectNameList($serializedDescriptionList);

        $classIdentifier = $content->getElementsByTagName('identifier')->item(0)->textContent;
        $classRemoteID = $content->getElementsByTagName('remote-id')->item(0)->textContent;
        $classObjectNamePattern = $content->getElementsByTagName('object-name-pattern')->item(0)->textContent;
        $classURLAliasPattern = is_object($content->getElementsByTagName('url-alias-pattern')->item(0)) ?
            $content->getElementsByTagName('url-alias-pattern')->item(0)->textContent :
            null;
        $classIsContainer = $content->getAttribute('is-container');
        if ($classIsContainer !== false)
            $classIsContainer = $classIsContainer == 'true' ? 1 : 0;

        $classRemoteNode = $content->getElementsByTagName('remote')->item(0);
        $classID = $classRemoteNode->getElementsByTagName('id')->item(0)->textContent;
        $classGroupsNode = $classRemoteNode->getElementsByTagName('groups')->item(0);
        $classCreated = $classRemoteNode->getElementsByTagName('created')->item(0)->textContent;
        $classModified = $classRemoteNode->getElementsByTagName('modified')->item(0)->textContent;
        $classCreatorNode = $classRemoteNode->getElementsByTagName('creator')->item(0);
        $classModifierNode = $classRemoteNode->getElementsByTagName('modifier')->item(0);

        $classAttributesNode = $content->getElementsByTagName('attributes')->item(0);

        $dateTime = time();
        $classCreated = $dateTime;
        $classModified = $dateTime;

        $userID = false;
        if (isset($installParameters['user_id']))
            $userID = $installParameters['user_id'];

        $class = eZContentClass::fetchByRemoteID($classRemoteID);

        if ($class) {
            $className = $class->name();
            $description = ezpI18n::tr('kernel/package', "Class '%classname' already exists.", false, array('%classname' => $className));

            $choosenAction = $this->errorChoosenAction(self::ERROR_EXISTS, $installParameters, $description, $this->HandlerType);
            switch ($choosenAction) {
                case eZPackage::NON_INTERACTIVE:
                case self::ACTION_REPLACE:
                    // Create Definition array for syncing.
                    $classDefintion = array(
                        'version' => 0,
                        'serialized_name_list' => $classNameList->serializeNames(),
                        'serialized_description_list' => $classDescriptionList->serializeNames(),
                        'identifier' => $classIdentifier,
                        'remote_id' => $classRemoteID,
                        'contentobject_name' => $classObjectNamePattern,
                        'url_alias_name' => $classURLAliasPattern,
                        'is_container' => $classIsContainer,
                        'created' => $classCreated,
                        'modified' => $classModified
                    );
                    if ($content->hasAttribute('sort-field')) {
                        $classDefintion['sort_field'] = eZContentObjectTreeNode::sortFieldID($content->getAttribute('sort-field'));
                    }
                    if ($content->hasAttribute('sort-order')) {
                        $classDefintion['sort_order'] = $content->getAttribute('sort-order');
                    }

                    if ($content->hasAttribute('always-available')) {
                        $classDefintion['always_available'] = ($content->getAttribute('always-available') === 'true' ? 1 : 0);
                    }

                    // override dirty data.
                    $class->SerializedNameList = $classDefintion['serialized_name_list'];
                    $class->NameList = $classNameList;
                    $class->DescriptionList = $classDescriptionList;
                    // Merge the existing class attributes with new ones.
                    if ($this->mergeExistingClass($class, $classDefintion) == false) {
                        eZDebug::writeWarning("Unable to merge class '$className' with new attributes.");
                        return false;
                    }
                    // Merge the existing class attributes with new ones.
                    if ($this->mergeWithExisting($class, $classAttributesNode) == false) {
                        eZDebug::writeWarning("Unable to merge class '$className' with new attributes.");
                        return false;
                    }

                    eZDebug::writeNotice("Class '$className' will be merged.", __METHOD__);
                    return true;

                case self::ACTION_SKIP:
                    return true;

                case self::ACTION_NEW:
                    $class->setAttribute('remote_id', eZRemoteIdUtility::generate('class'));
                    $class->store();
                    $classNameList->appendGroupName(" (imported)");
                    break;

                default:
                    $installParameters['error'] = array(
                        'error_code' => self::ERROR_EXISTS,
                        'element_id' => $classRemoteID,
                        'description' => $description,
                        'actions' => array()
                    );
                    if ($class->isRemovable()) {
                        $errorMsg = ezpI18n::tr('kernel/package', "Replace existing class");
                        $objectsCount = eZContentObject::fetchSameClassListCount($class->attribute('id'));
                        if ($objectsCount)
                            $errorMsg .= ' ' . ezpI18n::tr('kernel/package', "(Warning! $objectsCount content object(s) and their sub-items will be removed)");
                        $installParameters['error']['actions'][self::ACTION_REPLACE] = $errorMsg;
                    }
                    $installParameters['error']['actions'][self::ACTION_SKIP] = ezpI18n::tr('kernel/package', 'Skip installing this class');
                    $installParameters['error']['actions'][self::ACTION_NEW] = ezpI18n::tr('kernel/package', 'Keep existing and create a new one');
                    return false;
            }
        }

        unset($class);

        // Try to create a unique class identifier
        $currentClassIdentifier = $classIdentifier;
        $unique = false;

        while (!$unique) {
            $classList = eZContentClass::fetchByIdentifier($currentClassIdentifier);
            if ($classList) {
                // "increment" class identifier
                if (preg_match('/^(.*)_(\d+)$/', $currentClassIdentifier, $matches))
                    $currentClassIdentifier = $matches[1] . '_' . ($matches[2] + 1);
                else
                    $currentClassIdentifier = $currentClassIdentifier . '_1';
            } else
                $unique = true;

            unset($classList);
        }

        $classIdentifier = $currentClassIdentifier;

        $values = array(
            'version' => 0,
            'serialized_name_list' => $classNameList->serializeNames(),
            'serialized_description_list' => $classDescriptionList->serializeNames(),
            'create_lang_if_not_exist' => true,
            'identifier' => $classIdentifier,
            'remote_id' => $classRemoteID,
            'contentobject_name' => $classObjectNamePattern,
            'url_alias_name' => $classURLAliasPattern,
            'is_container' => $classIsContainer,
            'created' => $classCreated,
            'modified' => $classModified
        );

        if ($content->hasAttribute('sort-field')) {
            $values['sort_field'] = eZContentObjectTreeNode::sortFieldID($content->getAttribute('sort-field'));
        } else {
            eZDebug::writeNotice('The sort field was not specified in the content class package. ' .
            'This property is exported and imported since eZ Publish 4.0.2', __METHOD__);
        }

        if ($content->hasAttribute('sort-order')) {
            $values['sort_order'] = $content->getAttribute('sort-order');
        } else {
            eZDebug::writeNotice('The sort order was not specified in the content class package. ' .
            'This property is exported and imported since eZ Publish 4.0.2', __METHOD__);
        }

        if ($content->hasAttribute('always-available')) {
            $values['always_available'] = ($content->getAttribute('always-available') === 'true' ? 1 : 0);
        } else {
            eZDebug::writeNotice('The default object availability was not specified in the content class package. ' .
            'This property is exported and imported since eZ Publish 4.0.2', __METHOD__);
        }

        // create class
        $class = eZContentClass::create($userID,
            $values);
        $class->store();

        $classID = $class->attribute('id');

        if (!isset($installData['classid_list']))
            $installData['classid_list'] = array();
        if (!isset($installData['classid_map']))
            $installData['classid_map'] = array();
        $installData['classid_list'][] = $class->attribute('id');
        $installData['classid_map'][$classID] = $class->attribute('id');

        // create class attributes
        $classAttributeList = $classAttributesNode->getElementsByTagName('attribute');
        foreach ($classAttributeList as $classAttributeNode) {
            $attributeDatatype = $classAttributeNode->getAttribute('datatype');
            $isNotSupported = strtolower($classAttributeNode->getAttribute('unsupported')) == 'true';
            if ($attributeDatatype == 'ezpage')
                $isNotSupported = false;
            if ($isNotSupported)
                continue;

            $attributeIsRequired = strtolower($classAttributeNode->getAttribute('required')) == 'true';
            $attributeIsSearchable = strtolower($classAttributeNode->getAttribute('searchable')) == 'true';
            $attributeIsInformationCollector = strtolower($classAttributeNode->getAttribute('information-collector')) == 'true';
            $attributeIsTranslatable = strtolower($classAttributeNode->getAttribute('translatable')) == 'true';
            $attributeSerializedNameListNode = $classAttributeNode->getElementsByTagName('serialized-name-list')->item(0);
            $attributeSerializedNameListContent = $attributeSerializedNameListNode ? $attributeSerializedNameListNode->textContent : false;
            $attributeSerializedNameList = new eZSerializedObjectNameList($attributeSerializedNameListContent);
            if ($attributeSerializedNameList->isEmpty())
                $attributeSerializedNameList->initFromString($classAttributeNode->getElementsByTagName('name')->item(0)->textContent); // for backward compatibility( <= 3.8 )
            $attributeSerializedNameList->validate();

            $attributeSerializedDescriptionListNode = $classAttributeNode->getElementsByTagName('serialized-description-list')->item(0);
            $attributeSerializedDescriptionListContent = $attributeSerializedDescriptionListNode ? $attributeSerializedDescriptionListNode->textContent : false;
            $attributeSerializedDescriptionList = new eZSerializedObjectNameList($attributeSerializedDescriptionListContent);

            $attributeCategoryNode = $classAttributeNode->getElementsByTagName('category')->item(0);
            $attributeCategory = $attributeCategoryNode ? $attributeCategoryNode->textContent : '';

            $attributeSerializedDataTextNode = $classAttributeNode->getElementsByTagName('serialized-description-text')->item(0);
            $attributeSerializedDataTextContent = $attributeSerializedDataTextNode ? $attributeSerializedDataTextNode->textContent : false;
            $attributeSerializedDataText = new eZSerializedObjectNameList($attributeSerializedDataTextContent);

            $attributeIdentifier = $classAttributeNode->getElementsByTagName('identifier')->item(0)->textContent;
            $attributePlacement = $classAttributeNode->getElementsByTagName('placement')->item(0)->textContent;
            $attributeDatatypeParameterNode = $classAttributeNode->getElementsByTagName('datatype-parameters')->item(0);

            $classAttribute = $class->fetchAttributeByIdentifier($attributeIdentifier);
            if (!$classAttribute) {
                $classAttribute = eZContentClassAttribute::create($class->attribute('id'),
                    $attributeDatatype,
                    array(
                        'version' => 0,
                        'identifier' => $attributeIdentifier,
                        'serialized_name_list' => $attributeSerializedNameList->serializeNames(),
                        'serialized_description_list' => $attributeSerializedDescriptionList->serializeNames(),
                        'category' => $attributeCategory,
                        'serialized_data_text' => $attributeSerializedDataText->serializeNames(),
                        'is_required' => $attributeIsRequired,
                        'is_searchable' => $attributeIsSearchable,
                        'is_information_collector' => $attributeIsInformationCollector,
                        'can_translate' => $attributeIsTranslatable,
                        'placement' => $attributePlacement
                    ));

                $dataType = $classAttribute->dataType();
                $classAttribute->store();
                $dataType->unserializeContentClassAttribute($classAttribute, $classAttributeNode, $attributeDatatypeParameterNode);
                $classAttribute->sync();
            }
        }

        // add class to a class group
        $classGroupsList = $classGroupsNode->getElementsByTagName('group');
        foreach ($classGroupsList as $classGroupNode) {
            $classGroupName = $classGroupNode->getAttribute('name');
            $classGroup = eZContentClassGroup::fetchByName($classGroupName);
            if (!$classGroup) {
                $classGroup = eZContentClassGroup::create();
                $classGroup->setAttribute('name', $classGroupName);
                $classGroup->store();
            }
            $classGroup->appendClass($class);
        }
        return true;
    }


    /**
     * Merges the existing class-attributes with the imported xml content.
     * The diff between the attributes is searched by identifer.
     * Processing types:
     * - attribute_was_found : we replace the name and other data.
     * - attribute_was_not_found : we create a new one.
     *
     * In the aftercheck we loop through the existing attributes and check if they was commited.
     *
     * @param eZContentClass $class
     * @param \DOMElement $classAttributesNode
     *
     * @return void
     */
    public function mergeWithExisting(eZContentClass $class, DOMElement $classAttributesNode)
    {
        // Merged attribute container.
        $syncAttributesIdentifierList = array();

        // Loop through the XML-Nodes.
        $classAttributeList = $classAttributesNode->getElementsByTagName('attribute');
        foreach ($classAttributeList as $classAttributeNode) {
            $attributeDatatype = $classAttributeNode->getAttribute('datatype');
            // If the attribute does not support serialization and imports. Skip it.
            $isNotSupported = strtolower($classAttributeNode->getAttribute('unsupported')) == 'true';
            if ($attributeDatatype == 'ezpage')
                $isNotSupported = false;
            if ($isNotSupported) {
                continue;
            }
            // Get all informations about the attribute.
            $attributeIsRequired = strtolower($classAttributeNode->getAttribute('required')) == 'true';
            $attributeIsSearchable = strtolower($classAttributeNode->getAttribute('searchable')) == 'true';
            $attributeIsInformationCollector = strtolower($classAttributeNode->getAttribute('information-collector')) == 'true';
            $attributeIsTranslatable = strtolower($classAttributeNode->getAttribute('translatable')) == 'true';
            $attributeSerializedNameListNode = $classAttributeNode->getElementsByTagName('serialized-name-list')->item(0);
            $attributeSerializedNameListContent = $attributeSerializedNameListNode ? $attributeSerializedNameListNode->textContent : false;
            $attributeSerializedNameList = new eZSerializedObjectNameList($attributeSerializedNameListContent);
            if ($attributeSerializedNameList->isEmpty())
                $attributeSerializedNameList->initFromString($classAttributeNode->getElementsByTagName('name')->item(0)->textContent); // for backward compatibility( <= 3.8 )
            $attributeSerializedNameList->validate();

            $attributeSerializedDescriptionListNode = $classAttributeNode->getElementsByTagName('serialized-description-list')->item(0);
            $attributeSerializedDescriptionListContent = $attributeSerializedDescriptionListNode ? $attributeSerializedDescriptionListNode->textContent : false;
            $attributeSerializedDescriptionList = new eZSerializedObjectNameList($attributeSerializedDescriptionListContent);

            $attributeCategoryNode = $classAttributeNode->getElementsByTagName('category')->item(0);
            $attributeCategory = $attributeCategoryNode ? $attributeCategoryNode->textContent : '';

            $attributeSerializedDataTextNode = $classAttributeNode->getElementsByTagName('serialized-description-text')->item(0);
            $attributeSerializedDataTextContent = $attributeSerializedDataTextNode ? $attributeSerializedDataTextNode->textContent : false;
            $attributeSerializedDataText = new eZSerializedObjectNameList($attributeSerializedDataTextContent);

            $attributeIdentifier = $classAttributeNode->getElementsByTagName('identifier')->item(0)->textContent;
            $attributePlacement = $classAttributeNode->getElementsByTagName('placement')->item(0)->textContent;
            $attributeDatatypeParameterNode = $classAttributeNode->getElementsByTagName('datatype-parameters')->item(0);

            // Collect all needed Parameters in an array.
            $attributeParameters = array(
                'version' => 0,
                'identifier' => $attributeIdentifier,
                'serialized_name_list' => $attributeSerializedNameList->serializeNames(),
                'serialized_description_list' => $attributeSerializedDescriptionList->serializeNames(),
                'category' => $attributeCategory,
                'serialized_data_text' => $attributeSerializedDataText->serializeNames(),
                'is_required' => $attributeIsRequired,
                'is_searchable' => $attributeIsSearchable,
                'is_information_collector' => $attributeIsInformationCollector,
                'can_translate' => $attributeIsTranslatable,
                'placement' => $attributePlacement
            );

            // Search for the attribute in existing class.
            $classAttribute = $class->fetchAttributeByIdentifier($attributeIdentifier);
            // Compare the types between the imported and existing datatype.
            if ($classAttribute instanceof eZContentClassAttribute && $attributeDatatype != $classAttribute->attribute('data_type_string')) {
                // Remove current attribute if not the same type.
                $classAttribute->removeThis();
                // Unset the class attribute.
                $classAttribute = null;
            }

            // If the attribute was found, we override the params.
            if ($classAttribute instanceof eZContentClassAttribute) {
                // Predefine DirtyData.
                $classAttribute->NameList = $attributeSerializedNameList;
                $classAttribute->DescriptionList = $attributeSerializedDescriptionList;
                $classAttribute->DataTextI18nList = $attributeSerializedDataText;

                foreach ($attributeParameters as $key => $value) {
                    $classAttribute->setAttribute($key, $value);
                }
                $dataType = $classAttribute->dataType();
                if ($dataType)
                    @$dataType->unserializeContentClassAttribute($classAttribute, $classAttributeNode, $attributeDatatypeParameterNode);
                $classAttribute->sync();
                eZDebug::writeNotice("*Attribute $attributeIdentifier in class " . $class->attribute('identifier') . ' was merged.', __METHOD__);
            } // Create new class-attribute if nothing was found.
            else {
                $classAttribute = eZContentClassAttribute::create($class->attribute('id'), $attributeDatatype, $attributeParameters);
                $dataType = $classAttribute->dataType();
                $classAttribute->store();
                if (!$dataType) {
                    continue;
                }
                $dataType->unserializeContentClassAttribute($classAttribute, $classAttributeNode, $attributeDatatypeParameterNode);
                $classAttribute->sync();
                $classAttribute->initializeObjectAttributes();
                eZDebug::writeNotice("+Attribute $attributeIdentifier in class " . $class->attribute('identifier') . ' created.', __METHOD__);
            }
            $syncAttributesIdentifierList[] = $attributeIdentifier;
        }

        // Store the class with new attributes.
        $class->store();

        // Get now the existing attributes in class.
        $existingClassAttributes = $class->fetchAttributes();

        // Loop throug the existing attributes and check if availabled in new xml.
        foreach ($existingClassAttributes as $classAttribute) {
            // Skip processing if error in class-definition was found.
            if (!$classAttribute instanceof eZContentClassAttribute) {
                eZDebug::writeWarning("Fetched attribute not instanceof eZContentClassAttribute.", __METHOD__);
                continue;
            }
            // If the current attribute was not synced - remove it.
            $attributeIdentifier = $classAttribute->attribute('identifier');
            if (!in_array($attributeIdentifier, $syncAttributesIdentifierList)) {
                eZDebug::writeNotice("-Attribute $attributeIdentifier in class " . $class->attribute('identifier') . ' removed.', __METHOD__);
                $classAttribute->removeThis();
            }
        }

        return true;

    }


    /**
     * Merge the existing class with new values.
     *
     * @param eZContentClass $class
     * @param                $values
     *
     * @return bool
     */
    public function mergeExistingClass(eZContentClass $class, $values)
    {
        foreach ($values as $key => $value) {
            $class->setAttribute($key, $value);
        }
        $class->store();
        eZDebug::writeNotice('Class ' . $class->attribute('identifier') . ' updated.');

        return true;
    }
}
