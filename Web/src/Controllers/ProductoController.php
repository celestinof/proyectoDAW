<?php
namespace App\Controllers;

use App\Models\Producto;

class ProductoController {

    public function index() {
        $categoria_id = $_GET['categoria'] ?? null;
        $tipo = $_GET['tipo'] ?? null; // NUEVO: Capturamos si quieren ver personalizados
        
        $productoModel = new \App\Models\Producto();
        
        // Le pasamos AMBOS parámetros
        $productos = $productoModel->listarTodo($categoria_id, $tipo);
        
        require_once '../src/views/productos/index.php';
    }


    /**
     * La misión es traer el formulario de crear.php
     * cuando el enrutador reciba index.php?controller=Producto&action=crear,
     * llamará a esta función y el usuario verá el formulario en pantalla.
     */
    // 1. Método para mostrar el formulario de alta de un producto
    public function crear() {
        // Su única función es cargar el archivo de la vista donde está el formulario HTML
        require_once '../src/views/productos/crear.php';
    }

    /**
     * Método que guarda los datos insertados en el formulario de crear producto para insertarlo
     * en la BBDD. (De crear.php)
     */
    public function guardar() {
        // SEGURIDAD: Primero nos aseguramos de que los datos vienen por POST (para evitar que alguien intente meterse escribiendo cosas en la URL).
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // =================================================================
            // NUEVO: LÓGICA PARA PROCESAR LA SUBIDA DE LA FOTO DEL PRODUCTO
            // =================================================================
            $nombreImagen = 'default.jpg'; // Imagen por defecto si el usuario no sube ninguna foto
            
            // Comprobamos si nos ha llegado un archivo por $_FILES y si no ha habido errores en la subida temporal
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                // Definimos la carpeta física donde guardaremos las fotos
                $carpetaDestino = '../public/img/productos/';
                
                // Extraemos el nombre original del archivo subido
                $nombreOriginal = basename($_FILES['imagen_producto']['name']);
                
                // Generamos un nombre único añadiendo la marca de tiempo actual (time()) 
                // para evitar que dos fotos diferentes se llamen igual y se sobrescriban
                $nombreImagen = time() . '_' . $nombreOriginal; 
                
                $rutaFinal = $carpetaDestino . $nombreImagen;
                
                // Movemos el archivo de la memoria temporal de XAMPP a nuestra carpeta final
                move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaFinal);
            }
            // =================================================================

            // Recogemos los datos del formulario en un array limpiando con trim.
            $datos = [
                'nombre'       => trim($_POST['nombre']),
                'categoria_id' => $_POST['categoria_id'],
                'es_personalizable' => $_POST['es_personalizable'],
                'descripcion'  => trim($_POST['descripcion']),
                'precio_base'  => $_POST['precio_base'],
                'stock'        => $_POST['stock'],
                'imagen'       => $nombreImagen // <--- NUEVO: Añadimos el nombre de la foto que acabamos de procesar
            ];

            // Instanciamos el modelo Producto para comunicarnos con la base de datos
            $productoModel = new Producto();

            // Llamamos al método crear() del modelo (no confundir con el anterior) pasándole los datos.
            // (Este método del modelo será el encargado de hacer el INSERT INTO con PDO)
            if ($productoModel->crear($datos)) {
                // Si la base de datos lo guarda con éxito, redireccionamos al catálogo principal
                header("Location: index.php?controller=Producto&action=index");
                exit(); // Cortamos la ejecución aquí para asegurar la redirección
            } else {
                echo "Error: No se pudo guardar el producto en la base de datos.";
            }
        } else {
            //Si no llegó por POST
            echo "Error: Acceso denegado. Este método solo acepta peticiones POST.";
        }
    }


    /**
     * Función que sirve para ver el detalle de un producto elegido por el usuario
     */
    public function ver(){
        //Primero vamos a capturar el ID que viene en la URL, para saber que producto quiere ver el usuario
        $id = $_GET['id'] ?? null ; //Esta última parte, la vi en internet como consejo para evitar de que si hay algún error y no llega el id, no rompa la página

        //Creamos un objeto para hablar con la BBDD como en index(), pero llamando a método obtenerPorId()
        $productoModelo = new Producto();
        //Estamos usando el método con el $id que vino por $_GET
        $producto=$productoModelo->obtenerPorId($id); 

        //Una vez obtenemos los valores, llamamos a la página para pintar los datos
        require_once '../src/views/productos/ver.php';
    }

      /**
     * DELETE EN EL CRUD. Procesa la orden de borrar un producto
     */
    public function eliminar() {
        // Capturamos el ID
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $productoModel = new Producto();
            $productoModel->eliminar($id); // Borramos de la BBDD
        }
        
        // Pase lo que pase, redirigimos al catálogo
        header("Location: index.php");
        exit();
    }

  
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Cogemos la foto actual. Si por algún motivo viene vacía, le ponemos default.jpg
            $nombreImagen = !empty($_POST['imagen_actual']) ? $_POST['imagen_actual'] : 'default.jpg';
            
            // 2. Si el usuario sube una foto NUEVA
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                
                // Usamos __DIR__ para que la ruta sea absoluta y perfecta en Windows/XAMPP
                $carpetaDestino = __DIR__ . '/../../public/img/productos/';
                
                $nombreOriginal = basename($_FILES['imagen_producto']['name']);
                $nombreSinEspacios = str_replace(' ', '_', $nombreOriginal); // Quitamos espacios por seguridad
                $nombreImagen = time() . '_' . $nombreSinEspacios; 
                $rutaFinal = $carpetaDestino . $nombreImagen;
                
                // Movemos la foto nueva
                move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaFinal);
                
                // 3. BORRAR LA ANTIGUA (CORREGIDO)
                $imagenAntigua = $_POST['imagen_actual'] ?? '';
                
                // Verificamos que no esté vacía, que no sea la default, y ¡MUY IMPORTANTE! que sea un archivo (is_file)
                if ($imagenAntigua !== '' && $imagenAntigua !== 'default.jpg' && is_file($carpetaDestino . $imagenAntigua)) {
                    unlink($carpetaDestino . $imagenAntigua); 
                }
            }

            // Agrupamos todos los datos (Asegúrate de que 'es_personalizable' está capturado)
            $datos = [
                'id'                => $_POST['id'],
                'categoria_id'      => $_POST['categoria_id'], 
                'es_personalizable' => $_POST['es_personalizable'] ?? 1, 
                'nombre'            => trim($_POST['nombre']),
                'descripcion'       => trim($_POST['descripcion']), 
                'precio_base'       => $_POST['precio_base'],
                'stock'             => $_POST['stock'],
                'imagen'            => $nombreImagen 
            ];

            $productoModel = new \App\Models\Producto();
            
            if ($productoModel->actualizar($datos)) {
                header("Location: index.php?controller=Producto&action=index");
                exit();
            } else {
                echo "Error al intentar actualizar el producto en la base de datos.";
            }
        }
    }

    /**
     * Muestra el formulario relleno con los datos del producto a editar
     */
    public function editar() {
        // 1. Capturamos el ID de la URL
        $id = $_GET['id'] ?? null;

        if ($id) {
            // 2. Buscamos el producto en la BBDD
            $productoModel = new \App\Models\Producto();
            $producto = $productoModel->obtenerPorId($id); 
            
            // 3. Si existe, cargamos la vista pasándole los datos
            if ($producto) {
                require_once '../src/views/productos/editar.php';
            } else {
                echo "Error: El producto no existe.";
            }

            } else {
            // Si alguien intenta entrar sin pasar un ID, lo devolvemos al catálogo
            header("Location: index.php");
            exit();
        }
    }

}
?>