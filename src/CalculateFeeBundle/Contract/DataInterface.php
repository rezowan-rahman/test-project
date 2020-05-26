<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/26/20
 * Time: 12:15 AM
 */

namespace CalculateFeeBundle\Common\Contract;

/**
 * Interface DataInterface
 * @package CalculateFeeBundle\Common\Contract
 */
interface DataInterface
{
    /**
     * @return mixed
     */
    public function auth();

    /**
     * @param string $currency
     * @return mixed
     */
    public function getRateData($currency);

    /**
     * @param string $bin
     * @return mixed
     */
    public function getBinData($bin);

}