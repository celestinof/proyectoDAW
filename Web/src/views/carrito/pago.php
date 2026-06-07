<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-dark text-white text-center py-3">
                        <h4 class="mb-0">Finalizar Pedido y Pago Seguro</h4>
                    </div>
                    <div class="card-body p-5">
                        
                        <div class="alert alert-success text-center mb-4">
                            <h5>Total a cobrar: <strong><?= number_format($totalPagar, 2, ',', '.') ?> €</strong></h5>
                            <small>(IVA incluido)</small>
                        </div>

                        <form action="index.php?controller=Carrito&action=procesar" method="POST">
                            
                            <div class="mb-4 p-3 bg-light border border-primary rounded">
                                <label class="form-label fw-bold text-primary">📍 Dirección de Envío para este pedido:</label>
                                <input type="text" name="direccion_envio" class="form-control" required placeholder="Ej: Calle Gran Vía 1, 3ºB, 28013, Madrid">
                                <small class="text-muted">Indica la dirección completa donde quieres recibir tu artesanía.</small>
                            </div>
                            <hr class="my-4">
                            <h5 class="mb-3 fw-bold text-secondary">💳 Datos de Pago</h5>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre en la Tarjeta</label>
                                <input type="text" class="form-control" required placeholder="Ej: JUAN PÉREZ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Tarjeta</label>
                                <input type="text" class="form-control" required pattern="\d{16}" placeholder="1234567890123456" title="Debe contener 16 dígitos">
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Caducidad (MM/AA)</label>
                                    <input type="text" class="form-control" required pattern="\d{2}/\d{2}" placeholder="12/28">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">CVV</label>
                                    <input type="text" class="form-control" required pattern="\d{3}" placeholder="123">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Confirmar Pedido y Pagar</button>
                            <a href="index.php?controller=Carrito&action=ver" class="btn btn-link w-100 text-center mt-3 text-muted">Cancelar y volver al carrito</a>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">🔒 Tus datos están encriptados de forma segura.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>