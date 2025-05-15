<?php

namespace Kata;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InternacionalCalculatorTest extends TestCase
{
    #[Test]
    public function calcula_precio_internacional_sin_extra(): void
    {
        $shipment = [
            'weight' => 2,
            'distance' => 2000
        ];

        $calculator = new InternacionalCalculator();
        $precio = $calculator->calcular($shipment);

        $esperado = 25 + (2000 * 0.50) + (2 * 1.00);
        $this->assertEquals($esperado, $precio);
    }

    #[Test]
    public function calcula_precio_internacional_con_extra(): void
    {
        $shipment = [
            'weight' => 5,
            'distance' => 4000
        ];

        $calculator = new InternacionalCalculator();
        $precio = $calculator->calcular($shipment);

        $base = 25 + (4000 * 0.50) + (5 * 1.00);
        $esperado = $base + ($base * 0.2);

        $this->assertEquals($esperado, $precio);
    }
}
