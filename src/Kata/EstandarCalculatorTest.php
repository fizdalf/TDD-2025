<?php

namespace Kata;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EstandarCalculatorTest extends TestCase
{
    #[Test]
    public function calcula_precio_estandar_correctamente(): void
    {
        $shipment = [
            'weight' => 3,
            'distance' => 200
        ];

        $calculator = new EstandarCalculator();
        $precio = $calculator->calcular($shipment);

        $esperado = 5 + (200 * 0.10) + (3 * 0.50);
        $this->assertEquals($esperado, $precio);
    }
}
