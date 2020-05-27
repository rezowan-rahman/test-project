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
use GuzzleHttp\Exception\ServerException;

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
     * @var array
     */
    public static $response;

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
        return true;
    }

    /**
     * @return array
     */
    public function getProviderData()
    {
        try {
            $response = $this->client->get($this->getUrl());
        } catch (ClientException $e) {
            $response = $e->getResponse();
        } catch (ServerException $e) {
            $response = $e->getResponse();
        }

        return [
            "statusCode"        => $response->getStatusCode(),
            'responseObject'    => json_decode($response->getBody())
        ];

    }

    /**
     * @param $currency
     * @return int
     * @throws \Exception
     */
    public function getRate($currency)
    {
        $data = $this->getProviderData()['responseObject'];

        if(!property_exists($data, 'rates')) {
            throw new \Exception("rates does not exist", 404);
        }

        return property_exists($data->rates, $currency) ? $data->rates->$currency : 0;
    }

}