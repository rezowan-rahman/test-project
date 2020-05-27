<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/27/20
 * Time: 11:36 AM
 */

namespace CalculateFeeBundle\Common\Contract;


interface BinProviderInterface
{

    public function authenticate();

    /**
     * @param $url
     */
    public function setUrl($url);

    public function getAlpha2Value();
}