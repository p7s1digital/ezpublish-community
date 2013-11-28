<?php

class mxdXMLTransform
{

    /**
     * DOMDocument with the xml
     * @var DOMDocument
     */
    private $docXML;
    private $docXSL;

    /**
     * constructor
     *
     */
    public function __construct()
    {
    }

    public static function getUrlFromId($id)
    {
        $href = '#';
        if ($id) {
            $eZUrl = eZURL::fetch($id);
            if (is_object($eZUrl)) {
                $href = $eZUrl->attribute('url');
            }
        }

        return $href;
    }

    /**
     * set the xml string to transform
     *
     * @param string $xmlString
     * @return void
     */
    public function setXMLString($xmlString)
    {
        $this->docXML = new DOMDocument();
        // TODO improve this handle empty or invalid xmlStrings
        $success = @$this->docXML->loadXML($xmlString);
        return $success;
    }

    /**
     * transform the xml to bbcode using the given xslFile
     *
     * @param string $xslFile
     */
    public function transform($xslFile)
    {
        $this->setXSLFile($xslFile);

        // Configure the transformer
        $proc = new XSLTProcessor;
        $proc->registerPHPFunctions();
        $proc->importStyleSheet($this->docXSL);

        // transform $xml according to the stylesheet $xsl
        $result = $proc->transformToXML($this->docXML); // transform the data

        $result = strip_tags($result); // remove xml header
        return $result;
    }

    /**
     * the the filename woith the XSL used to transform xml
     *
     * @param string $filename
     */
    public function setXSLFile($filename)
    {
        if (false === file_exists($filename)) {
            throw new Exception(sprintf('Missing XSL File "%s" to transform XML To BBCode', $filename));
        }

        $this->docXSL = new DOMDocument();
        $this->docXSL->load($filename);
    }

    /**
     * get the domdoument with the xml to transform
     *
     * @throws RuntimeException
     * @return DOMDocument
     */
    private function getXML()
    {
        if (!isset($this->docXML)) {
            throw new RuntimeException('No XML specified, use method setXMLString');
        }

        return $this->docXML;
    }

}
