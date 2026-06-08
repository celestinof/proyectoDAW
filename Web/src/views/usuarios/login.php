<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Identificación</h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="index.php?controller=Usuario&action=autenticar" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Correo Electrónico:</label>
                                <input type="email" name="email" class="form-control" required placeholder="tu@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            <div class="text-end mt-1">
                                <a href="index.php?controller=Usuario&action=recuperar" class="text-decoration-none text-muted small">¿Olvidaste tu contraseña?</a>
                            </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Entrar al Sistema</button>
                            
                            <hr>
                            
                            <div class="text-center mt-3">
                                <p class="mb-2">¿No tienes cuenta para comprar?</p>
                                <a href="index.php?controller=Usuario&action=registro" class="btn btn-outline-success w-100 mb-2">Regístrate aquí</a>
                            </div>

                            <a href="index.php" class="btn btn-outline-secondary w-100">Volver al catálogo</a>
                            
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>