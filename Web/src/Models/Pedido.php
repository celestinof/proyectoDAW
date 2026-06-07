<?php
namespace App\Models;

use App\Config\Conexion;
use PDO;
use PDOException;

class Pedido {
    private $db;

    public function __construct(){
        $conexion = new Conexion();
        $this->db = $conexion->conectar();   
    }

    /**
     * Registra el pedido, los detalles, la dirección y actualiza el stock usando Transacciones.
     */
    public function procesarCheckout($carrito, $direccion_envio) {
        try {
            $this->db->beginTransaction();

            $totalBase = 0;
            $totalIva = 0;
            $totalFinal = 0;
            
            foreach ($carrito as $item) {
                $subtotalBase = $item['precio_base'] * $item['cantidad'];
                $subtotalIva = $subtotalBase * 0.21; 
                
                $totalBase += $subtotalBase;
                $totalIva += $subtotalIva;
                $totalFinal += ($subtotalBase + $subtotalIva);
            }

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            $sqlPedido = "INSERT INTO pedidos (usuario_id, direccion_envio, total_base_imponible, total_iva, total_final) 
                          VALUES (:usuario_id, :direccion_envio, :total_base, :total_iva, :total_final)";
            
            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtPedido->bindParam(':direccion_envio', $direccion_envio, PDO::PARAM_STR); 
            $stmtPedido->bindParam(':total_base', $totalBase, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_iva', $totalIva, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_final', $totalFinal, PDO::PARAM_STR);
            $stmtPedido->execute();

            $idPedido = $this->db->lastInsertId();

            $sqlDetalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario_captura, iva_aplicado_captura) 
                           VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario_captura, :iva_aplicado_captura)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            $sqlStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :producto_id";
            $stmtStock = $this->db->prepare($sqlStock);

            foreach ($carrito as $item) {
                $precioCaptura = $item['precio_base'];
                $ivaCaptura = 21.00;

                $stmtDetalle->bindParam(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':precio_unitario_captura', $precioCaptura, PDO::PARAM_STR);
                $stmtDetalle->bindParam(':iva_aplicado_captura', $ivaCaptura, PDO::PARAM_STR);
                $stmtDetalle->execute();

                $stmtStock->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtStock->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtStock->execute();
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            die("Error crítico al procesar la compra: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el historial de pedidos de un cliente específico
     */
    public function obtenerPedidosPorUsuario($usuario_id) {
        try {
            $sql = "SELECT * FROM pedidos WHERE usuario_id = :usuario_id ORDER BY id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener los pedidos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene TODOS los pedidos de la tienda (Para el panel del Administrador)
     */
    public function obtenerTodosLosPedidos() {
        try {
            $sql = "SELECT p.*, u.nombre AS cliente_nombre, u.email AS cliente_email 
                    FROM pedidos p 
                    JOIN usuarios u ON p.usuario_id = u.id 
                    ORDER BY p.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener todos los pedidos: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el estado de un pedido (Ej: De 'pendiente' a 'enviado')
     */
    public function actualizarEstado($pedido_id, $nuevo_estado) {
        try {
            $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $pedido_id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al cambiar el estado: " . $e->getMessage());
        }
    }

    /**
     * Obtiene los productos (detalles) de un pedido concreto
     */
    public function obtenerDetallesPorPedido($pedido_id) {
        try {
            $sql = "SELECT dp.*, p.nombre 
                    FROM detalles_pedido dp 
                    JOIN productos p ON dp.producto_id = p.id 
                    WHERE dp.pedido_id = :pedido_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener los detalles del pedido: " . $e->getMessage());
        }
    }
}
?>