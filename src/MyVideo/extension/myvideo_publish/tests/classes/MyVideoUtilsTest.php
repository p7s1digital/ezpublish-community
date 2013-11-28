<?php
/**
 * Class MyVideoUtilsTest
 */
class MyVideoUtilsTest extends ezpTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->setName("MyVideoUtility test");
    }

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @group myvideo
     */
    public function testFromCamelCase()
    {
        $this->assertSame('alpha_beta_gamma', MyVideoUtils::fromCamelCase('alphaBetaGamma'));
        $this->assertSame('alpha_beta_gamma', MyVideoUtils::fromCamelCase('AlphaBetaGamma'));
    }


    /**
     * @group myvideo
     */
    public function testToCamelCase()
    {
        $this->assertSame('alphaBetaGamma', MyVideoUtils::toCamelCase('alpha_beta_gamma'));
        $this->assertSame('AlphaBetaGamma', MyVideoUtils::toCamelCase('alpha_beta_gamma', true));

        $this->assertSame('alphaBetaGamma', MyVideoUtils::toCamelCase('ALPHA_BETA_GAMMA'));
        $this->assertSame('AlphaBetaGamma', MyVideoUtils::toCamelCase('ALPHA_BETA_GAMMA', true));
    }
}
