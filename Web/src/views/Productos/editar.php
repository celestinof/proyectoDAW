<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h2 class="mb-0">Editar Artículo: <?= htmlspecialchars($producto['nombre']) ?></h2>
        </div>
        <div class="card-body">
            <form id="formEditarProducto" action="index.php?controller=Producto&action=actualizar" method="POST" enctype="multipart/form-data">                    
                <input type="hidden" name="id" value="<?= $producto['id'] ?>">

                <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['imagen']) ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del producto:</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Categoría ID:</label>
                    <input type="number" name="categoria_id" class="form-control" value="<?= $producto['categoria_id'] ?>" required>
                </div>

                <div class="mb-3 p-3 bg-light border rounded">
                    <label class="form-label fw-bold text-primary">¿Admite Personalización (Grabado láser)?</label>
                    <select name="es_personalizable" class="form-select border-primary" required>
                        <option value="1" <?= $producto['es_personalizable'] == 1 ? 'selected' : '' ?>>Sí, solicitar texto al cliente (Personalizado)</option>
                        <option value="0" <?= $producto['es_personalizable'] == 0 ? 'selected' : '' ?>>No, es un producto estándar</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción:</label>
                    <textarea name="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                </div>

                <div class="mb-3 p-3 border rounded bg-white">
                    <label class="form-label fw-bold">Cambiar Fotografía (Opcional):</label>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="img/productos/<?= htmlspecialchars($producto['imagen']) ?>" alt="Foto actual" style="width: 60px; height: 60px; object-fit: cover;" class="rounded border">
                        <small class="text-muted">Si no seleccionas ningún archivo, se mantendrá esta imagen.</small>
                    </div>
                    <input type="file" name="imagen_producto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Precio Base (€):</label>
                        <input type="number" name="precio_base" class="form-control" step="0.01" value="<?= $producto['precio_base'] ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stock:</label>
                        <input type="number" name="stock" class="form-control" value="<?= $producto['stock'] ?>" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?controller=Producto&action=index" class="btn btn-secondary">Cancelar</a>
                    
                    <button type="submit" class="btn btn-warning text-dark fw-bold">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>