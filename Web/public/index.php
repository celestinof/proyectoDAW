<?php
// Este index.php funciona como FRONTCONTROLLER para arrancar el autoload y redirigir a donde corresponda.
// 1. Cargamos el Autoloader
require_once '../vendor/autoload.php';

// 2. Importamos el Controlador
use App\Controllers\ProductoController;

// 3. Capturar la acción de la URL
// Le preguntamos a la URL qué función quiere ejecutar el usuario.
// Si el usuario no pone nada (entra a la web a secas), 
// usamos el operador ?? para decirle que por defecto ejecute 'index' (el catálogo) si no hay nada en $_GET.
$accion = $_GET['action'] ?? 'index';


// 4. Instanciamos el controlador y ejecutamos su método index
$controlador = new ProductoController();

// 5. Cambio del $controlador->index(); que tenía al ir comenzando, a $action para que sea dinámico.
$controlador->$accion();