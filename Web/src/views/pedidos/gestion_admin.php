<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-warning fw-bold">⚙️ Panel de Gestión de Pedidos</h2>
        
        <?php if (!empty($pedidos)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4 py-3">Nº Pedido</th>
                                    <th class="py-3">Datos del Cliente</th>
                                    <th class="py-3">Fecha</th>
                                    <th class="py-3">Total</th>
                                    <th class="py-3 text-center">Gestión de Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            #<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        
                                        <td>
                                            <span class="fw-bold d-block"><?= htmlspecialchars($pedido['cliente_nombre'] ?? 'Usuario Borrado') ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($pedido['cliente_email'] ?? '') ?></small>
                                        </td>
                                        
                                        <td><?= date('d/m/Y', strtotime($pedido['fecha'] ?? 'now')) ?></td>
                                        
                                        <td class="text-success fw-bold">
                                            <?= number_format($pedido['total_final'], 2, ',', '.') ?> €
                                        </td>
                                        
                                        <td class="text-center">
                                            <form action="index.php?controller=Pedido&action=cambiarEstado" method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                                
                                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                                
                                                <?php $estadoActual = $pedido['estado'] ?? 'pendiente'; ?>
                                                    <select name="estado" class="form-select form-select-sm border-primary" style="width: auto;">
                                                        <option value="pendiente" <?= $estadoActual === 'pendiente' ? 'selected' : '' ?>>🟡 Pendiente de pago</option>
                                                        <option value="pagado" <?= $estadoActual === 'pagado' ? 'selected' : '' ?>>🔵 Pagado (En taller)</option>
                                                        <option value="enviado" <?= $estadoActual === 'enviado' ? 'selected' : '' ?>>🟢 Enviado</option>
                                                        <option value="cancelado" <?= $estadoActual === 'cancelado' ? 'selected' : '' ?>>🔴 Cancelado</option>
                                                    </select>
                                                
                                                <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold">Guardar</button>
                                            </form>
                                        </td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary text-center py-5">
                <h4>No hay ningún pedido registrado en el sistema todavía.</h4>
            </div>
        <?php endif; ?>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>