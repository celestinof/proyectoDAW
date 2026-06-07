<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Catálogo de Productos</h1>
        
        <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
        <a href="index.php?controller=Producto&action=crear" class="btn btn-primary">Añadir Nuevo Producto</a>
        <?php endif; ?>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom">
        
        <a href="index.php?controller=Producto&action=index" class="btn <?= empty($_GET['tipo']) ? 'btn-dark' : 'btn-outline-dark' ?> fw-bold">
           🛒 Todos los artículos
        </a>

        <span class="border-start mx-2"></span> 
        
        <a href="index.php?controller=Producto&action=index&tipo=personalizable" class="btn <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'personalizable') ? 'btn-info text-dark' : 'btn-outline-info' ?> fw-bold">
           ✨ Regalos Personalizados
        </a>
        
        <a href="index.php?controller=Producto&action=index&tipo=estandar" class="btn <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'estandar') ? 'btn-secondary text-white' : 'btn-outline-secondary' ?> fw-bold">
           🎁 Regalos Estándar
        </a>
        
    </div>

    <?php if (!empty($productos)): ?>
        <div class="row"> 
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 mb-4"> 
                    
                    <div class="producto card shadow-sm h-100 border-0">
                        
                        <img src="img/productos/<?= htmlspecialchars($producto['imagen']) ?>" class="card-img-top rounded-top" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="height: 280px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            
                            <h4 class="card-title fw-bold mb-2"><?= htmlspecialchars($producto['nombre']) ?></h4>
                            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($producto['descripcion']) ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-5 fw-bold text-success"><?= $producto['precio_base'] ?> €</span>
                                <span class="badge bg-light text-dark border">Stock: <?= $producto['stock'] ?></span>
                            </div>
                            
                            <div class="mt-auto">
                                
                                <form action="index.php?controller=Carrito&action=agregar" method="POST">
                                    <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                                    
                                    <?php if (isset($producto['es_personalizable']) && $producto['es_personalizable'] == 1): ?>
                                        <div class="mb-3 bg-light p-2 border rounded">
                                            <label class="form-label text-primary fw-bold" style="font-size: 0.85rem;">📝 Personalización (Opcional):</label>
                                            <textarea name="notas_personalizacion" class="form-control form-control-sm" rows="2" placeholder="Ej: Grabar la fecha '12/05/2026'"></textarea>
                                            <small class="text-muted" style="font-size: 0.75rem;">Te contactaremos con un boceto antes de fabricarlo.</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <button type="submit" class="btn btn-success w-100 fw-bold mb-3">
                                        Añadir al Carrito
                                    </button>
                                </form>

                                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                                    <hr class="mt-0 mb-2"> 
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php?controller=Producto&action=editar&id=<?= $producto['id'] ?>" class="btn btn-warning btn-sm fw-bold">Editar</a>
                                        <a href="index.php?controller=Producto&action=eliminar&id=<?= $producto['id'] ?>" class="btn btn-danger btn-sm fw-bold" onclick="return confirm('¿Eliminar pieza?')">Borrar</a>
                                    </div>
                                <?php endif; ?>

                            </div> 
                        </div> 
                    </div> 
                </div> 
            <?php endforeach; ?> 
        </div> 
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>No hay productos disponibles en este momento.</h4>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>