<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:19 PM
 */

namespace CalculateFeeBundle\Common;

use CalculateFeeBundle\Common\Contract\DataInterface;
use CalculateFeeBundle\DataSource\Data;


class Main
{
    /**
     * @var string
     */
    private $inputData;

    /**
     * @var DataInterface
     */
    private $dataModel;


    /**
     * Main constructor.
     * @param string $inputData
     */
    public function __construct(string $inputData, DataInterface $dataModel)
    {
        $this->inputData = $inputData;
        $this->dataModel = $dataModel;
    }

    public function printInStdOut($round = false)
    {
        $str = '';
        foreach (explode("\n", trim($this->inputData)) as $row) {
            $value = json_decode($row, true);

            if(!array_key_exists('bin', $value)
                AND !array_key_exists('amount', $value)
                AND !array_key_exists('currency', $value)
            ) continue;

            $data = $this->calculate($value['bin'], $value['amount'], $value['currency']);

            if($round) {
                $data = number_format($data, 2);
            }

            $str .= "{$data}\n";
            print "{$data}\n";
        }

        return $str;
    }

    /**
     * @param string $bin
     * @param float $amount
     * @param string $currency
     *
     * @return float|int
     */
    public function calculate(string $bin, float $amount, string $currency)
    {
        $rate = $this->dataModel->getRateData($currency);
        $commission = $this->getDividant($bin);
        $amntFixed = $rate == 0 ? $amount : $amount / $rate;

        return $amntFixed * $commission;
    }

    /**
     * @param string $bin
     * @return float
     */
    public function getDividant(string $bin)
    {
        $alpha2Code = $this->dataModel->getBinData($bin);
        return in_array(strtoupper($alpha2Code), Data::EU_DATA) ? 0.01 : 0.02;
    }
}