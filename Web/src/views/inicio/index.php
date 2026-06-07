<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">La Magia de la Madera, en tus Manos</h1>
            <p class="lead mb-5 fs-4">Descubre piezas únicas, talladas a mano con pasión y dedicación por maestros artesanos. Dale un toque natural y exclusivo a tu hogar.</p>
            
            <a href="index.php?controller=Producto&action=index" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow">
                Explorar la Tienda
            </a>
        </div>
    </header>

    <section class="container py-5 mt-4">
        <div class="row text-center">
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <h1 class="mb-3">🌳</h1>
                        <h4 class="fw-bold text-success">Madera Sostenible</h4>
                        <p class="text-muted">Trabajamos exclusivamente con maderas de origen ético y sostenible. Cuidamos el medio ambiente en cada creación.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <h1 class="mb-3">🔨</h1>
                        <h4 class="fw-bold text-success">Elaboración 100% Artesanal</h4>
                        <p class="text-muted">Olvídate de la producción en masa. Cada llavero, figura o mueble es tallado, lijado y barnizado a mano pieza a pieza.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <h1 class="mb-3">🚚</h1>
                        <h4 class="fw-bold text-success">Envío Seguro</h4>
                        <p class="text-muted">Embalamos tus piezas con el máximo cuidado para que la madera llegue en perfectas condiciones a la puerta de tu casa.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> ArtesanosEnLinea - Proyecto DAW. Todos los derechos reservados.</p>
        </div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>