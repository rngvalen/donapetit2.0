<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';

class Infocontroller extends Controller
{
    /**
     * Muestra la página de contacto
     */
    public function contacto(): void
    {
        $this->render('info/contacto', [
            'title' => 'Contacto - DonAppétit'
        ]);
    }

    /**
     * Muestra la página de privacidad
     */
    public function privacidad(): void
    {
        $this->render('info/privacidad', [
            'title' => 'Política de Privacidad - DonAppétit'
        ]);
    }

    /**
     * Muestra la página de ayuda
     */
    public function ayuda(): void
    {
        $this->render('info/ayuda', [
            'title' => 'Ayuda - DonAppétit'
        ]);
    }
}