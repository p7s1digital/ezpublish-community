<?php
/**
 * Description of MyVideoRenderFieldEzenum
 *
 * TODO: DEPRECATED
 */
class MyVideoRenderFieldEzenum extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();

        $enumObj = $att->content();
        $enumobjectList = $enumObj->attribute('enumobject_list');
        if (!empty($enumobjectList)) {
            $selectedEnumValue = $enumobjectList[0]->EnumValue;
            return $selectedEnumValue;
        } else {
            return null;
        }
    }
}
