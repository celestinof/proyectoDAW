<?php
// Arrancamos la sesión para el login y el carrito. (Siempre al principio)
session_start();

// Este index.php funciona como FRONTCONTROLLER
// 1. Cargamos el Autoloader
require_once '../vendor/autoload.php';

// 2. Capturar el Controlador y la Acción de la URL
$nombreControlador = $_GET['controller'] ?? 'Inicio';
$accion = $_GET['action'] ?? 'index';

// ==========================================
// 3. BLOQUE DE SEGURIDAD (Control de Acceso por Roles)
// ==========================================
$accionesProtegidas = ['crear', 'guardar', 'editar', 'actualizar', 'eliminar'];

if (in_array($accion, $accionesProtegidas)) {
    // Comprobamos de forma segura si la variable de sesión existe Y si su valor es 'admin'
    $esAdmin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
    
    // Si no es administrador, lo mandamos al login
    if (!$esAdmin) {
        header("Location: index.php?controller=Usuario&action=login");
        exit();
    }
}
// ==========================================

// 4. Instanciamos el controlador de forma DINÁMICA
$claseControlador = "App\\Controllers\\" . $nombreControlador . "Controller";

if (class_exists($claseControlador)) {
    
    $controlador = new $claseControlador();

    // 5. Ejecutamos la acción dinámicamente
    if (method_exists($controlador, $accion)) {
        $controlador->$accion();
    } else {
        echo "<h1>Error 404</h1><p>La acción [$accion] no existe en el controlador.</p>";
    }

} else {
    echo "<h1>Error 404</h1><p>El controlador [$claseControlador] no existe.</p>";
}