<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/22/20
 * Time: 3:19 PM
 */

namespace CalculateFeeBundle\Common;

use CalculateFeeBundle\Common\Contract\DataInterface;
use CalculateFeeBundle\Common\Contract\ProviderInterface;
use JsonSchema\Exception\InvalidSchemaException;
use Mockery\Exception;


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
     * @var ProviderInterface
     */
    private $provider;


    /**
     * Main constructor.
     * @param string $inputData
     * @param DataInterface $dataModel
     * @param ProviderInterface $provider
     */
    public function __construct(string $inputData, DataInterface $dataModel, ProviderInterface $provider)
    {
        $this->inputData    = $inputData;
        $this->dataModel    = $dataModel;
        $this->provider     = $provider;
    }

    public function printInStdOut($round = false)
    {
        try {
            $str = '';
            foreach (explode("\n", trim($this->inputData)) as $row) {
                $value = json_decode($row, true);

                if (!array_key_exists('bin', $value)
                    AND !array_key_exists('amount', $value)
                    AND !array_key_exists('currency', $value)
                ) continue;

                $data = $this->calculate($value['bin'], $value['amount'], $value['currency']);

                if ($round) {
                    $data = number_format($data, 2);
                }

                $str .= "{$data}\n";
                print "{$data}\n";
            }

            return $str;
        } catch (\Exception $e) {
            throw new InvalidSchemaException("{$row} might not be a valid json");
        }
    }

    /**
     * @param string $bin
     * @param float $amount
     * @param string $currency
     *
     * @return float
     */
    public function calculate(string $bin, float $amount, string $currency) :float
    {
        $rate = $this->dataModel->getRateData($currency);
        $alpha2Code = $this->dataModel->getBinData($bin);

        $amntFixed = $rate == 0 ? $amount : $amount / $rate;

        return $amntFixed * $this->provider->getDividant($alpha2Code);
    }
}