<?php
/**
 * Created by PhpStorm.
 * User: rezowan
 * Date: 5/26/20
 * Time: 3:40 PM
 */

namespace Test\CalculateFeeBundle\DataSource;


use CalculateFeeBundle\Common\Contract\ProviderInterface;
use CalculateFeeBundle\DataSource\CommissionProvider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->provider = new CommissionProvider();
    }

    public function testGetDividantIfExistsInArray()
    {
        $alpha2Code = "SI";
        $result = $this->provider->getDividant($alpha2Code);

        $this->assertIsFloat($result);
        $this->assertEquals(0.01, $result);
    }

    public function testGetDividantIfNotExistsInArray()
    {
        $alpha2Code = "JP";
        $result = $this->provider->getDividant($alpha2Code);

        $this->assertIsFloat($result);
        $this->assertEquals(0.02, $result);
    }
}