<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 11:39 AM
 */

namespace CalculateFeeBundle\Common\Contract;


interface ExchangeRateProviderInterface
{

    public function authenticate();

    /**
     * @param $url
     */
    public function setUrl($url);

    /**
     * @param $currency
     */
    public function getRate($currency);
}