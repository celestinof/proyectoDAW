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
            
            $password = $_POST['password']; 
            $confirm_password = $_POST['confirm_password'] ?? ''; 

            // Parche de última hora: Me di cuenta de que si el usuario tecleaba mal su clave
            // se iba a quedar bloqueado para siempre. Meto esta validación rápida.
            if ($password !== $confirm_password) {
                // Si no coinciden, lo devuelvo al registro pasándole un error por URL
                header("Location: index.php?controller=Usuario&action=registro&error=pass");
                exit();
            }

            $usuarioModel = new \App\Models\Usuario();
            
            // Nota mental para futuras versiones... (el resto sigue igual)
            if ($usuarioModel->registrar($nombre, $email, $password)) {
                header("Location: index.php?controller=Usuario&action=login&registro=ok");
                exit();
            }
        }
    }

    // Pinta un formulario sencillito pidiendo el email para recuperar la clave
    public function recuperar() {
        // Como no me daba tiempo a hacer una vista entera por que me di cuenta en los últimos testeos, uso un pequeño truco: 
        // Cargo la cabecera, pinto el HTML a pelo y cargo el footer. Queda resultón.
        require_once '../src/views/layouts/header.php';
        echo '
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-5 card p-4 shadow">
                    <h4 class="text-center">Recuperar Contraseña</h4>
                    <p class="text-muted text-center small">Introduce tu email y te enviaremos las instrucciones.</p>
                    <form action="index.php?controller=Usuario&action=procesarRecuperacion" method="POST">
                        <input type="email" name="email_recuperacion" class="form-control mb-3" required placeholder="tu@email.com">
                        <button type="submit" class="btn btn-primary w-100">✉️ Enviar enlace</button>
                    </form>
                </div>
            </div>
        </div>';
        require_once '../src/views/layouts/footer.php';
    }

    // Procesa (de mentira) la petición de recuperar contraseña
    public function procesarRecuperacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí en la vida real usaríamos PHPMailer para mandar un token de un solo uso.
            // Para la entrega del proyecto lo dejo simulado.
            // OJO: Por seguridad, siempre mostramos el mensaje de éxito, exista el correo en la BBDD o no.
            
            header("Location: index.php?controller=Usuario&action=login&recuperacion=enviada");
            exit();
        }
    }

}
?>