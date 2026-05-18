<?php
namespace App\Models;
use App\Config\Conexion; //Llamamos para usar conexion
use PDO; //Para usar la funcion global de PDO y que no la busque en mis clases

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
         * Función que recupera todos los objetos de la base de datos, por su id
         */
        public function listarTodo(){

        //Preparamos la consulta SQL y la guardamos en "statement"
        $stmt=$this->db->prepare("SELECT * FROM productos");

        //Ejecutamos la consulta SQL
        $stmt->execute();
             
        //Recuperamos y retornamos el array. Pedimos que solo nos muestre el nombre de las columnas (array asociativo)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio_base, iva_porcentaje, stock) 
                    VALUES (:categoria_id, :nombre, :descripcion, :precio_base, 21.00, :stock)";
            
            // 2. Preparamos la sentencia usando la conexión que ya teníamos en el constructor
            $stmt = $this->conexion->prepare($sql);
            
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

            // 4. EJECUCIÓN DE LA CONSULTA
            $resultado = $stmt->execute();
            
            return $resultado; // Devuelve true si funcionó

        } catch (PDOException $e) {
            // Si hay un error, lo mostramos
            die("Error al insertar el producto: " . $e->getMessage());
        }
    }

}




