<?php
/**
 * Description of MyVideoRenderFieldEzpage
 *
 */
class MyVideoRenderFieldEzpage extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    /**
     * fetch the datamap of the current contentobject and get the zoneinfo xml
     * return an initialized eZPage object with the current zoneinfo
     * 
     * @return eZPage
     */
    public function getEZPage($att)
    {
        $xml = $att->DataText;
        
        $page = eZPage::createFromXML($xml);
        return $page;
    }
    
    /**
     * get the name of the used template
     * 
     * @return string
     */
    public function getZoneLayout()
    {
        $page = $this->getEZPage();
        
        $zoneLayout = $page->attribute('zone_layout');
        return $zoneLayout;
    }
    
    public function renderPage($ezpage)
    {
        $stack = array();
        $stack['layout'] = $ezpage->attribute('zone_layout');
        $zoneCount = $ezpage->getZoneCount();
        
        for ($i=0; $i < $zoneCount; $i++) {
            $zone = $ezpage->getZone($i);
            $tmp = array('type' => 'zone','zone_identifier' => $zone->attribute('zone_identifier'));
            $blocks = $this->renderZone($zone);
            $tmp['blocks'] = $blocks;
            $stack['zones'][] = $tmp;
        }
        return $stack;
    }
    
    public function renderZone($zone)
    {
        $stack = array();
        $blockCount = $zone->getBlockCount();
        for ($i=0; $i < $blockCount; $i++) {
            $block = $zone->getBlock($i);
            $stack[] = $this->renderBlock($block);
        }
        return $stack;
    }

    
    public function renderBlock($block)
    {
        $data = array();
        $data['name'] = $block->getName();
        $data['type'] = $block->attribute('type');
        
        // TODO fix this
        $data['type'] = preg_replace('#^MyVideo#', '', $data['type']);
        
        
        
        
        if ($block->hasAttribute('custom_attributes')) {
            $data['custom_attributes'] = $block->attribute('custom_attributes');
        }
        $nodes = $block->getValidItemsAsNodes();


        $stack = array();
        foreach ($nodes as $item) {
            $stack[] = $this->renderItem($item);
        }
        $data['items'] = $stack;
 
        return $data;
    }
    
    public function renderItem($item)
    {
        $renderer = MyVideoRenderFactory::getInstance($item->ContentObject);

        $data = $renderer->render();
        mxdLog::write('DEBUG'.serialize($data));
        
        return $data;
        
        
        $data = array();
        
        $data['name'] = $item->Name;
        $data['type'] = $item->ClassIdentifier;
        $datamap = $item->ContentObject->fetchDataMap();
        $body_renderer = MyVideoFieldRendererFactory::getInstance($datamap['body']);
        
        $data['title'] = $datamap['title']->attribute('data_text');
        $data['body'] = $body_renderer->render();
        
        return $data;
    }
    
    
    public function render()
    {
        $stack = array();
        $att = $this->getContentObjectAttribute();
        $ezpage = $this->getEZPage($att);

        $stack = $this->renderPage($ezpage);
        return $stack;

    }
}
