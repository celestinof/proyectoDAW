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
     * Registra el pedido, los detalles y actualiza el stock usando Transacciones.
     */
    public function procesarCheckout($carrito) {
        try {
            // INICIAMOS LA TRANSACCIÓN (Punto clave para la defensa)
            $this->db->beginTransaction();

            // 1. Calculamos el total del pedido desde el carrito
            // ACTUALIZADO: Lo desglosamos en base, iva y final para que encaje perfecto en tu BBDD
            $totalBase = 0;
            $totalIva = 0;
            $totalFinal = 0;
            
            foreach ($carrito as $item) {
                $subtotalBase = $item['precio_base'] * $item['cantidad'];
                $subtotalIva = $subtotalBase * 0.21; // Calculamos el 21% de IVA
                
                $totalBase += $subtotalBase;
                $totalIva += $subtotalIva;
                $totalFinal += ($subtotalBase + $subtotalIva);
            }

            // Comprobamos si el que compra es el administrador (logueado) o un visitante (null)
            $usuario_id = $_SESSION['usuario_id'] ?? null;

            // 2. Insertamos el pedido general (asumimos fecha automática en la BD)
            // ACTUALIZADO: Insertamos en las 4 columnas reales que vimos en tu phpMyAdmin
            $sqlPedido = "INSERT INTO pedidos (usuario_id, total_base_imponible, total_iva, total_final) 
                          VALUES (:usuario_id, :total_base, :total_iva, :total_final)";
            
            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtPedido->bindParam(':total_base', $totalBase, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_iva', $totalIva, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_final', $totalFinal, PDO::PARAM_STR);
            $stmtPedido->execute();

            // Capturamos el ID del pedido que se acaba de crear
            $idPedido = $this->db->lastInsertId();

            // 3. Preparamos las consultas para los detalles y el stock
            // ACTUALIZADO: Adaptado a las columnas "captura" de tu diseño de BBDD
            $sqlDetalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario_captura, iva_aplicado_captura) 
                           VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario_captura, :iva_aplicado_captura)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            $sqlStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :producto_id";
            $stmtStock = $this->db->prepare($sqlStock);

            // 4. Bucle para procesar cada artículo de la cesta
            foreach ($carrito as $item) {
                
                // Extraemos el precio base sin alterar y definimos el IVA para guardarlo en el historial
                $precioCaptura = $item['precio_base'];
                $ivaCaptura = 21.00;

                // Insertamos la línea del detalle del pedido
                $stmtDetalle->bindParam(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':precio_unitario_captura', $precioCaptura, PDO::PARAM_STR);
                $stmtDetalle->bindParam(':iva_aplicado_captura', $ivaCaptura, PDO::PARAM_STR);
                $stmtDetalle->execute();

                // Restamos el stock
                $stmtStock->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtStock->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtStock->execute();
            }

            // CONFIRMAMOS LA TRANSACCIÓN (Si llegamos aquí, todo ha ido bien)
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            // SI ALGO FALLA, DESHACEMOS TODO PARA NO CORROMPER LA BD
            $this->db->rollBack();
            die("Error crítico al procesar la compra: " . $e->getMessage());
        }
    }
/**
     * Obtiene el historial de pedidos de un cliente específico
     */
    public function obtenerPedidosPorUsuario($usuario_id) {
        try {
            // Buscamos los pedidos de este usuario ordenados por fecha (los más nuevos primero)
            // Asumimos que tienes una columna de fecha en tu tabla 'pedidos'
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
            // Hacemos un JOIN con la tabla de usuarios para que el admin pueda ver el nombre y email del cliente
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
     * Actualiza el estado de un pedido (Ej: De 'Pendiente' a 'Enviado')
     * Asumiendo que has creado una columna 'estado' en la tabla 'pedidos'
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
}
?>