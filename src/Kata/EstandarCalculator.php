<?php

namespace Kata;

class EstandarCalculator
{
    public function calcular($shipment) {
        return 5 + ($shipment['distance'] * 0.10) + ($shipment['weight'] * 0.50);
    }
}