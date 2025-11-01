<?php
/**
 * Clase base para controladores.
 */
class Controller
{
    /**
     * Renderiza una vista usando los layouts comunes.
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../view/' . str_replace('.', '/', $view) . '.php';
        $header = __DIR__ . '/../view/layouts/header.php';
        $footer = __DIR__ . '/../view/layouts/footer.php';

        if (file_exists($header)) {
            require $header;
        }

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<p>Vista no encontrada: ' . htmlspecialchars($viewFile, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        if (file_exists($footer)) {
            require $footer;
        }
    }

    /**
     * Redirige a una URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}