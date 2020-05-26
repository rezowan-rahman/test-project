<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:39 PM
 */

namespace Test\CalculateFeeBundle\Common;

use CalculateFeeBundle\DataSource\Data;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

use CalculateFeeBundle\Common\Main;

class MainTest extends TestCase
{

    /**
     * @var Main
     */
    private $main;

    /**
     * @var Mock
     */
    private $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->data = \Mockery::mock(Data::class);
        $this->main = new Main($this->getInputFile(), $this->data);
    }

    public function getFileName()
    {
        return __DIR__.'/../../../../input.txt';
    }

    public function getInputFile()
    {
        return file_get_contents($this->getFileName());
    }

    public function testFileExists()
    {
        $this->assertFileExists($this->getFileName());
    }

    public function testCalculatewithRateNonZero()
    {
        $bin = "4745030";
        $amount = 2000.00;
        $currency = "GBP";

        $this->data->shouldReceive('getRateData')->with($currency)->andReturn(0.89515);
        $this->data->shouldReceive('getBinData')->with($bin)->andReturn('GB');

        $result = $this->main->calculate($bin, $amount, $currency);

        $this->assertIsFloat($result);
        $this->assertEquals(44.69, number_format($result, 2));
    }

    public function testCalculatewithRateZero()
    {
        $bin = "45417360";
        $amount = 100.00;
        $currency = "EUR";

        $this->data->shouldReceive('getRateData')->with($currency)->andReturn(0);
        $this->data->shouldReceive('getBinData')->with($bin)->andReturn('DK');

        $result = $this->main->calculate($bin, $amount, $currency);

        $this->assertIsFloat($result);
        $this->assertEquals(1.00, number_format($result, 2));
    }

}