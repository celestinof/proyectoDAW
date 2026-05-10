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


}




