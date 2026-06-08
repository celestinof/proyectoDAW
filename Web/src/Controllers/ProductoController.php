<?php
namespace App\Controllers;

use App\Models\Producto;

class ProductoController {

    public function index() {
        // Pillamos los filtros de la URL si el usuario ha pinchado en el menú
        $categoria_id = $_GET['categoria'] ?? null;
        $tipo = $_GET['tipo'] ?? null; // Para filtrar si quieren ver solo los que admiten grabado/personalización
        
        $productoModel = new \App\Models\Producto();
        
        // Le pasamos los dos filtros al modelo. Si vienen a null, el modelo ya sabe que tiene que sacar todo el catálogo.
        $productos = $productoModel->listarTodo($categoria_id, $tipo);
        
        require_once '../src/views/productos/index.php';
    }


    // Pinta el formulario HTML en blanco para dar de alta un producto nuevo. Nada de lógica de BBDD aquí.
    public function crear() {
        require_once '../src/views/productos/crear.php';
    }

    // Recibe los datos del formulario de crear y los manda a la base de datos
    public function guardar() {
        // Frenazo a los curiosos: nos aseguramos de que los datos vienen de darle al botón del formulario (POST)
        // y no de alguien escribiendo variables a mano en la barra de direcciones.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // --- TEMA FOTOS ---
            $nombreImagen = 'default.jpg'; // Si el artesano no sube foto, metemos esta para que no quede el hueco roto en la web
            
            // Comprobamos si nos llega archivo físico y si la subida temporal de PHP fue bien
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                
                $carpetaDestino = '../public/img/productos/';
                $nombreOriginal = basename($_FILES['imagen_producto']['name']);
                
                // Truco: Le meto un time() delante al nombre para que si suben dos fotos que se llamen "mesa.jpg" 
                // no se machaquen entre ellas en el servidor.
                $nombreImagen = time() . '_' . $nombreOriginal; 
                
                $rutaFinal = $carpetaDestino . $nombreImagen;
                
                // Movemos el archivo de la memoria temporal a nuestra carpeta de imágenes
                move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaFinal);
            }

            // Recogemos todo lo que viene del formulario. Le paso el 'trim' a los textos por si 
            // se les escapó algún espacio en blanco al principio o al final.
            $datos = [
                'nombre'       => trim($_POST['nombre']),
                'categoria_id' => $_POST['categoria_id'],
                'es_personalizable' => $_POST['es_personalizable'],
                'descripcion'  => trim($_POST['descripcion']),
                'precio_base'  => $_POST['precio_base'],
                'stock'        => $_POST['stock'],
                'imagen'       => $nombreImagen // Le enchufamos el nombre de la foto que acabamos de procesar arriba
            ];

            $productoModel = new Producto();

            // Mandamos el paquete de datos al modelo para que haga el INSERT INTO
            if ($productoModel->crear($datos)) {
                // Si la base de datos se lo traga, volvemos al catálogo
                header("Location: index.php?controller=Producto&action=index");
                exit(); // Cortamos de raíz para que no siga ejecutando código
            } else {
                echo "Error: No se pudo guardar el producto en la base de datos.";
            }
        } else {
            echo "Error: Acceso denegado. Este método solo acepta peticiones POST.";
        }
    }


    // Sirve para ver la ficha detallada de un solo producto
    public function ver(){
        // Pillamos el ID de la URL. Le pongo el '?? null' porque si alguien borra el número de la URL sin querer, 
        // así evito que PHP me tire un Fatal Error por variable indefinida. Lo leí en un foro y salva de muchos sustos.
        $id = $_GET['id'] ?? null; 

        $productoModelo = new Producto();
        // Vamos a la BBDD a por los datos exactos de esta artesanía
        $producto = $productoModelo->obtenerPorId($id); 

        // Pintamos la vista con los datos cargados
        require_once '../src/views/productos/ver.php';
    }


    // El botón de la papelera nos manda aquí
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $productoModel = new Producto();
            $productoModel->eliminar($id); // Fuego purificador
        }
        
        // Borre o no borre (por si falló el ID), lo devolvemos al catálogo como si nada
        header("Location: index.php");
        exit();
    }

  
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Si el usuario no tocó el input de la foto, nos quedamos con la que ya tenía guardada en el hidden
            $nombreImagen = !empty($_POST['imagen_actual']) ? $_POST['imagen_actual'] : 'default.jpg';
            
            // 2. Pero si vemos que sube una foto NUEVA...
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                
                // Uso __DIR__ porque las rutas relativas me daban algunos problemas al pasar de XAMPP a Docker. 
                // Así obligo a que la ruta sea absoluta desde la raíz del server.
                $carpetaDestino = __DIR__ . '/../../public/img/productos/';
                
                $nombreOriginal = basename($_FILES['imagen_producto']['name']);
                $nombreSinEspacios = str_replace(' ', '_', $nombreOriginal); // Quito espacios porque a veces dan bugs raros en Linux
                $nombreImagen = time() . '_' . $nombreSinEspacios; 
                $rutaFinal = $carpetaDestino . $nombreImagen;
                
                // Guardamos la foto nueva
                move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaFinal);
                
                // 3. LIMPIEZA DE LA FOTO VIEJA
                $imagenAntigua = $_POST['imagen_actual'] ?? '';
                
                // Ojo aquí: hay que comprobar que no esté vacía, que no sea la default (para no borrarla para todos) 
                // y sobre todo usar is_file para no intentar hacer un unlink de un directorio por error.
                if ($imagenAntigua !== '' && $imagenAntigua !== 'default.jpg' && is_file($carpetaDestino . $imagenAntigua)) {
                    unlink($carpetaDestino . $imagenAntigua); 
                }
            }

            // Montamos el array con todo actualizado. 
            // Cuidado con el checkbox de personalizable, si viene destildado $_POST no lo manda, así que le fuerzo un 1 por defecto.
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


    // Trae los datos actuales del producto de la BBDD para rellenar los inputs del formulario de edición
    public function editar() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $productoModel = new \App\Models\Producto();
            $producto = $productoModel->obtenerPorId($id); 
            
            // Si el ID de la URL coincide con un producto real, cargamos la vista pasándole el array de datos
            if ($producto) {
                require_once '../src/views/productos/editar.php';
            } else {
                echo "Error: El producto no existe.";
            }

        } else {
            // Si alguien intenta entrar a /editar a pelo en la URL sin pasar un ID, lo echamos al catálogo
            header("Location: index.php");
            exit();
        }
    }

}
?>