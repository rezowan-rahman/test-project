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
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use JsonSchema\Exception\InvalidSchemaException;

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
        // TODO: Implement authenticate() method.
    }

    /**
     * @return \ArrayObject
     */
    public function getProviderData()
    {
        try {
            $result = $this->client->get($this->getUrl());
        } catch (ClientException $e) {
            throw new BadResponseException();
        }

        $resultDecoded = json_decode($result->getBody());
        return new \ArrayObject($resultDecoded);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAlpha2Value()
    {
        $data = $this->getProviderData();

        try{
            return $data['country']->alpha2;
        } catch(\Exception $e) {
            throw new \Exception();
        }
    }
}