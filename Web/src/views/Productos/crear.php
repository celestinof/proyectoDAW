<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="css/estilos.css">

<div class="container mt-5">
    
    <div class="row justify-content-center">
        
        <div class="col-md-8">
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Añadir Nuevo Artículo</h2>
                </div>
                
                <div class="card-body">
                    <form id="formCrearProducto" action="index.php?controller=Producto&action=guardar" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre del producto:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej. Llavero de roble">
                            <span id="errorNombre" class="error-js">El nombre es obligatorio y solo admite letras y espacios.</span>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label fw-bold">Categoría:</label>
                            <select id="categoria" name="categoria_id" class="form-select">
                                <option value="">-- Selecciona una --</option>
                                <option value="1">Llaveros</option>
                                <option value="2">Grabados</option>
                                <option value="3">Joyería</option>
                            </select>
                            <span id="errorCategoria" class="error-js">Debes seleccionar una categoría.</span>
                        </div>

                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-primary">¿Admite Personalización (Grabado láser)?</label>
                            <select name="es_personalizable" class="form-select border-primary" required>
                                <option value="1">Sí, solicitar texto al cliente (Personalizado)</option>
                                <option value="0">No, es un producto estándar</option>
                            </select>
                            <small class="text-muted">Si marcas "Sí", al cliente se le habilitará la caja de texto para pedir grabados.</small>
                         </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" placeholder="Describe los materiales y detalles..."></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="precio" class="form-label fw-bold">Precio Base (€):</label>
                                <input type="number" id="precio" name="precio_base" class="form-control" step="0.01" placeholder="0.00">
                                <span id="errorPrecio" class="error-js">Introduce un precio válido mayor a 0.</span>
                            </div>

                            <div class="col-md-6">
                                <label for="stock" class="form-label fw-bold">Stock inicial:</label>
                                <input type="number" id="stock" name="stock" class="form-control" min="0" value="0">
                            </div>
                        </div>

                        <label class="form-label fw-bold">Fotografía del Producto:</label>
                        <input type="file" name="imagen_producto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted">Formatos permitidos: JPG, PNG.</small>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php?controller=Producto&action=index" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" id="btnGuardar" class="btn btn-success">Guardar Producto</button>
                        </div>

                    </form>
                </div>
            </div> 
        </div>
    </div>
</div>

<script src="js/validaciones.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>