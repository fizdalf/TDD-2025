<?php

namespace Kata;

use PHPUnit\Framework\Attributes\Test;

class ShippingSystemTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function test_estandar_sin_condiciones_especiales()
    {
        $system = new ShippingSystem();
        $shipment = [
            'id' => 'STD001',
            'customer' => 'Ana García',
            'type' => 'ESTANDAR',
            'weight' => 5,
            'distance' => 100,
            'deliveryDays' => 3,
            'address' => 'Av. Siempre Viva 123'
        ];

        $result = $system->process($shipment);

        $expectedPrice = 5 + (100 * 0.10) + (5 * 0.50); // 5 + 10 + 2.5 = 17.5

        $this->assertEquals(round($expectedPrice, 2), $result['price']);
        $this->assertEquals('MEDIA', $result['priority']);
        $this->assertStringContainsString('STD001 - Ana García', $result['label']);
    }


}
