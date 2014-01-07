<?php

class MyVideobulkuploadertype extends eZDataType
{

    const myvideobulkuploader = "myvideobulkuploader";

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eZDataType(
            self::myvideobulkuploader,
            "MyVideo Bulk Uploader",
            array(
                'serialize_supported' => true
            )
        );
    }

    /**
     * Validates input on content object level \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if the values are accepted or not
     *
     * @param $http
     * @param $base
     * @param $contentObjectAttribute
     * @return int
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches all variables from the object \return true if fetching of class attributes are successfull, false if not
     *
     * @param $http
     * @param $base
     * @param $contentObjectAttribute
     * @return bool|void
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return true;
    }

    /**
     * Returns the content.
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        return "";
    }

    /**
     * Returns the meta data used for storing search indeces.
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function metaData( $contentObjectAttribute )
    {
        return "";
    }

    /**
     * Returns the value as it will be shown if this attribute is used in the object name pattern.
     *
     * @param $contentObjectAttribute
     * @param null $name
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return "";
    }

    /**
     * return true if the datatype can be indexed
     *
     * @return bool
     */
    public function isIndexable()
    {
        return true;
    }

}

eZDataType::register( MyVideobulkuploadertype::myvideobulkuploader, "myvideobulkuploadertype" );
