<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 10:13 AM
 */

namespace CalculateFeeBundle\DataSource;


use CalculateFeeBundle\Common\Contract\BinProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class BinProvider implements BinProviderInterface
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
     * BinProvider constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client   = $client;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * @return mixed
     * @throws \Exception
     */
    public function getAlpha2Value()
    {
        $data = $this->getProviderData();

        try{
            $response = $data['responseObject'];
            return $response->country->alpha2;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage(), 404);
        }
    }
}