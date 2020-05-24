<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/23/20
 * Time: 9:34 PM
 */

namespace CalculateFeeBundle\DataSource;

class Data
{

    const EU_DATA = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR','HU', 'IE', 'IT', 'LT','LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];

    /**
     * @var string
     */
    public static $exchangeRateUrl = "https://api.exchangeratesapi.io/latest";

    /**
     * @var string
     */
    public static $binListUrl = "https://lookup.binlist.net";

    /**
     * @var array
     */
    public static $exchangeList = [];


    /**
     * @return array
     */
    public static function getEuData()
    {
        return self::EU_DATA;
    }

    /**
     * @param $bin
     * @return array|mixed
     */
    public static function getBinDetails($bin)
    {
        $result = file_get_contents(static::$binListUrl."/{$bin}");
        if(!$result) return [];

        return json_decode($result, true);
    }

    /**
     * @return array|mixed
     */
    public static function getExchageRate()
    {
        if(count(static::$exchangeList) <= 0) {
            $result = file_get_contents(static::$exchangeRateUrl);
            if(!$result) return [];

            static::$exchangeList = json_decode($result, true);
        }

        return static::$exchangeList;
    }

    public static function getDividant($bin)
    {
        $response = self::getBinDetails($bin);
        return (float) in_array(strtoupper($response['country']['alpha2']), self::getEuData()) ? 0.01 : 0.02;
    }


}