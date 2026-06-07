<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Tu Carrito de la Compra</h2>

        <?php if (!empty($carrito)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th>Precio Ud.</th>
                                <th class="text-center">Cantidad</th>
                                <th>Subtotal</th>
                                <th class="text-center">Quitar</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalPedido = 0;
                            // Recorremos los productos guardados en la sesión
                            foreach ($carrito as $item): 
                                $precioIva = $item['precio_base'] * 1.21;
                                $subtotal = $precioIva * $item['cantidad'];
                                $totalPedido += $subtotal;
                            ?>
                                <tr>
                                    <td class="fw-bold ps-3"><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td><?= number_format($precioIva, 2, ',', '.') ?> €</td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="index.php?controller=Carrito&action=restar&id=<?= $item['cartKey'] ?>" class="btn btn-outline-secondary px-2 fw-bold">-</a>
                                            
                                            <button type="button" class="btn btn-light border" disabled style="width: 45px; opacity: 1;">
                                                <?= $item['cantidad'] ?>
                                            </button>
                                            
                                            <a href="index.php?controller=Carrito&action=sumar&id=<?= $item['cartKey'] ?>" class="btn btn-outline-secondary px-2 fw-bold">+</a>
                                        </div>
                                    </td>
                                    
                                    <td class="text-success fw-bold"><?= number_format($subtotal, 2, ',', '.') ?> €</td>
                                    
                                    <td class="text-center">
                                        <a href="index.php?controller=Carrito&action=eliminarArticulo&id=<?= $item['cartKey'] ?>" class="btn btn-outline-danger btn-sm" title="Eliminar artículo">
                                            🗑️
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5 pe-3">TOTAL PEDIDO:</td>
                                <td class="fs-4 fw-bold text-primary"><?= number_format($totalPedido, 2, ',', '.') ?> €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-5">
                <a href="index.php" class="btn btn-outline-secondary">Seguir Comprando</a>
                
                <div>
                    <a href="index.php?controller=Carrito&action=vaciar" class="btn btn-danger me-2">Vaciar Carrito</a>
                    <a href="index.php?controller=Carrito&action=pagar" class="btn btn-success btn-lg">Finalizar Compra</a>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center py-5 shadow-sm">
                <h4>Tu carrito está vacío</h4>
                <p class="mb-4">¡Descubre nuestras artesanías de madera y añade productos a tu cesta!</p>
                <a href="index.php" class="btn btn-primary">Ver Catálogo</a>
            </div>
        <?php endif; ?>

    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>