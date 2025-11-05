<?php
/**
 * Clase base para controladores.
 */
class Controller
{
    /**
     * Renderiza una vista usando los layouts comunes u opcionales.
     *
     * @param string $view   Vista a renderizar utilizando la notación de puntos (ej: "auth.login").
     * @param array  $data   Datos que se extraerán como variables disponibles en la vista.
     * @param string $layout Identificador del layout a utilizar. Por defecto se carga el layout general.
     */
    protected function render(string $view, array $data = [], string $layout = 'default'): void
    {
        require_once __DIR__ . '/../core/auth_session.php';

        $baseViewDir = __DIR__ . '/../view/';
        $viewFile = $baseViewDir . str_replace('.', '/', $view) . '.php';

        $effectiveLayout = trim($layout);
        $header = $baseViewDir . 'layouts/header.php';
        $footer = $baseViewDir . 'layouts/footer.php';

        if ($effectiveLayout !== '' && $effectiveLayout !== 'default') {
            if ($effectiveLayout === 'none') {
                $header = null;
                $footer = null;
            } else {
                $customHeader = $baseViewDir . 'layouts/' . $effectiveLayout . '_header.php';
                $customFooter = $baseViewDir . 'layouts/' . $effectiveLayout . '_footer.php';

                if (file_exists($customHeader)) {
                    $header = $customHeader;
                }
                if (file_exists($customFooter)) {
                    $footer = $customFooter;
                }
            }
        }

        extract($data);

        if ($header !== null && file_exists($header)) {
            require $header;
        }

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<p>Vista no encontrada: ' . htmlspecialchars($viewFile, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        if ($footer !== null && file_exists($footer)) {
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
