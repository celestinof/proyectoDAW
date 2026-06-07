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


        /**
     * Registra un nuevo usuario en la base de datos (con rol 'cliente' por defecto)
     */
    public function registrar($nombre, $email, $password) {
        try {
            // Encriptamos la contraseña de forma segura 
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


    /**
     * Busca un usuario por su email para comprobar el login
     */
    public function buscarPorEmail($email) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve los datos del usuario o false
        } catch (PDOException $e) {
            die("Error en la base de datos: " . $e->getMessage());
        }
    }
}
?>