<?php
// 1. Cargamos el Autoloader
require_once '../vendor/autoload.php';

// 2. Importamos el Controlador
use App\Controllers\ProductoController;

// 3. Instanciamos el controlador y ejecutamos su método index
$controlador = new ProductoController();
$controlador->index();