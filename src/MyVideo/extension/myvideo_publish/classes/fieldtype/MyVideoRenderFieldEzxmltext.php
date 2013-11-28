<?php
/**
 * Description of MyVideoRenderFieldEzxmltext
 *
 */
class MyVideoRenderFieldEzxmltext extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        $xml = $att->attribute('data_text');
        $bbcode = $this->xmlToBBCode($xml);

        return $bbcode;
    }

    /**
     * transform the given xml text to bbcode
     *
     * @param string $xmlString
     * @return string
     */
    private function xmlToBBCode($xmlString)
    {
        $path = $this->getXSLDir();
        $xslFile = $path . 'bbcode.xsl';
        $transformator = new mxdXMLTransform();
        $transformator->setXMLString($xmlString);
        $bbCode = $transformator->transform($xslFile);
        $bbCode = trim($bbCode);

        return $bbCode;
    }

    /**
     * return the path to the directory with xsl files
     *
     * @return string
     */
    private function getXSLDir()
    {
        $rootDir = eZSys::rootDir();
        $extensionDir = eZExtension::baseDirectory();
        $extensionName = 'myvideo_publish';
        $fullFilename = $rootDir . '/' . $extensionDir . '/' . $extensionName . '/xsl/';

        return $fullFilename;
    }
}
