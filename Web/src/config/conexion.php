<?php
namespace App\Config; // Indicamos dónde vive este archivo para el Autoload, y olvidarnos de usar require_once, include... etc

//Para que no haya conflicto y php acceda a las clases globales, hacemos.
use PDO;
use PDOException;

class Conexion {

//Tal y como aplicamos en el módulo de DWES, dentro de una clase
    private $host = "localhost";
    private $db_name = "artesanos_db";
    private $user = "root"; 
    private $password = ""; 

// 2. Creamos una función que podremos llamar desde los Controladores
    public function conectar() {

    //construimos el dsn para la conexion
    $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";

/*Intentamos conectar a la BBDD con un try/catch para evitar que se cuelgue si no hay conexión.
Recordar que PDO no devuelve errores (se queda callado)*/

try {

$conexion=new PDO($dsn,$this->user,$this->password);

//Configuramos los errores de PDO para poder manejar errores
$conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//Si funciona, devolvemos la conexion
return $conexion;

}
//Si hay errores, lo capturamos en una excepción que encerramos en una variable $e
catch (PDOException $e){

    //Si no se conecta, la conexión muere, pero vemos un aviso
    die("Error al conectarse a la base de datos".$e->getMessage());
}

}
}
?>