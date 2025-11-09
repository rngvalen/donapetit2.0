<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';

/**
 * Controlador para las paginas informativas (contacto, privacidad, ayuda).
 */
class InfoController extends Controller
{
    /**
     * Muestra la pagina de contacto.
     */
    public function contacto(): void
    {
        $this->render('info.contacto', [
            'title' => 'Contacto - DonAppetit',
        ]);
    }

    /**
     * Muestra la politica de privacidad.
     */
    public function privacidad(): void
    {
        $this->render('info.privacidad', [
            'title' => 'Privacidad - DonAppetit',
        ]);
    }

    /**
     * Muestra el centro de ayuda.
     */
    public function ayuda(): void
    {
        $this->render('info.ayuda', [
            'title' => 'Ayuda - DonAppetit',
        ]);
    }
}
