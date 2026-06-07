<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-secondary">📦 Historial de mis pedidos</h2>
        
        <?php if (!empty($pedidos)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4 py-3">Nº Pedido</th>
                                    <th class="py-3">Fecha de Compra</th>
                                    <th class="py-3">Dirección de Envío</th>
                                    <th class="py-3">Total (IVA inc.)</th>
                                    <th class="py-3 text-center" style="width: 250px;">Estado del Envío</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">
                                            #<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?><br>
                                            <button class="btn btn-sm btn-outline-secondary mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#detalles-<?= $pedido['id'] ?>" aria-expanded="false">
                                                Ver artículos 🔽
                                            </button>
                                        </td>
                                        
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($pedido['fecha'] ?? $pedido['fecha_pedido'] ?? 'now')) ?>
                                        </td>

                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> 
                                                <?= !empty($pedido['direccion_envio']) ? htmlspecialchars($pedido['direccion_envio']) : 'No registrada (Pedido antiguo)' ?>
                                            </small>
                                        </td>
                                        
                                        <td class="text-success fw-bold">
                                            <?= number_format($pedido['total_final'], 2, ',', '.') ?> €
                                        </td>
                                        
                                        <td class="text-center py-3">
                                            <?php 
                                            $estado = $pedido['estado'] ?? 'pendiente';
                                            $badgeClass = 'bg-secondary';
                                            if ($estado === 'pendiente') $badgeClass = 'bg-warning text-dark';
                                            if ($estado === 'pagado') $badgeClass = 'bg-info text-dark';
                                            if ($estado === 'enviado') $badgeClass = 'bg-success';
                                            if ($estado === 'cancelado') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 text-uppercase mb-2 d-inline-block w-100">
                                                <?= htmlspecialchars($estado) ?>
                                            </span>

                                            <?php if ($estado === 'pendiente' || $estado === 'pagado'): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-1" data-bs-toggle="modal" data-bs-target="#modalCancelar">
                                                    ❌ Solicitar Cancelación
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <tr class="collapse bg-light" id="detalles-<?= $pedido['id'] ?>">
                                        <td colspan="5" class="p-4 border-bottom">
                                            <h6 class="text-secondary fw-bold mb-3">Contenido del paquete:</h6>
                                            <ul class="list-group list-group-flush border rounded">
                                                <?php foreach ($pedido['detalles'] as $detalle): ?>
                                                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-secondary rounded-pill me-2"><?= $detalle['cantidad'] ?>x</span>
                                                            <span class="fw-bold"><?= htmlspecialchars($detalle['nombre']) ?></span>
                                                        </div>
                                                        <span class="text-muted"><?= number_format($detalle['precio_unitario_captura'], 2, ',', '.') ?> € / ud</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5 shadow-sm border-0">
                <h4 class="mb-3">Aún no has realizado ninguna compra.</h4>
                <p class="mb-4">Anímate a explorar nuestro catálogo y encuentra piezas únicas talladas a mano.</p>
                <a href="index.php" class="btn btn-primary px-4 py-2 fw-bold">Ir al catálogo de artesanías</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalCancelarLabel">Cancelar Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark px-4 py-4">
                    <p class="fs-5">Para cancelar su pedido, no dude en ponerse en contacto con nosotros lo antes posible indicando su <strong>número de pedido</strong>.</p>
                    <hr>
                    <p class="mb-2">📞 <strong>Teléfono:</strong> 988 20 20 20</p>
                    <p class="mb-0">✉️ <strong>Correo electrónico:</strong> <a href="mailto:artesanosenlinea@gmail.com">artesanosenlinea@gmail.com</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar y volver</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>