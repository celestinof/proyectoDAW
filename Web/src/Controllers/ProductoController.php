<?php
namespace App\Controllers;

use App\Models\Producto;

class ProductoController {

    public function index() {
        // 1. Instanciar el modelo Producto
        $producto = new Producto();

        // 2. Llamar al método listarTodo() y guardar el resultado en una variable llamada $productos
        $productos=$producto->listarTodo();
       
        // 3. Cargar la vista. Llevamos lo que obtenemos del método listartodo para pintarla en index.php
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
            
            // Recogemos los datos del formulario en un array limpiando con trim.
            $datos = [
                'nombre'       => trim($_POST['nombre']),
                'categoria_id' => $_POST['categoria_id'],
                'descripcion'  => trim($_POST['descripcion']),
                'precio_base'  => $_POST['precio_base'],
                'stock'        => $_POST['stock']
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

        /**
     * Actualiza un producto existente UPDATE
     */
    public function actualizar($datos) {
        try {
            $sql = "UPDATE productos SET 
                    categoria_id = :categoria_id, 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    precio_base = :precio_base, 
                    stock = :stock 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':categoria_id', $datos['categoria_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre'], \PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], \PDO::PARAM_STR);
            $stmt->bindParam(':precio_base', $datos['precio_base'], \PDO::PARAM_STR);
            $stmt->bindParam(':stock', $datos['stock'], \PDO::PARAM_INT);
            $stmt->bindParam(':id', $datos['id'], \PDO::PARAM_INT); // El ID es clave para saber cuál actualizar

            return $stmt->execute();
        } catch (\PDOException $e) {
            die("Error al actualizar: " . $e->getMessage());
        }
    }

}