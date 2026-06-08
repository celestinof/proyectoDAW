<?php
namespace App\Controllers;

use App\Models\Pedido;

class PedidoController {

    // Carga la pantalla de "Mis Pedidos" con el historial del cliente
    public function misPedidos() {
        
        // Echamos a los curiosos que intenten entrar directamente por la URL sin loguearse
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        $pedidoModel = new Pedido();
        
        // Pillamos todos los pedidos generales de este usuario en concreto
        $pedidos = $pedidoModel->obtenerPedidosPorUsuario($_SESSION['usuario_id']);

        // Ojo aquí: uso la referencia (&) para modificar el array original y meterle 
        // los productos (detalles) dentro de cada pedido. Así la vista lo tiene más fácil para pintar.
        foreach ($pedidos as &$pedido) {
            $pedido['detalles'] = $pedidoModel->obtenerDetallesPorPedido($pedido['id']);
        }
        
        // Esto me dio un dolor de cabeza brutal... Rompemos la referencia del foreach 
        // para evitar bugs raros y que no salga duplicado el último pedido en la pantalla.
        unset($pedido);

        require_once __DIR__ . '/../views/pedidos/mis_pedidos.php';
    }

    // Pantalla del administrador para ver las ventas y cambiar estados
    public function gestionAdmin() {
        
        // Por si acaso alguien se cuela, doble check de que tiene el rol de admin de verdad 
        // (aunque el index.php ya debería haberlo parado)
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
            header("Location: index.php");
            exit();
        }

        $pedidoModel = new Pedido();
        
        // Aquí no filtramos por ID, el admin lo ve absolutamente todo
        $pedidos = $pedidoModel->obtenerTodosLosPedidos();

        require_once __DIR__ . '/../views/pedidos/gestion_admin.php';
    }

    // Función para cambiar si el pedido está "Pendiente", "Enviado", etc.
    public function cambiarEstado() {
        
        // Nos aseguramos de que los datos vengan del botón del formulario (POST) 
        // y no de alguien toqueteando variables en la URL
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = $_POST['pedido_id'] ?? null;
            $nuevo_estado = $_POST['estado'] ?? null;

            if ($pedido_id && $nuevo_estado) {
                $pedidoModel = new Pedido();
                $pedidoModel->actualizarEstado($pedido_id, $nuevo_estado);
            }
        }
        
        // Recargamos la página de gestión para que el admin vea el cambio de estado al instante
        header("Location: index.php?controller=Pedido&action=gestionAdmin");
        exit();
    }
}