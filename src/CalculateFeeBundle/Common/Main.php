<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:19 PM
 */

namespace CalculateFeeBundle\Common;

class Main
{
    const EU_DATA = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR','HU', 'IE', 'IT', 'LT','LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];

    /**
     * @var string
     */
    private $inputData;

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
    private $exchangeList = [];


    /**
     * Main constructor.
     * @param string $inputData
     */
    public function __construct(string $inputData)
    {
        $this->inputData = $inputData;
    }

    /**
     * @param $data
     */
    public function printInStdOut($round = false)
    {
        foreach (explode("\n", $this->inputData) as $row) {
            $value = $this->extractDataFromRow($row);
            $data = $this->calculate($value['bin'], floatval($value['amount']), $value['currency']);

            if($round) {
                $data = round($data, 2);
            }

            print "{$data}\n";
        }

    }

    public function calculate(int $bin, float $amount, string $currency)
    {
        $binResult = $this->isEu($bin);
        $rate = $this->getRate($currency);

        $amntFixed = $rate == 0 ? $amount : $amount / $rate;

        return $amntFixed * ($binResult ? 0.01 : 0.02);
    }

    /**
     * @param $row
     * @return array
     */
    public function extractDataFromRow($row)
    {
        $result = [];
        $row = preg_replace('/\{|\"|\'|\}/', '', $row);
        $items = explode(",", $row);

        if(count($items) <= 0) return $result;

        foreach($items as $item) {
            $pieces = explode(":", $item);
            if(count($pieces) <= 0) return $result;

            $result[$pieces[0]] = $pieces[1];
        }

        return $result;
    }

    /**
     * @param $value
     * @return string|null
     */
    public function getRate($value)
    {
        if(count($this->exchangeList) <= 0) {
            $result = file_get_contents(static::$exchangeRateUrl);
            if(!$result) return NULL;

            $this->exchangeList = json_decode($result, true);
        }

        if(!array_key_exists('rates', $this->exchangeList)) return 0;
        return array_key_exists($value, $this->exchangeList['rates']) ? $this->exchangeList['rates'][$value]: 0;
    }

    /**
     * @param $value
     * @return bool
     */
    private function getEuValueFromList($value)
    {
        $result = file_get_contents(static::$binListUrl."/{$value}");
        if (!$result) return false;

        $result = json_decode($result);

        if(!property_exists($result, "country")) return false;

        return property_exists($result->country, 'alpha2') ? $result->country->alpha2 : NULL;
    }

    public function isEu($value)
    {
        $response = $this->getEuValueFromList($value);
        return in_array($response, self::EU_DATA);
    }
}