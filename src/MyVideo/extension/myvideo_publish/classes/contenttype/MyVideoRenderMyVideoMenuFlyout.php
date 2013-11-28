<?php
/**
 * Description of MyVideoRenderMyVideoMenuFlyout
 *
 */
class MyVideoRenderMyVideoMenuFlyout extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * render content object
     */
    public function render()
    {
        $data = array();
        $data['type'] = 'image';
        $this->copyFieldContent('url', $data);
        $this->copyFieldContent('image', $data, false, array('src', 'width', 'height', 'title'));

        return $data;
    }

    /**
     * loop over the parent nodes until the main menu node and render the hole menu
     */
    public function handleDelete($node)
    {
        if ($node instanceof eZContentObjectTreeNode) {
            $mainMenuNode = $this->getMainMenuNode($node);

            if ($mainMenuNode !== null) {
                $contentObject = $mainMenuNode->ContentObject;
                MyVideoRenderer::render($contentObject);
            }
        }
    }

    /**
     * Iterate through the parent nodes to find the main menu node and return it
     *
     * @param eZContentObjectTreeNode $node
     * @return eZContentObjectTreeNode
     */
    private function getMainMenuNode(eZContentObjectTreeNode $node = null)
    {
        if ($node === null) {
            return null;
        }

        if ($node->ClassIdentifier === 'myvideo_menu') {
            return $node;
        }

        $parentNode = eZContentObjectTreeNode::fetch($node->ParentNodeID);
        return $this->getMainMenuNode($parentNode);
    }
}
