<?php
namespace App\Controllers;

use App\Models\Producto;
use App\Models\Pedido; 

class CarritoController {

    // El constructor se asegura de que el carrito exista en la sesión al instanciar la clase
    public function __construct() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    /**
     * Añade un producto al carrito
     */
    public function agregar() {
        // 1. CORRECCIÓN: Buscamos el ID en POST (formulario) y si no, en GET (enlace)
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        // 2. NUEVO: Capturamos el texto de personalización (si lo hay)
        $notas = trim($_POST['notas_personalizacion'] ?? '');
        
        if ($id) {
            $productoModel = new Producto();
            $producto = $productoModel->obtenerPorId($id);

            if ($producto) {
                // 3. NUEVO: Creamos una clave única para la sesión combinando el ID y un hash del texto.
                // Ej: Si no hay texto será "5". Si hay texto será "5_a1b2c3d4"
                $cartKey = $id . ($notas !== '' ? '_' . md5($notas) : '');

                // Si esa combinación exacta ya está en el carrito, sumamos 1 a la cantidad
                if (isset($_SESSION['carrito'][$cartKey])) {
                    $_SESSION['carrito'][$cartKey]['cantidad'] += 1;
                } else {
                    // Si es la primera vez que lo añade, lo metemos con cantidad 1
                    $_SESSION['carrito'][$cartKey] = [
                        'id'          => $producto['id'], // ID real de la BBDD
                        'nombre'      => $producto['nombre'],
                        'precio_base' => $producto['precio_base'],
                        'cantidad'    => 1,
                        'notas'       => $notas,
                        'cartKey'     => $cartKey // Guardamos esta clave para los botones de + y -
                    ];
                }
            }
        }
        
        // Redirigimos a la vista del carrito
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Muestra la pantalla con los productos del carrito
     */
    public function ver() {
        // Pasamos el array del carrito a la vista
        $carrito = $_SESSION['carrito'];
        require_once '../src/views/carrito/ver.php';
    }

    /**
     * Vacía el carrito por completo
     */
    public function vaciar() {
        unset($_SESSION['carrito']); // Destruye la variable del carrito
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Llama al modelo Pedido para guardar la compra y vacía el carrito
     */
    public function procesar() {
        if (!empty($_SESSION['carrito'])) {
            $pedidoModel = new Pedido();

            // 1. NUEVO: Atrapamos la dirección que viene del formulario de pago
            $direccion_envio = $_POST['direccion_envio'] ?? 'Dirección no proporcionada';

            // 2. NUEVO: Le pasamos AMBAS cosas al modelo (el carrito y la dirección)
            if ($pedidoModel->procesarCheckout($_SESSION['carrito'], $direccion_envio)) {
                // Si la BD lo guarda bien, vaciamos el carrito de la memoria RAM
                unset($_SESSION['carrito']);

                // Redirigimos a la pantalla de mis pedidos (mucho más elegante que el mensaje suelto)
                header("Location: index.php?controller=Pedido&action=misPedidos");
                exit();
            }
        } else {
            header("Location: index.php?controller=Carrito&action=ver");
            exit();
        }
    }

    /**
     * Muestra la pasarela de pago ficticia
     */
    public function pagar() {
        // Comprobamos si el carrito tiene productos
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?controller=Carrito&action=ver");
            exit();
        }

        // Obligamos a que el usuario esté registrado y logueado para poder pagar
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        // Calculamos el total rápido para mostrárselo en el botón de pago
        $totalPagar = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $totalPagar += ($item['precio_base'] * 1.21) * $item['cantidad'];
        }

        require_once '../src/views/carrito/pago.php';
    }

    /**
     * Suma 1 a la cantidad de un producto en el carrito
     */
    public function sumar() {
        // Ahora el id que recibimos es la clave única (ej: "5" o "5_a1b2c3d4")
        $cartKey = $_GET['id'] ?? null;
        if ($cartKey && isset($_SESSION['carrito'][$cartKey])) {
            $_SESSION['carrito'][$cartKey]['cantidad'] += 1;
        }
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Resta 1 a la cantidad. Si llega a 0, elimina el producto del carrito.
     */
    public function restar() {
        $cartKey = $_GET['id'] ?? null;
        if ($cartKey && isset($_SESSION['carrito'][$cartKey])) {
            $_SESSION['carrito'][$cartKey]['cantidad'] -= 1;
            
            // Si al restar nos quedamos a 0, lo borramos del carrito
            if ($_SESSION['carrito'][$cartKey]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$cartKey]);
            }
        }
        header("Location: index.php?controller=Carrito&action=ver");
        exit();
    }

    /**
     * Elimina completamente un producto del carrito, sin importar la cantidad
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