<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:39 PM
 */

namespace Test\CalculateFeeBundle\Common;

use PHPUnit\Framework\TestCase;

use CalculateFeeBundle\Common\Main;

class TestMain extends TestCase
{

    private $main;

    private $rowNumber = NULL;

    public function init()
    {
        if($this->main instanceof Main) return $this->main;
        $this->main = new Main($this->getInputFile());
        return $this->main;
    }

    public function getInputFile()
    {
        return file_get_contents(__DIR__.'/../../../../input.txt');
    }

    private function getRow()
    {
        if($this->rowNumber == NULL) $this->rowNumber = rand(0,4);
        return $this->rowNumber;
    }

    public function testExtractDataFromRow()
    {
        $row = explode("\n", $this->getInputFile());
        $value = $this->init()->extractDataFromRow($row[$this->getRow()]);

        $this->assertArrayHasKey('bin', $value);
        $this->assertArrayHasKey('amount', $value);
        $this->assertArrayHasKey('currency', $value);
    }

    public function testCalculate()
    {
        $row = explode("\n", $this->getInputFile());
        $value = $this->init()->extractDataFromRow($row[$this->getRow()]);

        $this->assertIsNumeric($value['bin']);
        $this->assertIsNumeric($value['amount']);
        $this->assertIsString($value['currency']);

        $result = $this->init()->calculate($value['bin'], floatval($value['amount']), $value['currency']);

        $this->assertIsFloat($result);
    }

    public function testGetRatet()
    {
        $row = explode("\n", $this->getInputFile());
        $value = $this->init()->extractDataFromRow($row[$this->getRow()]);

        $result = $this->init()->getRate($value['currency']);
        $this->assertIsFloat($result);
    }

    public function testExchageRateUrl()
    {
        $result = file_get_contents(Main::$exchangeRateUrl);
        $result = json_decode($result, true);
        $this->assertArrayHasKey('rates', $result);
    }

    public function testBinListUrl()
    {
        $row = explode("\n", $this->getInputFile());
        $value = $this->init()->extractDataFromRow($row[$this->getRow()]);

        $result = file_get_contents(Main::$binListUrl."/{$value['bin']}");
        $result = json_decode($result, true);

        $this->assertArrayHasKey("country", $result);
        $this->assertArrayHasKey("alpha2", $result['country']);
    }

    public function testIsEu()
    {
        $row = explode("\n", $this->getInputFile());
        $value = $this->init()->extractDataFromRow($row[$this->getRow()]);

        $result = $this->init()->isEu($value['bin']);
        $this->assertIsBool($result);

    }
}