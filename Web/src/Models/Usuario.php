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