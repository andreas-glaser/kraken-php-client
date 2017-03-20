<?php

namespace AndreasGlaser\KPC\Tests;

use AndreasGlaser\Helpers\ValueHelper;
use AndreasGlaser\KPC\KPC;

/**
 * Class KPCTest
 *
 * @package AndreasGlaser\KPC\Tests
 * @author  Andreas Glaser
 */
class KPCTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KPC
     */
    protected $kpc;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->kpc = new KPC();
    }

    /**
     * @author Andreas Glaser
     */
    public function testConstructor()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Both "apiKey" and "apiSecret" have to be provided');

        new KPC(3);
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetTime()
    {
        $result = $this->kpc->getTime();

        $this->assertTrue(is_array($result->decoded));

        $this->assertArrayHasKey('error', $result->decoded);
        $this->assertArrayHasKey('result', $result->decoded);
        $this->assertArrayHasKey('unixtime', $result->decoded['result']);
        $this->assertArrayHasKey('rfc1123', $result->decoded['result']);

        $this->assertEmpty($result->decoded['error']);
        $this->assertTrue(ValueHelper::isInteger($result->decoded['result']['unixtime']));
        $this->assertTrue(is_string($result->decoded['result']['rfc1123']));
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetAssets()
    {
        $result = $this->kpc->getAssets();

        $this->assertTrue(is_array($result->decoded));
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetAssetPairs()
    {
        $result = $this->kpc->getAssetPairs('fees', ['XBTUSD']);

        $this->assertTrue(is_array($result->decoded));
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetTicker()
    {
        $result = $this->kpc->getTicker(['XBTUSD', 'ETHXBT']);

        $this->assertTrue(is_array($result->decoded));
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetOHLC()
    {
        $result = $this->kpc->getOHLC('XBTUSD', 10080);

        $this->assertTrue(is_array($result->decoded));
    }

    /**
     * @author Andreas Glaser
     */
    public function testGetOrderBook()
    {
        $result = $this->kpc->getOrderBook('XBTUSD', 5);

        $this->assertTrue(is_array($result->decoded));
    }

}