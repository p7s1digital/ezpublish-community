<?php
/**
 * Description of MyVideoRenderFieldEzselection
 *
 */
class MyVideoRenderFieldEzselection extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        $att_id = $att->ContentClassAttributeID;

        // data lives in the content object ...

        $selected = $att->content();
        
        // but the options lives in the content class ...

        $class = eZContentClassAttribute::fetch($att_id);
        $arr = $class->content();
        $options = $arr['options'];
        
        // initialize result array ...
        $data = array(
                        'keys' => array(),
                        'values' => array(),
                        'selected_keys' => $selected,
                        'selected_values' => array()
        );
        
        // prepare result array ...
        foreach ($options as $v) {
            $data['keys'][] = $v['id'];
            $data['values'][] = $v['name'];
            if (in_array($v['id'], (array) $selected)) {
                $data['selected_values'][] = $v['name'];
                
            }
        }
        
        return $data;
    }
}
