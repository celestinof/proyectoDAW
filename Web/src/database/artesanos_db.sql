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
    FOREIGN KEY (produc to_id) REFERENCES productos(id) ON DELETE SET NULL -- Si borro un producto del catálogo, no se borra del pedido. 
                                                                          -- Simplemente pasa a estar NULL en detalles pedido, pero sigue existiendo en pedido.
);