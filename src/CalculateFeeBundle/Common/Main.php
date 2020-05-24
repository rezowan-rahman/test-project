<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:19 PM
 */

namespace CalculateFeeBundle\Common;

use CalculateFeeBundle\DataSource\Data;

class Main
{
    /**
     * @var string
     */
    private $inputData;


    /**
     * Main constructor.
     * @param string $inputData
     */
    public function __construct(string $inputData)
    {
        $this->inputData = $inputData;
    }

    public function getRowsFromInputData()
    {
        return explode("\n", trim($this->inputData));
    }

    /**
     * @param $data
     */
    public function printInStdOut($round = false)
    {
        $str = '';
        foreach ($this->getRowsFromInputData() as $row) {
            $value = $this->extractDataFromRow($row);

            $data = $this->calculate($value['bin'], floatval($value['amount']), $value['currency']);

            if($round) {
                $data = round($data, 2);
            }

            $str .= "{$data}\n";
            print "{$data}\n";
        }

        return $str;
    }

    public function calculate(int $bin, float $amount, string $currency)
    {
        $rate = $this->getRate($currency);
        $amntFixed = $rate == 0 ? $amount : $amount / $rate;

        return $amntFixed * Data::getDividant($bin);
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
     * @return int
     */
    public function getRate($value)
    {
        $response = Data::getExchageRate();
        if(!array_key_exists('rates', $response)) return 0;
        return array_key_exists($value, $response['rates']) ? $response['rates'][$value]: 0;
    }
}