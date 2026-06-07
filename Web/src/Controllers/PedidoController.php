<?php
namespace App\Controllers;

use App\Models\Pedido;

class PedidoController {

    /**
     * Muestra el historial de compras del cliente
     */
    public function misPedidos() {
        // 1. Verificamos que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        // 2. Llamamos al modelo para buscar sus pedidos
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->obtenerPedidosPorUsuario($_SESSION['usuario_id']);

        // 3. Cargamos la vista pasándole la variable $pedidos
        require_once __DIR__ . '/../views/pedidos/mis_pedidos.php';
    }

    /**
     * Muestra el panel de gestión para el Administrador
     */
    public function gestionAdmin() {
        // Doble control de seguridad (además del que ya pusimos en index.php)
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
            header("Location: index.php");
            exit();
        }

        $pedidoModel = new Pedido();
        // El admin necesita ver TODOS los pedidos de la tienda
        $pedidos = $pedidoModel->obtenerTodosLosPedidos();

        require_once __DIR__ . '/../views/pedidos/gestion_admin.php';
    }

    /**
     * Actualiza el estado de envío de un pedido (Solo Admin)
     */
    public function cambiarEstado() {
        // Solo aceptamos peticiones por formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = $_POST['pedido_id'] ?? null;
            $nuevo_estado = $_POST['estado'] ?? null;

            if ($pedido_id && $nuevo_estado) {
                $pedidoModel = new Pedido();
                $pedidoModel->actualizarEstado($pedido_id, $nuevo_estado);
            }
        }
        
        // Tras actualizar, devolvemos al admin a la pantalla de gestión
        header("Location: index.php?controller=Pedido&action=gestionAdmin");
        exit();
    }
}