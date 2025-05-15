<?php

namespace Kata;

class ExpressCalculator {
    public function calcular($shipment) {
        $cost = 10 + ($shipment['distance'] * 0.20) + ($shipment['weight'] * 0.80);
        if ($shipment['weight'] > 10) {
            $cost *= 1.1;
        }
        return $cost;
    }
}