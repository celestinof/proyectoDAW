<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Artesanos</title>
  </head>
<body>

    <h1>Catálogo de Productos</h1>
<!--Nos traemos la lista de productos al index del controlador-->
    <?php if (!empty($productos)): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="producto">
                <h2><?= $producto['nombre'] ?></h2>
                <p><?= $producto['descripcion'] ?></p>
                <p><strong>Precio:</strong> <?= $producto['precio_base'] ?> €</p>
                <p><strong>Stock:</strong> <?= $producto['stock'] ?> unidades</p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>

</body>
</html>