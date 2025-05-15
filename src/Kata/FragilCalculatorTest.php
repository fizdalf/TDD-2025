<?php

namespace Kata;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FragilCalculatorTest extends TestCase
{
    #[Test]
    public function calcula_precio_fragil_sin_extra(): void
    {
        $shipment = [
            'weight' => 3,
            'distance' => 50
        ];

        $calculator = new FragilCalculator();
        $precio = $calculator->calcular($shipment);

        $esperado = 8 + (50 * 0.15) + (3 * 0.70);
        $this->assertEquals($esperado, $precio);
    }

    #[Test]
    public function calcula_precio_fragil_con_extra(): void
    {
        $shipment = [
            'weight' => 6,
            'distance' => 50
        ];

        $calculator = new FragilCalculator();
        $precio = $calculator->calcular($shipment);

        $base = 8 + (50 * 0.15) + (6 * 0.70);
        $esperado = $base + 5;

        $this->assertEquals($esperado, $precio);
    }
}

