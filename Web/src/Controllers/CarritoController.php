<?php
namespace App\Controllers;

use App\Models\Producto;
use App\Models\Pedido; 

class CarritoController {

    // Si es la primera vez que el usuario hace algo con el carrito en esta sesión, lo inicializamos vacío
    public function __construct() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    /**
     * Añade un producto al carrito
     */
    public function agregar() {
        // A veces el ID viene por el formulario (POST) y otras por un enlace directo (GET). Pillo el que llegue.
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        // Atrapamos el texto por si el cliente quiere algún grabado en la madera o personalización
        $notas = trim($_POST['notas_personalizacion'] ?? '');
        
        if ($id) {
            $productoModel = new Producto();
            $producto = $productoModel->obtenerPorId($id);

            if ($producto) {
                // Truco clave: Como un mismo producto puede pedirse con distintas personalizaciones, 
                // creo una clave combinada usando el ID y un hash del texto. Así no se pisan en el carrito.
                // Ej: Si no hay texto será "5". Si hay texto será "5_a1b2c3d4"
                $cartKey = $id . ($notas !== '' ? '_' . md5($notas) : '');

                // Si esa combinación exacta ya la metió antes, solo le sumamos 1 a la cantidad
                if (isset($_SESSION['carrito'][$cartKey])) {
                    $_SESSION['carrito'][$cartKey]['cantidad'] += 1;
                } else {
                    // Si es la primera vez que lo añade con esa configuración, lo metemos como nuevo
                    $_SESSION['carrito'][$cartKey] = [
                        'id'          => $producto['id'], // El ID real que irá a la base de datos
                        'nombre'      => $producto['nombre'],
                        'precio_base' => $producto['precio_base'],
                        'cantidad'    => 1,
                        'notas'       => $notas,
                        'cartKey'     => $cartKey // Me guardo la clave rara para poder buscarlo luego en los botones de + y -
                    ];
                }
            }
        }
        
        // De vuelta a la vista del carrito
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Muestra la pantalla con los productos del carrito
     */
    public function ver() {
        // Volcamos lo que haya en la sesión a una variable para que la vista lo pinte más fácil
        $carrito = $_SESSION['carrito'];
        require_once '../src/views/carrito/ver.php';
    }

    /**
     * Vacía el carrito por completo de un plumazo
     */
    public function vaciar() {
        unset($_SESSION['carrito']); 
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Llama al modelo Pedido para guardar la compra definitiva
     */
    public function procesar() {
        if (!empty($_SESSION['carrito'])) {
            $pedidoModel = new Pedido();

            // Pillamos la dirección que rellenó en el formulario de pago
            $direccion_envio = $_POST['direccion_envio'] ?? 'Dirección no proporcionada';

            // Le mandamos todo al modelo para que haga la inserción en la BBDD
            if ($pedidoModel->procesarCheckout($_SESSION['carrito'], $direccion_envio)) {
                
                // Si la base de datos lo traga bien, ya podemos limpiar el carrito de la sesión
                unset($_SESSION['carrito']);

                // Lo mandamos a su historial de pedidos, queda mucho más limpio que un simple alert
                header("Location: index.php?controller=Pedido&action=misPedidos");
                exit();
            }
        } else {
            // Por si intentan forzar la URL de procesar con el carrito vacío
            header("Location: index.php?controller=Carrito&action=ver");
            exit();
        }
    }

    /**
     * Muestra la pasarela de pago (ficticia para el proyecto)
     */
    public function pagar() {
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?controller=Carrito&action=ver");
            exit();
        }

        // Control de seguridad vital: si no estás logueado, al login de cabeza. No se puede comprar como anónimo.
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        // Calculo rápido del total con IVA para pintarlo en el botón final
        $totalPagar = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $totalPagar += ($item['precio_base'] * 1.21) * $item['cantidad'];
        }

        require_once '../src/views/carrito/pago.php';
    }

    /**
     * Suma 1 a la cantidad de un producto concreto
     */
    public function sumar() {
        // Ojo, aquí recibimos la clave combinada (ej: "5_a1b2c3d4"), no el ID normal
        $cartKey = $_GET['id'] ?? null;
        if ($cartKey && isset($_SESSION['carrito'][$cartKey])) {
            $_SESSION['carrito'][$cartKey]['cantidad'] += 1;
        }
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Resta 1 a la cantidad. Si llega a 0, adiós producto.
     */
    public function restar() {
        $cartKey = $_GET['id'] ?? null;
        if ($cartKey && isset($_SESSION['carrito'][$cartKey])) {
            $_SESSION['carrito'][$cartKey]['cantidad'] -= 1;
            
            // Si al restar nos quedamos a 0, lo borramos de la sesión directamente
            if ($_SESSION['carrito'][$cartKey]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$cartKey]);
            }
        }
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Elimina el producto del carrito sin importar cuántos haya
     */
    public function eliminarArticulo() {
        $cartKey = $_GET['id'] ?? null;
        if ($cartKey && isset($_SESSION['carrito'][$cartKey])) {
            unset($_SESSION['carrito'][$cartKey]);
        }
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }
}