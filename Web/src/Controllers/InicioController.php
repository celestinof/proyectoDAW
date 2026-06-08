<?php
namespace App\Controllers;

class InicioController {
    
    // Este es el controlador más simple del proyecto. 
    // Hace de "recibidor", solo escupe la vista de la portada (Landing Page) sin tener que tocar la base de datos.
    public function index() {
        require_once '../src/views/inicio/index.php';
    }
}
?>