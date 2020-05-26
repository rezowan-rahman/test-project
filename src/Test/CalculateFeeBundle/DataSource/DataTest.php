<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/23/20
 * Time: 9:56 PM
 */

namespace Test\CalculateFeeBundle\DataSource;

use CalculateFeeBundle\DataSource\Data;
use JsonSchema\Exception\InvalidSchemaException;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $dataClass;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->dataClass = new Data();

    }

    public function testSetRateUrl()
   {
       $exampleUrl = "http://example.com";

       $this->dataClass->setRateUrl($exampleUrl);
       $this->assertIsString($this->dataClass->getRateUrl());
       $this->assertEquals($exampleUrl, $this->dataClass->getRateUrl());
   }

    public function testSetBinUrl()
    {
        $exampleUrl = "http://example.com/bin";
        $binValue = '1234';
        $data = new Data();

        $this->dataClass->setBinUrl($exampleUrl);
        $this->assertIsString($this->dataClass->getBinUrl($binValue));
        $this->assertEquals($exampleUrl.'/'.$binValue, $this->dataClass->getBinUrl($binValue));
    }

    public function testGetBinData()
    {
        $bin = "45417360";

        $result = $this->dataClass->getBinData($bin);
        $this->assertEquals('JP', $result);
    }

    public function testGetRateData()
    {
        $result = $this->dataClass->getRateData('EUR');
        $this->assertEquals(0, $result);
    }

    public function testGetRateDataWithNonZero()
    {
        $result = $this->dataClass->getRateData('JPY');
        $this->assertIsFloat($result);
    }


}