<?php

namespace Kata;

class ShippingSystem {

    public function process($shipment) {
        $cost = $this->calcularPrecio($shipment);
        $priority = $this->determinarPrioridad($shipment);
        $label = $this->generarEtiqueta($shipment, $cost, $priority);

        return [
            'price' => round($cost, 2),
            'label' => $label,
            'priority' => $priority
        ];
    }

    private function calcularPrecio($shipment) {
        $type = $shipment['type'];
        $weight = $shipment['weight'];
        $distance = $shipment['distance'];

        if ($type === "ESTANDAR") {
            return 5 + ($distance * 0.10) + ($weight * 0.50);
        } else if ($type === "EXPRESS") {
            $cost = 10 + ($distance * 0.20) + ($weight * 0.80);
            if ($weight > 10) {
                $cost *= 1.1;
            }
            return $cost;
        } else if ($type === "INTERNACIONAL") {
            $cost = 25 + ($distance * 0.50) + ($weight * 1.00);
            if ($distance > 3000) {
                $cost += $cost * 0.2;
            }
            return $cost;
        } else if ($type === "FRAGIL") {
            $cost = 8 + ($distance * 0.15) + ($weight * 0.70);
            if ($weight > 5) {
                $cost += 5;
            }
            return $cost;
        }

        return 0;
    }

    private function determinarPrioridad($shipment) {
        $type = $shipment['type'];
        $days = $shipment['deliveryDays'];

        if (($type === "EXPRESS" || $type === "INTERNACIONAL") && $days <= 2) {
            return "ALTA";
        } elseif (($type === "ESTANDAR" || $type === "FRAGIL") && $days <= 3) {
            return "MEDIA";
        }

        return "BAJA";
    }

    private function generarEtiqueta($shipment, $cost, $priority) {
        return $shipment['id'] . " - " . $shipment['customer'] . "\n"
            . "Entregar en: " . $shipment['address'] . "\n"
            . "Tipo: " . $shipment['type'] . " - Precio: EUR" . round($cost, 2) . "\n"
            . "Prioridad: " . $priority;
    }
}