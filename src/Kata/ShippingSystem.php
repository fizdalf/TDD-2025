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

        if ($type === "ESTANDAR") {
            return (new EstandarCalculator())->calcular($shipment);
        } else if ($type === "EXPRESS") {
            return (new ExpressCalculator())->calcular($shipment);
        } else if ($type === "INTERNACIONAL") {
            $cost = 25 + ($shipment['distance'] * 0.50) + ($shipment['weight'] * 1.00);
            if ($shipment['distance'] > 3000) {
                $cost += $cost * 0.2;
            }
            return $cost;
        } else if ($type === "FRAGIL") {
            $cost = 8 + ($shipment['distance'] * 0.15) + ($shipment['weight'] * 0.70);
            if ($shipment['weight'] > 5) {
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