<?php
namespace App\Models;
use App\Config\Conexion;
use PDO;
use PDOException;

class Usuario {
    private $db;

    public function __construct(){
        $conexion = new Conexion();
        $this->db = $conexion->conectar();   
    }


    // Da de alta a un usuario nuevo en la BBDD.
    // Ojo: Le clavo el rol 'cliente' a fuego en la consulta SQL para evitar que algún listillo 
    // intente inyectar un rol 'admin' modificando el HTML del formulario en el navegador.
    public function registrar($nombre, $email, $password) {
        try {
            // Encriptamos la clave. Uso PASSWORD_DEFAULT en vez de poner BCRYPT directo porque 
            // leí que así PHP se encarga de usar el mejor algoritmo automáticamente si actualizamos el servidor.
            $hashSeguro = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, email, password, rol) 
                    VALUES (:nombre, :email, :password, 'cliente')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, \PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashSeguro, \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            die("Error al registrar: " . $e->getMessage());
        }
    }


    // Busca a un usuario por su email cuando intenta hacer login.
    public function buscarPorEmail($email) {
        try {
            // Le meto el LIMIT 1 a la consulta para rascar rendimiento: 
            // como los emails no se pueden repetir, en cuanto encuentre el suyo le digo a MySQL que pare de buscar.
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            // Fetch normal (no fetchAll) porque sabemos que como mucho va a devolver una sola fila
            return $stmt->fetch(PDO::FETCH_ASSOC); 
            
        } catch (PDOException $e) {
            die("Error en la base de datos al buscar usuario: " . $e->getMessage());
        }
    }
}
?>