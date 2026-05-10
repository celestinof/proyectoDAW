-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS artesanos_db; -- Creamos la BBDD si no existe.
USE artesanos_db;

-- 1. Tabla de Categorías (Cuadros, Llaveros, etc.)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Autoincrementamos para que cada categoría tenga un id distinto
    nombre VARCHAR(100) NOT NULL, -- Nombre de la categoría necesario. Descripción opcional
    descripcion TEXT
); 

-- 2. Tabla de Productos (Los productos concretamente grabados)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Lo mismo que en categorias
    categoria_id INT, -- clave de categorías, se referencia al final
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    -- Para precio y para IVA empleo "DECIMAL" en vez de "FLOAT" por que leyendo en un foro de substack sonbre la creación de una tienda ecommerce
    -- Encontré que FLOAT puede dar problemas de redondeo.
    precio_base DECIMAL(10,2) NOT NULL, -- Precio sin IVA, con hasta dos decimales. Máximo 8 enteros y 2 decimales.
    iva_porcentaje DECIMAL(5,2) DEFAULT 21.00, -- IVA. En defecto 21%, que es el que suelen tener esos productos.
    stock INT DEFAULT 0,
    imagen VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- 3. Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL, -- Unique por que un mismo email no puede dar lugar a múltiples usuarios.
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente', 
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Esta no se me había ocurrido, y lo vi posteriormente en una guía. Me parece interesante.
);

-- 4. Tabla de Pedidos (Cabecera)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- fecha en el que se hace el pedido
    total_base_imponible DECIMAL(10,2), -- Suma de precios_base
    total_iva DECIMAL(10,2),           -- Suma de los IVAs
    total_final DECIMAL(10,2),         -- El importe real pagado
    estado ENUM('pendiente', 'pagado', 'enviado', 'cancelado') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE -- Si se borra un usuario, desaparecen sus pedidos. 
                                                                       -- Sin esta parte, no se podría borrar un usuario si tiene pedidos.
); 

-- 5. Tabla de Detalles del Pedido (Lo que antes era lineas_pedido)
CREATE TABLE detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario_captura DECIMAL(10,2) NOT NULL, -- Guardamos el precio del momento
    iva_aplicado_captura DECIMAL(5,2) NOT NULL,    -- Guardamos el IVA del momento
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE, -- Si borro un pedido, borra todos los detalles de pedido
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL -- Si borro un producto del catálogo, no se borra del pedido. 
                                                                          -- Simplemente pasa a estar NULL en detalles pedido, pero sigue existiendo en pedido.
);


-- Insertando algunos datos (los pedí a una IA generativa por que Fazinotto me daba problemas, y la verdad intenté no perder tiempo escribiéndolos)
-- 1. Insertando en categorias
INSERT INTO categorias (nombre, descripcion) VALUES
('Llaveros', 'Llaveros artesanales personalizados en madera y metal'),
('Grabados', 'Cuadros y placas con grabado láser de alta precisión'),
('Joyería', 'Colgantes, pulseras y anillos hechos a mano');

-- 2. Insertando en productos (Ajustado a: precio_base)
INSERT INTO productos (categoria_id, nombre, descripcion, precio_base, stock) VALUES
(1, 'Llavero Personalizado Madera', 'Llavero de roble con nombre grabado a doble cara', 12.50, 50),
(1, 'Llavero Acero Inoxidable', 'Llavero resistente con forma de corazón y fecha grabada', 15.00, 30),
(1, 'Llavero de Cuero', 'Llavero de cuero trenzado con iniciales', 9.90, 45),
(2, 'Retrato Grabado Láser', 'Retrato familiar grabado en madera de pino 20x20cm', 45.00, 10),
(2, 'Placa para Mascotas', 'Placa identificativa con el nombre y teléfono para collar', 9.99, 100),
(2, 'Cartel Boda Personalizado', 'Cartel de bienvenida para bodas en metacrilato', 65.00, 5),
(3, 'Colgante Árbol de la Vida', 'Colgante artesanal de plata y cuarzo', 25.00, 15),
(3, 'Pulsera Piedras Naturales', 'Pulsera elástica con piedras volcánicas', 18.50, 20);

-- 3. Insertando en usuarios
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin Tienda', 'admin@artesanos.com', '123456', 'admin'),
('Carlos García', 'carlos.garcia@gmail.com', '123456', 'cliente'),
('Laura Martínez', 'laura.martinez@hotmail.com', '123456', 'cliente'),
('Miguel Ángel', 'miguel.angel@yahoo.com', '123456', 'cliente');

-- 4. Insertando en pedidos (Ajustado a: fecha_pedido, totales desglosados)
INSERT INTO pedidos (usuario_id, fecha_pedido, total_base_imponible, total_iva, total_final, estado) VALUES
(2, '2023-10-01 10:30:00', 22.73, 4.77, 27.50, 'pagado'),
(3, '2023-10-05 16:45:00', 37.19, 7.81, 45.00, 'enviado'),
(4, '2023-10-10 09:15:00', 53.72, 11.28, 65.00, 'pendiente');

-- 5. Insertando en detalles_pedido (Añadido para completar la estructura)
INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario_captura, iva_aplicado_captura) VALUES
(1, 1, 1, 12.50, 21.00),
(1, 2, 1, 15.00, 21.00),
(2, 4, 1, 45.00, 21.00),
(3, 6, 1, 65.00, 21.00);