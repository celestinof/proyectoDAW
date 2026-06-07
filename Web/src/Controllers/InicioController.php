<?php
namespace App\Controllers;

class InicioController {
    /**
     * Muestra la Landing Page (Página principal de bienvenida)
     */
    public function index() {
        require_once '../src/views/inicio/index.php';
    }
}
?>