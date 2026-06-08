<?php
namespace App\Models;

use App\Config\Conexion;
use PDO;
use PDOException;

class Pedido {
    private $db;

    public function __construct(){
        // Instanciamos la conexión y nos la guardamos en la variable de la clase para usarla en todas las funciones
        $conexion = new Conexion();
        $this->db = $conexion->conectar();   
    }

    // Aquí es donde ocurre la magia gorda del carrito.
    // Meto todo en una Transacción: o se guarda el pedido, el detalle y se resta el stock de golpe, o no se guarda NADA.
    // Así evito que si la BBDD falla a la mitad, me quede con un pedido sin líneas o con stock fantasma.
    public function procesarCheckout($carrito, $direccion_envio) {
        try {
            // Arrancamos la transacción con PDO
            $this->db->beginTransaction();

            $totalBase = 0;
            $totalIva = 0;
            $totalFinal = 0;
            
            // Calculamos los totales a mano antes de insertar nada
            foreach ($carrito as $item) {
                $subtotalBase = $item['precio_base'] * $item['cantidad'];
                $subtotalIva = $subtotalBase * 0.21; // Le aplico el 21% de IVA directamente
                
                $totalBase += $subtotalBase;
                $totalIva += $subtotalIva;
                $totalFinal += ($subtotalBase + $subtotalIva);
            }

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            // 1. Creamos la "cabecera" del pedido
            $sqlPedido = "INSERT INTO pedidos (usuario_id, direccion_envio, total_base_imponible, total_iva, total_final) 
                          VALUES (:usuario_id, :direccion_envio, :total_base, :total_iva, :total_final)";
            
            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtPedido->bindParam(':direccion_envio', $direccion_envio, PDO::PARAM_STR); 
            $stmtPedido->bindParam(':total_base', $totalBase, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_iva', $totalIva, PDO::PARAM_STR);
            $stmtPedido->bindParam(':total_final', $totalFinal, PDO::PARAM_STR);
            $stmtPedido->execute();

            // Trucazo: pillo el ID autonumérico que le acaba de dar MySQL a este pedido para usarlo en las líneas de detalle
            $idPedido = $this->db->lastInsertId();

            // 2. Preparamos las consultas para los detalles y para restar el stock
            $sqlDetalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario_captura, iva_aplicado_captura) 
                           VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario_captura, :iva_aplicado_captura)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            $sqlStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :producto_id";
            $stmtStock = $this->db->prepare($sqlStock);

            // Bucle para ir guardando línea a línea lo que había en el carrito
            foreach ($carrito as $item) {
                // Guardo una "foto" del precio y el IVA en este momento exacto. 
                // Por si en el futuro el artesano sube los precios, que no cambie el historial de este ticket.
                $precioCaptura = $item['precio_base'];
                $ivaCaptura = 21.00;

                $stmtDetalle->bindParam(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':precio_unitario_captura', $precioCaptura, PDO::PARAM_STR);
                $stmtDetalle->bindParam(':iva_aplicado_captura', $ivaCaptura, PDO::PARAM_STR);
                $stmtDetalle->execute();

                // Le quitamos las unidades compradas al stock disponible
                $stmtStock->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtStock->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmtStock->execute();
            }

            // Si hemos llegado hasta aquí abajo sin que salte ningún error... confirmamos todos los cambios en la BBDD
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            // Si algo peta (falta un dato, se cae MySQL...), hacemos un RollBack para deshacer lo que se hubiera insertado a medias
            $this->db->rollBack();
            die("Error crítico al procesar la compra: " . $e->getMessage());
        }
    }


    // Saca la lista de compras de un cliente para su apartado de "Mis Pedidos".
    // Los ordeno por ID de forma descendente (DESC) para que le salgan primero los más recientes.
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


    // Esta es para el panel del Admin. 
    // Le meto un JOIN con usuarios para saber cómo se llama el que compró y su email, si no solo vería el usuario_id y es un lío.
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


    // Simplemente cambia la palabrita del estado en la base de datos (Ej: de 'pendiente' a 'enviado')
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


    // Saca las líneas de los artículos que hay dentro de un pedido.
    // Hago JOIN con la tabla productos para sacar el nombre del artículo y no mostrarle al cliente solo un número de ID feo.
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