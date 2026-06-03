<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!--Pendiente de incorporar JS-->
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h2 class="mb-0">Editar Artículo: <?= htmlspecialchars($producto['nombre']) ?></h2>
            </div>
            <div class="card-body">
                <form action="index.php?action=actualizar" method="POST">
                    
                    <input type="hidden" name="id" value="<?= $producto['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del producto:</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoría ID:</label>
                        <input type="number" name="categoria_id" class="form-control" value="<?= $producto['categoria_id'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción:</label>
                        <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio Base (€):</label>
                            <input type="number" name="precio_base" class="form-control" step="0.01" value="<?= $producto['precio_base'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Stock:</label>
                            <input type="number" name="stock" class="form-control" value="<?= $producto['stock'] ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning text-dark">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
