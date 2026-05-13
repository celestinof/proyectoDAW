<?php
namespace App\Controllers;

use App\Models\Producto;

class ProductoController {

    public function index() {
        // 1. Instanciar el modelo Producto
        $producto = new Producto();

        // 2. Llamar al método listarTodo() y guardar el resultado en una variable llamada $productos
        $productos=$producto->listarTodo();

        // 3. print_r de $productos para ver que llegan los datos como prueba
        print_r($productos);
    }

}