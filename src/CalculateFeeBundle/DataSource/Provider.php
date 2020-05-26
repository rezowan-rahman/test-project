<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/26/20
 * Time: 3:13 PM
 */

namespace CalculateFeeBundle\DataSource;


use CalculateFeeBundle\Common\Contract\ProviderInterface;

class Provider implements ProviderInterface
{
    private const EU_DATA = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR','HU', 'IE', 'IT', 'LT','LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];


    public function getDividant(string $code)
    {
        return in_array(strtoupper($code),self::EU_DATA) ? 0.01 : 0.02;
    }

}