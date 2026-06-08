<?php
namespace App\Models;

use App\Config\Conexion; // Nos traemos nuestra clase de conexión
use PDO; // Fundamental para que PHP no intente buscar la clase PDO dentro de la carpeta Models
use PDOException; 

class Producto {

    // Variable para guardarnos la conexión a la base de datos y no tener que abrirla en cada función
    private $db;
    private $arrayProductos; 

    public function __construct() {
        // Arrancamos la conexión nada más instanciar el modelo
        $conexion = new Conexion();
        $this->db = $conexion->conectar();   
    }

    // Saca el catálogo entero. 
    // Truco: Le metí el "WHERE 1=1" de base para poder ir concatenando los "AND" de los filtros 
    // sin tener que comerme la cabeza comprobando si es la primera condición o no.
    public function listarTodo($categoria_id = null, $tipo = null) {
        
        $sql = "SELECT * FROM productos WHERE 1=1";
        
        // Si el usuario pinchó en alguna categoría concreta, añadimos el filtro
        if ($categoria_id) {
            $sql .= " AND categoria_id = :categoria_id";
        }
        
        // Si usó los botones para ver solo los que admiten grabado/personalización
        if ($tipo === 'personalizable') {
            $sql .= " AND es_personalizable = 1";
        } elseif ($tipo === 'estandar') {
            $sql .= " AND es_personalizable = 0";
        }

        $stmt = $this->db->prepare($sql);

        // Ojo, solo bindeamos la categoría si realmente nos llegó el parámetro
        if ($categoria_id) {
            $stmt->bindParam(':categoria_id', $categoria_id, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // Recibe el array de datos del ProductoController y hace el INSERT
    public function crear($datos) {
        try {
            // Montamos la consulta con marcadores (:campo) para blindarnos contra inyecciones SQL.
            // Nota: Le clavo el 21.00 de IVA directamente a pelo porque en esta tienda todos los artículos tienen el mismo.
            $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio_base, iva_porcentaje, stock, imagen, es_personalizable) 
                    VALUES (:categoria_id, :nombre, :descripcion, :precio_base, 21.00, :stock, :imagen, :es_personalizable)";
            
            $stmt = $this->db->prepare($sql);
            
            // Vamos enchufando cada dato que nos pasó el controlador a su marcador correspondiente
            $stmt->bindParam(':categoria_id', $datos['categoria_id']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':precio_base', $datos['precio_base']);
            $stmt->bindParam(':stock', $datos['stock']);
            $stmt->bindParam(':imagen', $datos['imagen']);
            $stmt->bindParam(':es_personalizable', $datos['es_personalizable']);

            return $stmt->execute();

        } catch (PDOException $e) {
            // Chivatazo por si falla algún campo o restricción de la base de datos
            die("Error al insertar el producto: " . $e->getMessage());
        }
    }


    // Busca la ficha de un único artículo
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM productos WHERE id=:id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // Me di cuenta de que aquí tenía que usar fetch() normal en vez de fetchAll(), 
            // porque estoy buscando por ID (Primary Key) y solo va a devolver un registro como máximo.
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error al buscar el producto: " . $e->getMessage());
        }
    }


    // Borra el producto. Cuidado porque si tiene pedidos asociados podría dar fallo de integridad referencial.
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


    // Pisa los datos viejos con los nuevos en la base de datos
    public function actualizar($datos) {
        try {
            // SQL EN UNA SOLA LÍNEA. Me pasé un buen rato peleando con un error de sintaxis por 
            // hacer saltos de línea y tener una coma fantasma, así que lo dejé todo junto para evitar problemas.
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