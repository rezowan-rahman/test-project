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

class MainTest extends TestCase
{

    public static $main;

    public static function init()
    {
        if(!self::$main instanceof Main) {
            self::$main = new Main(self::getInputFile());
        }
        return self::$main;
    }

    public static function getFileName()
    {
        return __DIR__.'/../../../../input.txt';
    }

    public static function getInputFile()
    {
        return file_get_contents(self::getFileName());
    }

    public static function getRow()
    {
        $rows = self::init()->getRowsFromInputData();
        return $rows[count($rows) -1];
    }

    public function testInputFileExists()
    {
        $this->assertFileExists(self::getFileName());
    }

    public function testPrintInStdOut()
    {
        $output = self::init()->printInStdOut(true);
        $this->assertIsString($output);
        $this->assertRegExp('/\d*\.?\d*\\n/', $output);
    }

    public function testExtractDataFromRow()
    {
        foreach(self::init()->getRowsFromInputData() as $row) {

            $value = self::init()->extractDataFromRow($row);

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
        $value = self::init()->extractDataFromRow(self::getRow());
        $result = self::init()->calculate($value['bin'], floatval($value['amount']), $value['currency']);

        $this->assertIsFloat($result);
    }

    public function testGetRate()
    {
        $value = self::init()->extractDataFromRow(self::getRow());

        $result = $this->init()->getRate($value['currency']);
        $this->assertIsNumeric($result);
    }
}