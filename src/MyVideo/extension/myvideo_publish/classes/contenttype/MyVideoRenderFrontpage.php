<?php
/**
 * Render an eZPublish contentType frontpage 
 */

class MyVideoRenderFrontpage extends MyVideoRendererBase implements MyVideoRendererInterface
{
    
    public function __construct($contentObject)
    {
        parent::__construct($contentObject);
        mxdLog::write('CALLED '.__METHOD__);
    }
    /**
     * get all treenodes that point to the given contentObjectId
     *
     * @param integer $contentObjectId
     * @return array <eZContentObjectTreeNode>
     */
    private function getTreeNodesByContentObjectId($contentObjectId)
    {
        $db = eZDB::instance();
        $contentObjectId = intval($contentObjectId);
        $query = "Select node_id
                  from ezm_block
                  Where id IN
                          (Select block_id
                           from ezm_pool
                           where object_id = $contentObjectId
                          )";

        $results = $db->arrayQuery($query);

        if (empty($results)) {
            return false;
        }

        foreach ($results as $res) {
            $nodes[] = eZContentObjectTreeNode::fetch($res['node_id']);
        }

        return $nodes;
    }

    /**
     * cycle through the treeNode array and init
     * the a new Frontpage class for each
     *
     * @param array $treeNodes
     * @return array <Frontpage>
     */
    private function getFrontpages(array $treeNodes)
    {
        $frontpages = array();
        foreach ($treeNodes as $treeNode) {
            $frontpage = new mxdFrontpage();
            $found = $frontpage->fetchByContentObjectTreeNode($treeNode);

            if (true === $found) {
                $frontpages[] = $frontpage;
            }
        }

        return $frontpages;
    }
    
    public function render()
    {
        $data = parent::render();
        // HINT, create a simplified version
        // TODO, find a better solution for this hack
        $tmp = array();
        $tmp['type'] = $data['type'];
        $tmp = array_merge($tmp, $data['fields']['page']['content']);

        $data = $tmp;
        // mxdLog::write(json_encode($data));
        return $data;
    }
}
