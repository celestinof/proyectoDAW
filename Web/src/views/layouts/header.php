<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Artesanos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4 p-3">
        <div class="container d-flex justify-content-between align-items-center">
            
            <a class="navbar-brand fw-bold fs-4" href="index.php">ArtesanosEnLinea</a>
            
            <div class="d-flex align-items-center">
                
                <a href="#" class="btn btn-outline-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#modalContacto">
                    📞 Contacto
                </a>

                <a href="index.php?controller=Carrito&action=ver" class="btn btn-success me-3 fw-bold">
                    🛒 Mi Carrito
                </a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    
                    <a href="index.php?controller=Pedido&action=misPedidos" class="btn btn-info fw-bold me-3">
                        📦 Mis Pedidos
                    </a>
                    
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                        <a href="index.php?controller=Pedido&action=gestionAdmin" class="btn btn-warning fw-bold me-3">
                            ⚙️ Gestión Pedidos
                        </a>
                        <span class="text-warning fw-bold me-3">Hola, Administrador</span>
                    <?php else: ?>
                        <span class="text-white me-3">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                    <?php endif; ?>

                    <a href="index.php?controller=Usuario&action=logout" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
                
                <?php else: ?>
                    <a href="index.php?controller=Usuario&action=login" class="btn btn-outline-light btn-sm">Acceso / Registro</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="modalContacto" tabindex="-1" aria-labelledby="modalContactoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalContactoLabel">Información de Contacto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark px-4 py-4">
                    <p class="mb-4">Si deseas contactar con nosotros, recuerda que estamos ubicados en:</p>
                    
                    <div class="p-3 bg-light border rounded mb-3">
                        <address class="mb-0">
                            <strong>ArtesanosEnLinea</strong><br>
                            C/ Alegría nº 20 bajo 1<br>
                            32100 Ourense<br>
                            España
                        </address>
                    </div>
                    
                    <p class="mb-2">📞 <strong>Teléfono:</strong> 988 20 20 20</p>
                    <p class="mb-0">✉️ <strong>Correo electrónico:</strong> <a href="mailto:artesanosenlinea@gmail.com">artesanosenlinea@gmail.com</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>