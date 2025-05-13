<?php

namespace Kata;

class ShippingSystem {

    public function process($shipment) {
        $cost = 0;
        $type = $shipment['type'];
        $weight = $shipment['weight'];
        $distance = $shipment['distance'];

        if ($type === "ESTANDAR") {
            $cost = 5 + ($distance * 0.10) + ($weight * 0.50);
        } else if ($type === "EXPRESS") {
            $cost = 10 + ($distance * 0.20) + ($weight * 0.80);
            if ($weight > 10) {
                $cost = $cost * 1.1;
            }
        } else if ($type === "INTERNACIONAL") {
            $cost = 25 + ($distance * 0.50) + ($weight * 1.00);
            if ($distance > 3000) {
                $cost = $cost + ($cost * 0.2);
            }
        } else if ($type === "FRAGIL") {
            $cost = 8 + ($distance * 0.15) + ($weight * 0.70);
            if ($weight > 5) {
                $cost = $cost + 5;
            }
        }

        $priority = "BAJA";
        if ($type === "EXPRESS" || $type === "INTERNACIONAL") {
            if ($shipment['deliveryDays'] <= 2) {
                $priority = "ALTA";
            }
        } else {
            if ($shipment['deliveryDays'] <= 3) {
                $priority = "MEDIA";
            }
        }

        $label = $shipment['id'] . " - " . $shipment['customer'] . "\n";
        $label .= "Entregar en: " . $shipment['address'] . "\n";
        $label .= "Tipo: " . $type . " - Precio: €" . round($cost, 2) . "\n";
        $label .= "Prioridad: " . $priority;

        return [
            'price' => round($cost, 2),
            'label' => $label,
            'priority' => $priority
        ];
    }
}