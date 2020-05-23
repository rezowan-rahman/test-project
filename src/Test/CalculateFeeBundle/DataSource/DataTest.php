<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/23/20
 * Time: 9:56 PM
 */

namespace Test\CalculateFeeBundle\DataSource;

use CalculateFeeBundle\DataSource\Data;
use PHPUnit\Framework\TestCase;
use Test\CalculateFeeBundle\Common\MainTest;

class DataTest extends TestCase
{
    public function testGetEuData()
    {
        $result = Data::getEuData();
        $this->assertIsArray($result);
    }

    public function testGetBinDetails()
    {
        $value = MainTest::init()->extractDataFromRow(MainTest::getRow());

        $result = Data::getBinDetails($value['bin']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('country', $result);
        $this->assertArrayHasKey('alpha2', $result['country']);
    }

    public function testGetExchageRate()
    {
        $result = Data::getExchageRate();
        $this->assertIsArray($result);
        $this->assertArrayHasKey("rates", $result);
    }

    public function testGetDividant()
    {
        $value = MainTest::init()->extractDataFromRow(MainTest::getRow());
        $result = Data::getDividant($value['bin']);
        $this->assertIsFloat($result);
    }
}