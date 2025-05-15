<?php

namespace Kata;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExpressCalculatorTest extends TestCase
{
    #[Test]
    public function calcula_precio_express_sin_extra(): void
    {
        $shipment = [
            'weight' => 8,
            'distance' => 100
        ];

        $calculator = new ExpressCalculator();
        $precio = $calculator->calcular($shipment);

        $esperado = 10 + (100 * 0.20) + (8 * 0.80);
        $this->assertEquals($esperado, $precio);
    }

    #[Test]
    public function calcula_precio_express_con_extra(): void
    {
        $shipment = [
            'weight' => 12,
            'distance' => 100
        ];

        $calculator = new ExpressCalculator();
        $precio = $calculator->calcular($shipment);

        $base = 10 + (100 * 0.20) + (12 * 0.80);
        $esperado = $base * 1.1;

        $this->assertEquals($esperado, $precio);
    }
}
