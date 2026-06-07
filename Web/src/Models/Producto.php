<?php
namespace App\Models;
use App\Config\Conexion; //Llamamos para usar conexion
use PDO; //Para usar la funcion global de PDO y que no la busque en mis clases
use PDOException; // <--- Para los errores de PDO

class Producto{

//Creamos un atributo donde se almacenará la conexión
private $db;
private $arrayProductos; 

        /**
         * Constructor que crea un objeto de la clase conexion (en Config/Conexion.php)
         */
        public function __construct(){
            // Creamos un objeto (instancia) de conexion 
            $conexion=new Conexion();

            //Lanzamos la conexion y la almacenamos en el objeto PDO (si no la almacenamos, la conexion se pierde en la memoria)
            $this->db=$conexion->conectar();   
        }

        /**
         * Función que recupera los productos. Filtra por categoría y/o por tipo de personalización.
         */
        public function listarTodo($categoria_id = null, $tipo = null){
            
            // Empezamos la consulta base
            $sql = "SELECT * FROM productos WHERE 1=1";
            
            if ($categoria_id) {
                $sql .= " AND categoria_id = :categoria_id";
            }
            
            // Añadimos el filtro de personalización si nos lo piden
            if ($tipo === 'personalizable') {
                $sql .= " AND es_personalizable = 1";
            } elseif ($tipo === 'estandar') {
                $sql .= " AND es_personalizable = 0";
            }

            $stmt = $this->db->prepare($sql);

            if ($categoria_id) {
                $stmt->bindParam(':categoria_id', $categoria_id, \PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

            /**
         * Inserta un nuevo producto en la base de datos viniendo de "/views/productos/crear.php").
         * @param array $datos Array asociativo con los datos del formulario
         * @return boolean True si tuvo éxito, False si falló
         */
        public function crear($datos) {
        try {
            // 1. Preparamos la consulta SQL para vinvular con bindparam. Esto evita inyecciones SQL porque separa el código de los datos.
            // Nota: Asumimos un iva_porcentaje por defecto del 21.00 si no viene del formulario
            $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio_base, iva_porcentaje, stock, imagen, es_personalizable) 
                    VALUES (:categoria_id, :nombre, :descripcion, :precio_base, 21.00, :stock, :imagen, :es_personalizable)";
            
            // 2. Preparamos la sentencia usando la conexión que ya teníamos en el constructor
            $stmt = $this->db->prepare($sql);
            
           // 3. VINCULACIÓN DE PARÁMETROS (Binding)
            // Usamos bindParam() para asociar cada marcador de la consulta SQL con su variable real.

            //categoría
            $stmt->bindParam(':categoria_id', $datos['categoria_id']);
            
            //nombre
            $stmt->bindParam(':nombre', $datos['nombre']);
            
            //descripción
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
            //precio
            $stmt->bindParam(':precio_base', $datos['precio_base']);
            
            // stock
            $stmt->bindParam(':stock', $datos['stock']);

            // imagen
            $stmt->bindParam(':imagen', $datos['imagen']);

            // es_personalizable
            $stmt->bindParam(':es_personalizable', $datos['es_personalizable']);

            // 4. EJECUCIÓN DE LA CONSULTA y almacenar el resultado
            $resultado = $stmt->execute();
            
            return $resultado; // Devuelve true si funcionó

        } catch (PDOException $e) {
            // Si hay un error, lo mostramos
            die("Error al insertar el producto: " . $e->getMessage());
        }
    }

    /**
         * Intenta obtener un producto por su id y mostrar el producto "/views/productos/ver.php").
         * @param string $id identificador del producto
         * @return boolean True si tuvo éxito, False si falló
         */
    public function obtenerPorId($id){
        try {
        //Consulta para extraer los datos del producto por su id
        $sql="SELECT * FROM productos WHERE id=:id";

        //Preparamos la consulta
        $stmt=$this->db->prepare($sql);

        //vinculamos el id para la seguridad en la consulta
        $stmt->bindParam(":id",$id);

        // Ejecutamos la consulta en el motor de la base de datos
        $stmt->execute();

        // Retornamos el resultado. 
        // IMPORTANTE: Usamos fetch() en lugar de fetchAll() porque solo esperamos 
        // un único registro (el ID es clave primaria y es único).
        return $stmt->fetch(\PDO::FETCH_ASSOC);

        
        } catch (PDOException $e) {
            // Si hay un error, lo mostramos
            die("Error al insertar el producto: " . $e->getMessage());
        }



    }


    /**
     * Elimina un producto por su ID. DELETE EN EL CRUD
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            die("Error al eliminar el producto: " . $e->getMessage());
        }
    }


    /**
     * Actualiza un producto existente. update
     */
    public function actualizar($datos) {
        try {
            // SQL EN UNA SOLA LÍNEA para evitar errores de sintaxis y comas fantasma
            $sql = "UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, precio_base = :precio_base, stock = :stock, imagen = :imagen, es_personalizable = :es_personalizable WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':categoria_id', $datos['categoria_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre'], \PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], \PDO::PARAM_STR);
            $stmt->bindParam(':precio_base', $datos['precio_base'], \PDO::PARAM_STR);
            $stmt->bindParam(':stock', $datos['stock'], \PDO::PARAM_INT);
            $stmt->bindParam(':imagen', $datos['imagen'], \PDO::PARAM_STR);
            $stmt->bindParam(':es_personalizable', $datos['es_personalizable'], \PDO::PARAM_INT);
            $stmt->bindParam(':id', $datos['id'], \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            die("Error al actualizar el producto: " . $e->getMessage());
        }
    }
}




