<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header text-center bg-success text-white">
                        <h3>Crear Cuenta de Cliente</h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="index.php?controller=Usuario&action=guardarUsuario" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Completo:</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Correo Electrónico:</label>
                                <input type="email" name="email" class="form-control" required placeholder="tu@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                           </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            

                            <button type="submit" class="btn btn-success w-100">Registrarme</button>
                            <a href="index.php?controller=Usuario&action=login" class="btn btn-outline-secondary w-100 mt-2">Ya tengo cuenta</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>