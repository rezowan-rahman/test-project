<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 2:01 PM
 */

namespace Test\CalculateFeeBundle\DataSource;


use CalculateFeeBundle\DataSource\BinProvider;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

class BinProviderTest extends TestCase
{
    /**
     * @var BinProvider
     */
    private $provider;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->provider = new BinProvider($this->getMockClient(200, [], $this->getMockResponseData()));
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

    public function getMockResponseData()
    {
        return <<<RESPONSE
        {
            "number": {},
            "scheme": "visa",
            "country": {
                "numeric": "840",
                "alpha2": "US",
                "name": "United States of America",
                "emoji": "🇺🇸",
                "currency": "USD",
                "latitude": 38,
                "longitude": -97
            },
            "bank": {
                "name": "VERMONT NATIONAL BANK",
                "url": "www.communitynationalbank.com",
                "phone": "(802) 744-2287"
            }
        }
RESPONSE;

    }

    public function testSetUrl()
    {
        $url = "https://bin.example.com/binValue";
        $this->provider->setUrl($url);
        $this->assertEquals($url, $this->provider->getUrl());
    }

    public function testAuthentication()
    {
        $result = $this->provider->authenticate();
        $this->assertEquals(true, $result);
    }

    public function testGetProviderData()
    {
        $result = $this->provider->getProviderData();
        $this->assertIsObject($result['responseObject']);
    }

    public function testGetProviderDataException()
    {
        $this->provider = new BinProvider($this->getMockClient(403, [], NULL));
        $result = $this->provider->getProviderData();
        $this->assertEquals(403, $result['statusCode']);
    }

    public function testGetProviderDataServerException()
    {
        $this->provider = new BinProvider($this->getMockClient(504, [], NULL));
        $result = $this->provider->getProviderData();
        $this->assertEquals(504, $result['statusCode']);
    }

    public function testAlpha2IfExists()
    {
        $value = $this->provider->getAlpha2Value();
        $this->assertEquals('US', $value);
    }

    public function testAlpha2IfNotExists()
    {
        $data = <<<RESPONSE
        {
            "country": {
                "numeric": "840",
                "name": "United States of America",
                "emoji": "🇺🇸",
                "currency": "USD",
                "latitude": 38,
                "longitude": -97
            }
        }
RESPONSE;

        $this->provider = new BinProvider($this->getMockClient(200, [], $data));
        $exception = false;
        try {
            $this->provider->getAlpha2Value();
        } catch (\Exception $e) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
    }


}