<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:39 PM
 */

namespace Test\CalculateFeeBundle\Common;

use CalculateFeeBundle\Common\Contract\DataInterface;
use CalculateFeeBundle\Common\Contract\ProviderInterface;
use CalculateFeeBundle\DataSource\BinProvider;
use CalculateFeeBundle\DataSource\CommissionProvider;
use CalculateFeeBundle\DataSource\Data;
use CalculateFeeBundle\DataSource\ExchangeRateProvider;
use CalculateFeeBundle\DataSource\Provider;
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
    private $binProvider;

    /**
     * @var Mock
     */
    private $exchangeRateProvider;

    /**
     * @var Mock
     */
    private $comissionProvider;


    public function setUp(): void
    {
        parent::setUp();

        $this->binProvider = \Mockery::mock(BinProvider::class)->makePartial();
        $this->exchangeRateProvider = \Mockery::mock(ExchangeRateProvider::class)->makePartial();
        $this->comissionProvider = \Mockery::mock(CommissionProvider::class);

        $this->main     = new Main($this->getInputFile(), $this->binProvider, $this->exchangeRateProvider, $this->comissionProvider);
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

        $this->binProvider->shouldReceive("setUrl")->with($bin)->andReturns("https://lookup.binlist.net/{$bin}");
        $this->binProvider->shouldReceive("getAlpha2Value")->andReturns("GB");
        $this->exchangeRateProvider->shouldReceive("getRate")->andReturns(0.88878);
        $this->comissionProvider->shouldReceive('getDividant')->with("GB")->andReturns(0.02);

        $result = $this->main->calculate($bin, $amount, $currency);

        $this->assertIsFloat($result);
        $this->assertEquals(45.01, number_format($result, 2));
    }

    public function testCalculatewithRateZero()
    {
        $bin = "45717360";
        $amount = 100.00;
        $currency = "EUR";

        $this->binProvider->shouldReceive("setUrl")->with($bin)->andReturns("https://lookup.binlist.net/{$bin}");
        $this->binProvider->shouldReceive("getAlpha2Value")->andReturns("DK");
        $this->exchangeRateProvider->shouldReceive("getRate")->andReturns(0);
        $this->comissionProvider->shouldReceive('getDividant')->with("DK")->andReturns(0.01);

        $result = $this->main->calculate($bin, $amount, $currency);

        $this->assertIsFloat($result);
        $this->assertEquals(1.00, number_format($result, 2));
    }

}