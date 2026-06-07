<?php
//Este controlador gestiona si la contraseña es correcta y arranca la sesión.
namespace App\Controllers;
use App\Models\Usuario;

class UsuarioController {

    // Muestra la pantalla del formulario de login
    public function login() {
        require_once '../src/views/usuarios/login.php';
    }

    // Procesa el formulario
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscarPorEmail($email);

            // Comprobamos si el usuario existe y si la contraseña es correcta
            // (Asume que en la BD guardaste la contraseña con password_hash())
            if ($usuario && password_verify($password, $usuario['password'])) {
                // ¡Login correcto! Guardamos datos en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol']; //Guardamos si es admin o cliente
                
                // Redirigimos al catálogo
                header("Location: index.php");
                exit();
            } else {
                echo "<h3>Error: Email o contraseña incorrectos.</h3>";
                echo "<a href='index.php?controller=Usuario&action=login'>Volver a intentar</a>";
            }
        }
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit();
    }


    // Muestra la vista de registro
    public function registro() {
        require_once '../src/views/usuarios/registro.php';
    }

    // Procesa el formulario de registro
    public function guardarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password']; // No se hace trim a la contraseña por si el usuario quiso usar espacios

            $usuarioModel = new \App\Models\Usuario();
            
            // Aquí en un proyecto real comprobaríamos si el email ya existe.
            if ($usuarioModel->registrar($nombre, $email, $password)) {
                // Redirigimos al login con un mensaje de éxito por URL (opcional)
                header("Location: index.php?controller=Usuario&action=login&registro=ok");
                exit();
            }
        }
    }

}
?>