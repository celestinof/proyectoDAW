<?php
// Controlador encargado de la seguridad básica: registro, login y control de sesiones.
namespace App\Controllers;
use App\Models\Usuario;

class UsuarioController {

    // Solo pinta el HTML del formulario para iniciar sesión
    public function login() {
        require_once '../src/views/usuarios/login.php';
    }

    // Aquí llega el envío del formulario de login
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscarPorEmail($email);

            // Comprobamos si el usuario existe y verificamos el hash. 
            // Ojo, nada de contraseñas en texto plano en la BBDD, por eso uso password_verify.
            if ($usuario && password_verify($password, $usuario['password'])) {
                
                // ¡Todo OK! Montamos las variables de sesión para que la web "recuerde" quién es
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol']; // Fundamental para saber si le enseñamos el panel de admin o no
                
                // Pa' dentro, lo mandamos al catálogo principal
                header("Location: index.php");
                exit();
            } else {
                // Mensaje rápido si falla. En un proyecto más grande esto iría con alertas de Bootstrap bonitas
                echo "<h3>Error: Email o contraseña incorrectos.</h3>";
                echo "<a href='index.php?controller=Usuario&action=login'>Volver a intentar</a>";
            }
        }
    }

    // Botón de Salir
    public function logout() {
        session_destroy(); // Destruimos todo (login, carrito, etc.)
        header("Location: index.php");
        exit();
    }


    // Pinta el HTML del formulario para darse de alta
    public function registro() {
        require_once '../src/views/usuarios/registro.php';
    }

    // Recoge los datos del nuevo cliente
    public function guardarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            
            // Cuidado aquí: NO le hago trim() a la contraseña a propósito. 
            // Si el usuario quiso poner espacios al principio o al final de su clave por manía, hay que respetarlo.
            $password = $_POST['password']; 

            $usuarioModel = new \App\Models\Usuario();
            
            // Nota mental para futuras versiones: Estaría bien meter aquí una consulta 
            // para avisar si el email ya está pillado antes de intentar registrarlo a lo bruto.
            if ($usuarioModel->registrar($nombre, $email, $password)) {
                // Lo mandamos al login pasándole un chivato por la URL para poder mostrarle un mensaje de éxito
                header("Location: index.php?controller=Usuario&action=login&registro=ok");
                exit();
            }
        }
    }

}
?>