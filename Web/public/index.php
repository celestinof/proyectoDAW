<?php
// 1. Cargamos el Autoloader
require_once '../vendor/autoload.php';

// 2. Traemos el modelo Producto (ya no necesitamos traer Conexion aquí, el Modelo lo hace por dentro)
use App\Models\Producto;

try {
    // 3. Instanciamos el modelo
    $modeloProducto = new Producto();
    
    // 4. Ejecutamos la función y guardamos el array de resultados
    $listaProductos = $modeloProducto->listarTodo();

    // 5. Imprimimos el array en pantalla de forma legible
    echo "<pre>";
    print_r($listaProductos);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2> Ups, algo falló:</h2> " . $e->getMessage();
}