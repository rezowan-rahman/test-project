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

    private $row = NULL;

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
        $rows = explode("\n", $this->getInputFile());
        $max = count($rows);

        if($this->row == NULL) $this->row = $rows[rand(0,$max-1)];
        return $this->row;
    }

    public function testExtractDataFromRow()
    {
        $rows = explode("\n", $this->getInputFile());

        foreach($rows as $row) {

            $value = $this->init()->extractDataFromRow($row);

            $this->assertArrayHasKey('bin', $value);
            $this->assertArrayHasKey('amount', $value);
            $this->assertArrayHasKey('currency', $value);

            $this->assertNotEmpty($value['bin']);
            $this->assertIsNumeric($value['amount']);
            $this->assertNotEmpty($value['currency']);
        }
    }

    public function testCalculate()
    {
        $value = $this->init()->extractDataFromRow($this->getRow());
        $result = $this->init()->calculate($value['bin'], floatval($value['amount']), $value['currency']);

        $this->assertIsFloat($result);
    }

    public function testGetRatet()
    {
        $value = $this->init()->extractDataFromRow($this->getRow());

        $result = $this->init()->getRate($value['currency']);
        $this->assertIsNumeric($result);
    }

    public function testExchageRateUrl()
    {
        $result = file_get_contents(Main::$exchangeRateUrl);
        $result = json_decode($result, true);
        $this->assertArrayHasKey('rates', $result);
    }

    public function testBinListUrl()
    {
        $value = $this->init()->extractDataFromRow($this->getRow());

        $result = file_get_contents(Main::$binListUrl."/{$value['bin']}");
        $result = json_decode($result, true);

        $this->assertArrayHasKey("country", $result);
        $this->assertArrayHasKey("alpha2", $result['country']);
    }

    public function testIsEu()
    {
        $value = $this->init()->extractDataFromRow($this->getRow());

        $result = $this->init()->isEu($value['bin']);
        $this->assertIsBool($result);

    }
}