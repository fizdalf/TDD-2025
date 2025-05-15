<?php

namespace Kata;

class FragilCalculator {
    public function calcular($shipment) {
        $cost = 8 + ($shipment['distance'] * 0.15) + ($shipment['weight'] * 0.70);
        if ($shipment['weight'] > 5) {
            $cost += 5;
        }
        return $cost;
    }
}
