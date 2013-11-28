<?php
/**
 * File containing MyVideoPublishTestSuite class
 *
 */

class MyVideoPublishTestSuite extends ezpDatabaseTestSuite
{
    protected $insertDefaultData = false;
    protected $sqlFiles = array('extension/myvideo_publish/tests/eztest3.sql');
    
    public function __construct()
    {
        parent::__construct();
        $this->setName("MyVideo Publish Test Suite");
        $this->addTestSuite("MyVideoUtilsTest");
    }

    public static function suite()
    {
        return new self();
    }
}