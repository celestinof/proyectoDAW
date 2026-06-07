-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2026 a las 17:01:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `artesanos_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Llaveros', 'Llaveros artesanales personalizados en madera y metal'),
(2, 'Grabados', 'Cuadros y placas con grabado láser de alta precisión'),
(3, 'Joyería', 'Colgantes, pulseras y anillos hechos a mano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pedido`
--

DROP TABLE IF EXISTS `detalles_pedido`;
CREATE TABLE `detalles_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario_captura` decimal(10,2) NOT NULL,
  `iva_aplicado_captura` decimal(5,2) NOT NULL,
  `notas_personalizacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_pedido`
--

INSERT INTO `detalles_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario_captura`, `iva_aplicado_captura`, `notas_personalizacion`) VALUES
(8, 10, 1, 1, 13.00, 21.00, NULL),
(9, 11, 1, 3, 13.00, 21.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_base_imponible` decimal(10,2) DEFAULT NULL,
  `total_iva` decimal(10,2) DEFAULT NULL,
  `total_final` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','pagado','enviado','cancelado') DEFAULT 'pendiente',
  `direccion_envio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `fecha_pedido`, `total_base_imponible`, `total_iva`, `total_final`, `estado`, `direccion_envio`) VALUES
(10, 2, '2026-06-04 17:27:02', 13.00, 2.73, 15.73, 'pendiente', ''),
(11, 2, '2026-06-04 21:42:52', 39.00, 8.19, 47.19, 'enviado', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `iva_porcentaje` decimal(5,2) DEFAULT 21.00,
  `stock` int(11) DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `es_personalizable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio_base`, `iva_porcentaje`, `stock`, `imagen`, `es_personalizable`) VALUES
(1, 1, 'Llavero Personalizado Madera', 'Llavero de roble con nombre grabado a doble cara', 13.00, 21.00, 44, '1780593399_Llaveros_de_madera_personalizados.webp', 1),
(2, 1, 'Llavero Acero Inoxidable', 'Llavero resistente con forma de corazón y fecha grabada', 15.00, 21.00, 29, '1780593421_LLavero_de_acero_inoxidable.webp', 1),
(3, 1, 'Llavero de Cuero', 'Llavero de cuero trenzado con iniciales', 9.90, 21.00, 44, '1780593442_Llavero_de_cuero.webp', 1),
(4, 2, 'Retrato Grabado Láser', 'Retrato familiar grabado en madera de pino 20x20cm', 45.00, 21.00, 10, '1780593465_Retrato_grabado.webp', 1),
(5, 2, 'Placa para Mascotas', 'Placa identificativa con el nombre y teléfono para collar', 9.99, 21.00, 100, '1780593482_Collar_Mascota.webp', 1),
(6, 2, 'Cartel Boda Personalizado', 'Cartel de bienvenida para bodas en metacrilato', 65.00, 21.00, 5, '1780593512_Cartel_Boda_personalizado.webp', 1),
(7, 3, 'Colgante Árbol de la Vida', 'Colgante artesanal de plata y cuarzo', 25.00, 21.00, 15, '1780593548_Arbol_de_la_vida.webp', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cliente') DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Admin Tienda', 'admin@artesanos.com', '$2y$10$HdQzT2BtyDvgzqdKsO5DxOJKK1gL/xjfDCl13poidoPsdOQJILQv2', 'admin', '2026-05-10 19:09:32'),
(2, 'Carlos García', 'carlos.garcia@gmail.com', '$2y$10$HdQzT2BtyDvgzqdKsO5DxOJKK1gL/xjfDCl13poidoPsdOQJILQv2', 'cliente', '2026-05-10 19:09:32'),
(3, 'Laura Martínez', 'laura.martinez@hotmail.com', '$2y$10$HdQzT2BtyDvgzqdKsO5DxOJKK1gL/xjfDCl13poidoPsdOQJILQv2', 'cliente', '2026-05-10 19:09:32'),
(4, 'Miguel Ángel', 'miguel.angel@yahoo.com', '$2y$10$HdQzT2BtyDvgzqdKsO5DxOJKK1gL/xjfDCl13poidoPsdOQJILQv2', 'cliente', '2026-05-10 19:09:32'),
(5, 'LARA', 'lara@gmail.com', '$2y$10$HdQzT2BtyDvgzqdKsO5DxOJKK1gL/xjfDCl13poidoPsdOQJILQv2', 'cliente', '2026-06-04 09:22:47');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD CONSTRAINT `detalles_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalles_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
