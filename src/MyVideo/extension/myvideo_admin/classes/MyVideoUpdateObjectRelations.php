<?php
/**
 * Description of MyVideoUpdateObjectRelations
 *
 * Add Object-Ids (myvideo_video) to attribute "relation_videos"
 *
 * HINT
 * - despite its name this class handles just one special use case
 */
class MyVideoUpdateObjectRelations
{
    private $targetContentObjectId = null;
    private $targetContentObjectVersion = null;

    /**
     * specify which object and version to alter
     * @param null $contentObjectId
     * @param null $contentObjectVersion
     */
    public function __construct($contentObjectId = null, $contentObjectVersion = null)
    {
        if (!is_null($contentObjectId)) {
            $this->targetContentObjectId = $contentObjectId;
        }

        if (!is_null($contentObjectVersion)) {
            $this->targetContentObjectVersion = $contentObjectVersion;
        }
    }

    /**
     * add content object id of myvideo_video objects
     * @param array $ids
     */
    public function addContentObjectIds($ids = array())
    {
        $version = $this->fetchEditVersion();

        $contentObjectAttributes = $version->contentObjectAttributes();

        // Loop all attributes of the object's class because
        // in eZ you can not fetch the attribute by its identifier
        foreach (array_keys($contentObjectAttributes) as $key) {
            // Identify each attribute name
            $contentObjectAttribute = $contentObjectAttributes[$key];
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $attributeIdentifier = $contentClassAttribute->attribute("identifier");

            if ($attributeIdentifier == 'relation_videos') {
                $content = $contentObjectAttribute->content();
                $stack = array();
                $alreadyStoredIds = array();
                foreach ($content['relation_list'] as $relation):
                    // keep current
                    $stack[] = $relation;
                    $alreadyStoredIds[] = $relation['contentobject_id'];
                endforeach;

                // add new ids
                $n = count($stack) + 1;
                foreach ($ids as $id) {
                    // skip this id if object is already in relation list ...
                    if (in_array($id, $alreadyStoredIds)) {
                        continue;
                    }
                    $priority = $n++;
                    $eZObjectRelationListType = new eZObjectRelationListType();
                    $stack[] = $eZObjectRelationListType->appendObject($id, $priority, $contentObjectAttribute);
                }

                $content['relation_list'] = $stack;
                $contentObjectAttribute->setContent($content);
                $contentObjectAttribute->store();
            }
        }
        $version->store();
    }

    /**
     * fetch the version of the ezContentObject
     * @return eZContentObjectVersion
     */
    private function fetchEditVersion()
    {
        // fetch object by its ID ...
        $contentObject = eZContentObject::fetch($this->targetContentObjectId);

        // get version of the object ...
        $version = $contentObject->version($this->targetContentObjectVersion);

        return $version;
    }
}
