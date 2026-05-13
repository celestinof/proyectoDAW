<?php
namespace App\Controllers;

use App\Models\Producto;

class ProductoController {

    public function index() {
        // 1. Instanciar el modelo Producto
        $producto = new Producto();

        // 2. Llamar al método listarTodo() y guardar el resultado en una variable llamada $productos
        $productos=$producto->listarTodo();

        
        // 3. Cargar la vista. Al cargarla aquí, automáticamente tiene acceso a $productos
        require_once '../src/views/productos/index.php';
    }

}