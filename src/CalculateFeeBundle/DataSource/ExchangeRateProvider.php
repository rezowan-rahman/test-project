<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 11:02 AM
 */

namespace CalculateFeeBundle\DataSource;

use CalculateFeeBundle\Common\Contract\ExchangeRateProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\ResourceNotFoundException;
use Mockery\Exception;

class ExchangeRateProvider implements ExchangeRateProviderInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \ArrayObject
     */
    private $response;

    /**
     * ExchangeRateProvider constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->url      = "https://api.exchangeratesapi.io/latest";
        $this->client   = $client;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function authenticate()
    {
        // TODO: Implement authenticate() method.
    }

    /**
     * @return \ArrayObject
     */
    public function getProviderData()
    {
        if($this->response != NULL) {
            return $this->response;
        }

        try {
            $result = $this->client->get($this->getUrl());
        } catch (ClientException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->response = new \ArrayObject(json_decode($result->getBody()));
        return $this->response;
    }

    /**
     * @param $currency
     * @return int
     * @throws \Exception
     */
    public function getRate($currency)
    {
        $data = $this->getProviderData();

        if(!array_key_exists('rates', $data)) {
            throw new \Exception("rates does not exist", 404);
        }

        return property_exists($data['rates'], $currency) ? $data['rates']->$currency : 0;
    }

}