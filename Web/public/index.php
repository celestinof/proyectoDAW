<?php
// Este index.php funciona como FRONTCONTROLLER para arrancar el autoload y redirigir a donde corresponda.
// 1. Cargamos el Autoloader
require_once '../vendor/autoload.php';

// 2. Capturar el Controlador y la Acción de la URL
// Como he añadido más controladores, ahora pregunto qué Controlador quiere usar. Si no pone nada, por defecto será Producto.
$nombreControlador = $_GET['controller'] ?? 'Producto';

// Le preguntamos a la URL qué función (método) quiere ejecutar el usuario.
// Si el usuario no pone nada (entra a la web a secas), 
// usamos el operador ?? para decirle que por defecto ejecute 'index' (el catálogo) si no hay nada en $_GET.
$accion = $_GET['action'] ?? 'index';

// ==========================================
// 3. BLOQUE DE SEGURIDAD (Control de Acceso)
// ==========================================
// Definimos qué acciones son exclusivas del administrador.
$accionesProtegidas = ['crear', 'guardar', 'editar', 'actualizar', 'eliminar'];

// Si el usuario intenta ir a una acción protegida y NO existe la sesión de login ('usuario_id')
if (in_array($accion, $accionesProtegidas) && !isset($_SESSION['usuario_id'])) {
    // Lo expulsamos y lo mandamos a la pantalla de login (llamando al UsuarioController)
    header("Location: index.php?controller=Usuario&action=login");
    exit();
}
// ==========================================

// 4. Instanciamos el controlador de forma DINÁMICA
// Ya no usamos el "use" fijo arriba, sino que construimos el namespace completo según lo que pida la URL.
// Ejemplo: Si $nombreControlador es "Carrito", esto genera "App\Controllers\CarritoController"
$claseControlador = "App\\Controllers\\" . $nombreControlador . "Controller";

// Comprobamos por seguridad que la clase de ese controlador realmente exista en nuestros archivos
if (class_exists($claseControlador)) {
    
    // Instanciamos el controlador que toque en este momento
    $controlador = new $claseControlador();

    // 5. Cambio del $controlador->index(); que tenía al ir comenzando, a $accion para que sea dinámico.
    // Antes comprobamos que la función exista dentro de ese controlador para evitar cuelgues.
    if (method_exists($controlador, $accion)) {
        $controlador->$accion();
    } else {
        echo "<h1>Error 404</h1><p>La acción [$accion] no existe en el controlador.</p>";
    }

} else {
    echo "<h1>Error 404</h1><p>El controlador [$claseControlador] no existe.</p>";
}