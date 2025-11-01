<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';

/**
 * Controlador de confirmación de entregas.
 *
 * Renderiza comprobantes/confirmaciones de entrega.
 */
class ConfirmController extends Controller
{
    /**
     * Muestra la confirmación de entrega con datos por defecto o recibidos.
     *
     * @return void
     */
    public function order(): void
    {
        $entrega = [
            'fecha' => date('d/m/Y'),
            'hora' => date('H:i'),
            'donante' => 'Supermercado El Ahorro',
            'receptor' => 'Comedor Comunitario Las Flores',
        ];

        $productos = [
            '5 kg de arroz',
            '3 kg de porotos',
            '10 paquetes de pasta',
            '2 kg de azucar',
        ];

        $this->render('confirm.order_confirmation', compact('entrega', 'productos'));
    }
}

