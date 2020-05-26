<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/23/20
 * Time: 9:34 PM
 */

namespace CalculateFeeBundle\DataSource;

use CalculateFeeBundle\Common\Contract\DataInterface;
use JsonSchema\Exception\InvalidSchemaException;

class Data implements DataInterface
{

    const EU_DATA = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR','HU', 'IE', 'IT', 'LT','LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];

    /**
     * @var string
     */
    private $rateUrl = "https://api.exchangeratesapi.io/latest";

    /**
     * @var string
     */
    private $binUrl = "https://lookup.binlist.net";


    public function getRateUrl()
    {
        return $this->rateUrl;
    }

    public function setRateUrl(string $rateUrl)
    {
        return $this->rateUrl = $rateUrl;
    }

    public function getBinUrl(string $bin)
    {
        return $this->binUrl."/".$bin;
    }

    public function setBinUrl(string $binUrl)
    {
        return $this->binUrl = $binUrl;
    }


    public function auth()
    {
        /**
         * TO-DO
         */
    }

    /**
     * @param string $bin
     * @return mixed
     */
    public function getBinData($bin)
    {
        $url = $this->getBinUrl($bin);
        $result = json_decode(file_get_contents($url), true);

        try{
            return $result['country']['alpha2'];
        } catch(\Exception $e) {
            throw new InvalidSchemaException("alpha2 code not found for {$bin}", 400);
        }
    }

    /**
     * @param string $currency
     * @return int
     */
    public function getRateData($currency)
    {
        $url = $this->getRateUrl();
        $result = json_decode(file_get_contents($url), true);

        if(!array_key_exists("rates", $result)) {
            throw new InvalidSchemaException("rates not found in".$result, 400);
        }

        return !array_key_exists($currency, $result['rates']) ? 0: $result['rates'][$currency];

    }

}