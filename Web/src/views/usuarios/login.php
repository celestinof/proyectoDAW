<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Artesanos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Identificación de Artesano</h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="index.php?controller=Usuario&action=autenticar" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Correo Electrónico:</label>
                                <input type="email" name="email" class="form-control" required placeholder="tu@email.com">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Contraseña:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Entrar al Sistema</button>
                            <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Volver al catálogo</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>