<?php
/**
 * Render an eZPublish contentType menue_item
 */

class MyVideoRenderMyVideoMenuItem extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * render content object
     *
     * create a simplified output for menu items
     */
    public function render()
    {
        $default = parent::render();
        $data = array();
        $data['type'] = $default['type'];

        // HINT meta data uses object.Name which is always default language ...
        // ... so we use the attribute value "title" instead
        $data['name'] = $this->getAttributeValue('title', $default['name']);

        // TODO create URL
        $this->copyFieldContent('url', $data);
        $data['ugcId'] = $default['fields']['frontend_id']['content'];
        $flyout = $this->getFlyout();
        if (!is_null($flyout)) {
            $data['flyout'] = $flyout;
        }

        return $data;
    }

    public function getFlyout()
    {
        /** @var eZContentObjectTreeNode $node */
        $node = $this->ContentObject->MainNode();
        $children = $node->Children();

        foreach ($children as $child) {
            // HINT skip if node is invisible ...
            if ($child->IsInvisible || !$this->isContentObjectOnline($child->ContentObject)) {
                continue;
            }

            if ($child->ClassIdentifier == 'myvideo_menu_flyout') {
                $renderer = MyVideoRenderFactory::getInstance($child->ContentObject);
                return $renderer->render();
            }
        }
        return null;
    }

    /**
     * loop over the parent nodes until the main menu node and render the hole menu
     * @param eZContentObjectTreeNode $node
     */
    public function handleDelete($node)
    {
        if ($node instanceof eZContentObjectTreeNode) {
            $mainMenuNode = $this->getMainMenuNode($node);

            if ($mainMenuNode !== null) {
                /** @var eZContentObject $contentObject */
                $contentObject = $mainMenuNode->object();
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
