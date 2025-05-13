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

    #[Test]
    public function test_express_con_peso_alto()
    {
        $system = new ShippingSystem();
        $shipment = [
            'id' => 'EXP002',
            'customer' => 'Luis Torres',
            'type' => 'EXPRESS',
            'weight' => 15,
            'distance' => 50,
            'deliveryDays' => 2,
            'address' => 'Calle Luna 45'
        ];

        $base = 10 + (50 * 0.20) + (15 * 0.80); // 10 + 10 + 12 = 32
        $expectedPrice = $base * 1.1; // 32 * 1.1 = 35.2

        $result = $system->process($shipment);

        $this->assertEquals(round($expectedPrice, 2), $result['price']);
        $this->assertEquals('ALTA', $result['priority']);
    }

    #[Test]
    public function test_internacional_con_distancia_alta()
    {
        $system = new ShippingSystem();
        $shipment = [
            'id' => 'INT003',
            'customer' => 'Lucía Fernández',
            'type' => 'INTERNACIONAL',
            'weight' => 8,
            'distance' => 4000,
            'deliveryDays' => 1,
            'address' => 'Plaza del Sol 9'
        ];

        $base = 25 + (4000 * 0.50) + (8 * 1.00); // 25 + 2000 + 8 = 2033
        $expectedPrice = $base + ($base * 0.2); // 2033 + 406.6 = 2439.6

        $result = $system->process($shipment);

        $this->assertEquals(round($expectedPrice, 2), $result['price']);
        $this->assertEquals('ALTA', $result['priority']);
    }


}
