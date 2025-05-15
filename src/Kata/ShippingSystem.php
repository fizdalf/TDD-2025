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
            return (new InternacionalCalculator())->calcular($shipment);
        } else if ($type === "FRAGIL") {
            return (new FragilCalculator())->calcular($shipment);
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