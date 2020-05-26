<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/26/20
 * Time: 3:14 PM
 */

namespace CalculateFeeBundle\Common\Contract;


interface ProviderInterface
{
    /**
     * @param string $alpha2Code
     * @return mixed
     */
    public function getDividant(string $alpha2Code);
}