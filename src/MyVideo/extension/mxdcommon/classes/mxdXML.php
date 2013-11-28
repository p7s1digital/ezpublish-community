<?php

class mxdXML
{

    /**
     * the DOMDocument
     * @var DOMDocument
     */
    private $_doc;

    /**
     * XPATH
     * @var DOMXPath
     */
    private $_xPath;

    /**
     * constructor
     * initialize a DOMDocument
     *
     * @param string $xmlString
     * @param string $encoding
     */
    public function __construct($xmlString, $encoding = 'utf-8')
    {
        $this->_doc = new DOMDocument($version, $encoding);
        $this->_doc->loadXML($xmlString);
    }

    /**
     * create a DOMXPath object to query the content
     *
     * @return DOMXPath
     */
    protected function getXPath()
    {
        if (false === isset($this->_xPath)) {
            $this->_xPath = new DOMXPath($this->_doc);
        }
        return $this->_xPath;
    }

    /**
     * Registers the namespaceURI and prefix with the DOMXPath object.
     * just a wrapper for the DOMXPath
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @return boolean
     */
    public function registerNamespace($prefix, $namespaceURI)
    {
        $success = $this->getXPath()->registerNamespace($prefix, $namespaceURI);
        return $success;
    }

    /**
     * Executes the given XPath expression
     * just a wrapper for the DOMXPath
     *
     * @param string $expression
     * @param DOMNode $contextNode
     * @return mixed
     */
    public function query($expression, DOMNode $contextNode = null)
    {
        $result = $this->getXPath()->query($expression, $contextNode);
        return $result;
    }

}
