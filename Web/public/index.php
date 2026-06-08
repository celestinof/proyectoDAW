<?php
// 1. Iniciamos sesión. Tiene que ir siempre arriba del todo para que el carrito y el login no den fallos de cabeceras.
session_start();

// FRONT CONTROLLER
// Todo pasa por aquí. Cargamos el Autoloader de Composer para no tener que andar haciendo 'require' en cada archivo.
require_once '../vendor/autoload.php';

// 2. Recogemos por GET a dónde quiere ir el usuario. 
// Si entran a la raíz sin parámetros, por defecto cargamos el controlador Inicio y la acción index.
$nombreControlador = $_GET['controller'] ?? 'Inicio';
$accion = $_GET['action'] ?? 'index';

// ==========================================
// 3. BLOQUE DE SEGURIDAD Y CONTROL DE ROLES
// ==========================================
// Hacemos una lista de las acciones que solo puede tocar el administrador (gestión del catálogo)
$accionesProtegidas = ['crear', 'guardar', 'editar', 'actualizar', 'eliminar'];

// Si la acción que piden por la URL está en la lista de arriba...
if (in_array($accion, $accionesProtegidas)) {
    // Comprobamos si el usuario está logueado y además tiene el rol de admin
    $esAdmin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
    
    // Si intenta entrar a la fuerza saltándose la seguridad, lo echamos al login directamente
    if (!$esAdmin) {
        header("Location: index.php?controller=Usuario&action=login");
        exit(); // Paramos la ejecución de golpe por seguridad
    }
}
// ==========================================

// 4. Instanciamos el controlador "al vuelo"
// Montamos la ruta de la clase aprovechando los namespaces de PHP
$claseControlador = "App\\Controllers\\" . $nombreControlador . "Controller";

// Comprobamos que el archivo/clase del controlador realmente exista antes de intentar usarlo
if (class_exists($claseControlador)) {
    
    $controlador = new $claseControlador();

    // 5. Ejecutamos la función (acción) que han pedido
    if (method_exists($controlador, $accion)) {
        $controlador->$accion();
    } else {
        // Si el controlador existe pero la función no, mostramos un error de manera amigable
        echo "<h1>Error 404</h1><p>Vaya, parece que la acción que buscas [$accion] no está disponible.</p>";
    }

} else {
    // Si directamente se inventan el nombre del controlador en la URL
    echo "<h1>Error 404</h1><p>No hemos encontrado la página que buscas. La ruta no es válida.</p>";
}