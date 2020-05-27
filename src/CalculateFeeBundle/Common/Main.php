<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:19 PM
 */

namespace CalculateFeeBundle\Common;

use CalculateFeeBundle\Common\Contract\BinProviderInterface;
use CalculateFeeBundle\Common\Contract\ExchangeRateProviderInterface;
use CalculateFeeBundle\Common\Contract\ProviderInterface;


class Main
{

    /**
     * @var string
     */
    private $inputData;

    /**
     * @var BinProviderInterface
     */
    private $binProvider;

    /**
     * @var ExchangeRateProviderInterface
     */
    private $exchangeRateProvider;

    /**
     * @var ProviderInterface
     */
    private $provider;



    public function __construct(string $inputData, BinProviderInterface $binProvider, ExchangeRateProviderInterface $exchangeRateProvider,
                                ProviderInterface $provider)
    {
        $this->inputData            = $inputData;
        $this->binProvider          = $binProvider;
        $this->exchangeRateProvider = $exchangeRateProvider;
        $this->provider             = $provider;
    }


    /**
     * @param $round
     */
    public function printInStdOut($round)
    {
        $data = $this->processData($round);
        print(implode("\n", $data));
    }


    /**
     * @param $round
     * @return array
     */
    private function processData($round)
    {
        $result = array();

        foreach (explode("\n", trim($this->inputData)) as $row) {
            $value = json_decode($row, true);

            if (!array_key_exists('bin', $value)
                AND !array_key_exists('amount', $value)
                AND !array_key_exists('currency', $value)
            ) continue;

            $data = $this->calculate($value['bin'], $value['amount'], $value['currency']);

            if($round == true) {
                $data = number_format($data, 2);
            }

            array_push($result, $data);
        }

        return $result;
    }


    /**
     * @param $bin
     * @return BinProviderInterface
     */
    public function setBinUrl($bin)
    {
        return $this->binProvider->setUrl(sprintf("https://lookup.binlist.net/%s", $bin));
    }


    /**
     * @param string $bin
     * @param float $amount
     * @param string $currency
     *
     * @return float
     */
    public function calculate(string $bin, float $amount, string $currency)
    {
        $binProvider = $this->setBinUrl($bin);
        $alpha2Code = $binProvider->getAlpha2Value();

        $rate = $this->exchangeRateProvider->getRate($currency);

        $amntFixed = $rate == 0 ? $amount : $amount / $rate;

        return $amntFixed * $this->provider->getDividant($alpha2Code);
    }
}