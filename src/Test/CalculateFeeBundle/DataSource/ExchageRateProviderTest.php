<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 1:02 PM
 */

namespace Test\CalculateFeeBundle\DataSource;


use CalculateFeeBundle\DataSource\ExchangeRateProvider;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExchageRateProviderTest extends TestCase
{
    /**
     * @var ExchangeRateProvider
     */
    private $provider;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->provider = new ExchangeRateProvider($this->getMockClient(200, [], $this->getMockResponseData()));

    }

    public function getMockResponseData()
    {
        return <<<RESPONSE
        {
            "rates": {
                "CAD": 1.5184,
                "HKD": 8.5078,
                "ISK": 154.1,
                "PHP": 55.358,
                "DKK": 7.4558,
                "HUF": 349.6,
                "CZK": 27.073,
                "AUD": 1.6539,
                "RON": 4.844,
                "SEK": 10.5563,
                "IDR": 16193.61,
                "INR": 82.927,
                "BRL": 5.9114,
                "RUB": 77.7494,
                "HRK": 7.584,
                "JPY": 117.92,
                "THB": 34.988,
                "CHF": 1.06,
                "SGD": 1.556,
                "PLN": 4.4506,
                "BGN": 1.9558,
                "TRY": 7.4014,
                "CNY": 7.8269,
                "NOK": 10.8943,
                "NZD": 1.7717,
                "ZAR": 19.065,
                "USD": 1.0975,
                "MXN": 24.3102,
                "ILS": 3.8543,
                "GBP": 0.88878,
                "KRW": 1352.23,
                "MYR": 4.7889
            }
        }
RESPONSE;

    }

    public function getMockClient($status, $headers=[], $body)
    {
        $mock = new MockHandler([
            new Response($status, $headers, $body),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return $client;
    }

    public function testSetUrl()
    {
        $url = "https://exchageRate.example.com";
        $this->provider->setUrl($url);
        $this->assertEquals($url, $this->provider->getUrl());
    }

    public function testAuthenticate()
    {
        $result = $this->provider->authenticate();
        $this->assertEquals(true, $result);
    }

    public function testGetProviderData()
    {
        $result = $this->provider->getProviderData();
        $this->assertIsObject($result['responseObject']);
    }

    public function testGetProviderDataClientException()
    {
        $this->provider = new ExchangeRateProvider($this->getMockClient(403, [], NULL));
        $result = $this->provider->getProviderData();
        $this->assertEquals(403, $result['statusCode']);
    }

    public function testGetProviderDataServerException()
    {
        $this->provider = new ExchangeRateProvider($this->getMockClient(504, [], NULL));
        $result = $this->provider->getProviderData();
        $this->assertEquals(504, $result['statusCode']);
    }

    public function testGetRateIfExists()
    {
        $currency = "JPY";
        $rate = $this->provider->getRate($currency);
        $this->assertEquals(117.92, $rate);
    }

    public function testGetRateIfNotExists()
    {
        $currency = "EUR";
        $rate = $this->provider->getRate($currency);
        $this->assertEquals(0, $rate);
    }

    public function testGetRateIfException()
    {
        $data = <<<RESPONSE
        {
        }
RESPONSE;

        $this->provider = new ExchangeRateProvider($this->getMockClient(200, [], $data));
        $exception = false;
        try {
            $this->provider->getRate("JPY");
        } catch (\Exception $e) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);

    }

}