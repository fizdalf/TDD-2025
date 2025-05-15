<?php

namespace Kata;

class InternacionalCalculator {
    public function calcular($shipment) {
        $cost = 25 + ($shipment['distance'] * 0.50) + ($shipment['weight'] * 1.00);
        if ($shipment['distance'] > 3000) {
            $cost += $cost * 0.2;
        }
        return $cost;
    }
}
