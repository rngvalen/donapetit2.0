-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2025 a las 23:11:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `donappetit`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargar_productos`
--

CREATE TABLE `cargar_productos` (
  `id_carga_producto` int(11) NOT NULL,
  `nom_producto` varchar(255) NOT NULL COMMENT 'Nombre del producto disponible en el catalogo.',
  `id_unidades` int(11) NOT NULL,
  `id_categorias` int(11) NOT NULL,
  `id_donante` int(11) DEFAULT NULL,
  `tipo_origen` varchar(10) NOT NULL DEFAULT 'admin',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `create_at` datetime NOT NULL COMMENT 'Fecha de creación del registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre de la categoría.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(6, 'Almacén'),
(3, 'Bebidas'),
(7, 'Carnes'),
(8, 'Enlatados'),
(4, 'Frutas'),
(9, 'Granos y Cereales'),
(2, 'Lácteos'),
(1, 'Panificados'),
(5, 'Verduras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigo_verificacion`
--

CREATE TABLE `codigo_verificacion` (
  `id_cod` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `fecha_expiracion` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_donacion`
--

CREATE TABLE `detalles_donacion` (
  `id_secuencia` int(11) NOT NULL,
  `id_donacionfk` int(11) NOT NULL,
  `id_productofk` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `cantidad_donado` int(200) NOT NULL,
  `fecha_donacion` datetime NOT NULL,
  `id_solicitud_detallefk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL,
  `id_usuario_direcc` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `nom_calle` varchar(50) NOT NULL,
  `num_calle` int(11) NOT NULL,
  `Latitud` decimal(10,8) DEFAULT NULL,
  `Longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id_direccion`, `id_usuario_direcc`, `nom_calle`, `num_calle`, `Latitud`, `Longitud`) VALUES
(1, 5, 'arbo y blanco', 550, -27.46006100, -58.98607500),
(2, 7, 'arbo y blanco', 550, -27.46021300, -58.98611000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donacion`
--

CREATE TABLE `donacion` (
  `id_donacion` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `id_donantefk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donante`
--

CREATE TABLE `donante` (
  `id_usu_donante` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `nom_comercial` varchar(255) NOT NULL COMMENT 'Nombre comercial.',
  `CUIT` varchar(20) NOT NULL COMMENT 'CUIT.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `id_solicitud_detalle` int(11) DEFAULT NULL,
  `id_donacionfk` int(11) DEFAULT NULL,
  `create_at` datetime NOT NULL,
  `id_stockfk` int(11) NOT NULL,
  `id_productofk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_productos` int(11) NOT NULL COMMENT 'ID producto.',
  `id_carga_producto` int(11) DEFAULT NULL COMMENT 'FK del catálogo cargar_productos',
  `create_at` datetime NOT NULL COMMENT 'Creación.',
  `update_at` datetime NOT NULL COMMENT 'Última mod.',
  `comentario` varchar(255) NOT NULL COMMENT 'Marca, empaque, etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor`
--

CREATE TABLE `receptor` (
  `id_usu_receptor` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `num_renacom` varchar(50) NOT NULL,
  `nom_institucion` varchar(255) NOT NULL,
  `responsable` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receptor`
--

INSERT INTO `receptor` (`id_usu_receptor`, `num_renacom`, `nom_institucion`, `responsable`) VALUES
(5, '321354321321', 'los redondos', 'valwnr'),
(7, '1321321321', 'pirisFTcandela', 'candela');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación del registro',
  `update_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Última modificación',
  `id_receptorfk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_detalle`
--

CREATE TABLE `solicitudes_detalle` (
  `id_solicitud_detalle` int(11) NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_retiro` datetime NOT NULL,
  `estado` tinyint(5) NOT NULL,
  `id_solicitudfk` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL COMMENT 'cantidad solicitada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_productos`
--

CREATE TABLE `stock_productos` (
  `id_stock` int(11) NOT NULL,
  `id_donante` int(11) NOT NULL COMMENT 'FK donante.id_usu_donante',
  `cantidad` int(11) NOT NULL COMMENT 'Cantidad en stock',
  `id_producto` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `fecha_venc` date NOT NULL COMMENT 'Fecha de vencimiento del producto.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_productos_donacion`
--

CREATE TABLE `stock_productos_donacion` (
  `id_stock_productos_donaciones` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `stock_productos` int(11) NOT NULL COMMENT 'FK stock_productos.id_stock',
  `id_detalle_donacion` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE `unidades` (
  `id_unidad` int(11) NOT NULL,
  `nombre_unidad` varchar(50) NOT NULL COMMENT 'Kilogramo, Gramo, Litro, etc.',
  `abreviatura` varchar(10) NOT NULL COMMENT 'kg, g, lts, ml, etc.',
  `estado` tinyint(1) NOT NULL COMMENT '1=activo, 0=inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades`
--

INSERT INTO `unidades` (`id_unidad`, `nombre_unidad`, `abreviatura`, `estado`) VALUES
(1, 'Kilogramo', 'kg', 1),
(2, 'Gramo', 'g', 1),
(3, 'Litro', 'lts', 1),
(4, 'Mililitro', 'ml', 1),
(5, 'Unidad', 'u', 1),
(6, 'Paquete', 'pack', 1),
(22, 'Docena', 'docena', 1),
(23, 'Lata', 'lata', 1),
(24, 'Caja', 'caja', 1),
(25, 'Botella', 'botella', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL COMMENT 'Identificador único.',
  `Nombre` varchar(255) NOT NULL COMMENT 'Nombre completo.',
  `Email` varchar(255) NOT NULL COMMENT 'Único. Para login.',
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL COMMENT 'donante | receptor | admin',
  `telefono` varchar(50) DEFAULT NULL COMMENT 'Teléfono del usuario.',
  `Latitud` decimal(10,8) DEFAULT NULL COMMENT 'Latitud GPS.',
  `Longitud` decimal(11,8) DEFAULT NULL COMMENT 'Longitud GPS.',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `Nombre`, `Email`, `contrasena`, `rol`, `telefono`, `Latitud`, `Longitud`, `activo`) VALUES
(2, 'TestUser', 'test@example.com', '$2y$10$Rn59n7lAywj4Tcc3sAsOj.tYh2HA6VQeDPsbc7YMM2/19C3E8asZ2', 'donante', '123456789', 0.00000000, 0.00000000, 1),
(3, 'Benjamin', 'ivanluxen76@gmail.com', '$2y$10$5v8ExOwsB0QIFwWIrMJZQO5Lp7lz3Et6ZWsM4B58F7BQfp2x7QPKu', 'donante', '3644883178', -27.44828195, -58.98502021, 1),
(4, 'Benjamin', 'ivanluxen@gmail.com', '$2y$10$COopMyyoKRUB.ZD/jAq0aOqVq/mvhY8/WR/owWYW.zlwfr7TmrnIe', 'donante', '3644883178', -27.44827068, -58.98504788, 1),
(5, 'valentin', 'valentinfluss29@gmail.com', '$2y$10$cP5SVabKJWmFVGXyZc8WreQc/fnj1xNSrWe53caOSRm82uyTwVQWy', 'receptor', NULL, -27.46021316, -58.98610953, 1),
(6, 'benja', 'banja123@gmail.com', '$2y$10$s8jDIDdswnncj3B8dUHNaOXrS1JDH4aPZ55TTdwa1Zwq5rbdFN5bu', 'receptor', NULL, -27.46017357, -58.98606901, 1),
(7, 'candela', 'candela12@gmail.com', '$2y$10$igE4KW.RRm1vbQCf5Xb9Se1spFdhtVpJe1uUXu/fkjnzH6Xy7mVy6', 'receptor', NULL, -27.46021316, -58.98610953, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargar_productos`
--
ALTER TABLE `cargar_productos`
  ADD PRIMARY KEY (`id_carga_producto`),
  ADD KEY `fk_cargar_donante` (`id_donante`),
  ADD KEY `fk_idcategoria` (`id_categorias`),
  ADD KEY `fk_idunidad` (`id_unidades`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `uq_categorias_nombre` (`nombre`);

--
-- Indices de la tabla `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  ADD PRIMARY KEY (`id_cod`),
  ADD KEY `idx_cod_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  ADD PRIMARY KEY (`id_secuencia`),
  ADD KEY `id_donacionfk` (`id_donacionfk`),
  ADD KEY `id_productofk` (`id_productofk`),
  ADD KEY `id_retirofk` (`id_solicitud_detallefk`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `idx_dir_usuario` (`id_usuario_direcc`);

--
-- Indices de la tabla `donacion`
--
ALTER TABLE `donacion`
  ADD PRIMARY KEY (`id_donacion`),
  ADD KEY `id_donantefk` (`id_donantefk`);

--
-- Indices de la tabla `donante`
--
ALTER TABLE `donante`
  ADD PRIMARY KEY (`id_usu_donante`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_donacion` (`id_donacionfk`),
  ADD KEY `id_stock` (`id_stockfk`),
  ADD KEY `id_producto` (`id_productofk`),
  ADD KEY `id_solicitud_detalles` (`id_solicitud_detalle`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_productos`),
  ADD KEY `fk_productos_cargar` (`id_carga_producto`);

--
-- Indices de la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD PRIMARY KEY (`id_usu_receptor`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_receptorfk` (`id_receptorfk`);

--
-- Indices de la tabla `solicitudes_detalle`
--
ALTER TABLE `solicitudes_detalle`
  ADD PRIMARY KEY (`id_solicitud_detalle`),
  ADD KEY `id_solicitudfk` (`id_solicitudfk`);

--
-- Indices de la tabla `stock_productos`
--
ALTER TABLE `stock_productos`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `idx_stock_donante` (`id_donante`),
  ADD KEY `idx_stock_producto` (`id_producto`);

--
-- Indices de la tabla `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  ADD PRIMARY KEY (`id_stock_productos_donaciones`),
  ADD KEY `idx_spd_producto` (`id_producto`),
  ADD KEY `idx_spd_stock` (`stock_productos`),
  ADD KEY `id_detallesDonacion` (`id_detalle_donacion`);

--
-- Indices de la tabla `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id_unidad`),
  ADD UNIQUE KEY `uq_unidades_nombre` (`nombre_unidad`),
  ADD UNIQUE KEY `uq_unidades_abrev` (`abreviatura`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuarios_email` (`Email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cargar_productos`
--
ALTER TABLE `cargar_productos`
  MODIFY `id_carga_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  MODIFY `id_cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  MODIFY `id_secuencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `donacion`
--
ALTER TABLE `donacion`
  MODIFY `id_donacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_productos` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID producto.';

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_detalle`
--
ALTER TABLE `solicitudes_detalle`
  MODIFY `id_solicitud_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_productos`
--
ALTER TABLE `stock_productos`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  MODIFY `id_stock_productos_donaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único.', AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cargar_productos`
--
ALTER TABLE `cargar_productos`
  ADD CONSTRAINT `fk_cargar_donante` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idcategoria` FOREIGN KEY (`id_categorias`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idunidad` FOREIGN KEY (`id_unidades`) REFERENCES `unidades` (`id_unidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  ADD CONSTRAINT `fk_codigo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  ADD CONSTRAINT `id_donacionfk` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_productofk` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_solicitud_detallefk` FOREIGN KEY (`id_solicitud_detallefk`) REFERENCES `solicitudes_detalle` (`id_solicitud_detalle`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `fk_dir_usuario` FOREIGN KEY (`id_usuario_direcc`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `donacion`
--
ALTER TABLE `donacion`
  ADD CONSTRAINT `id_donantefk` FOREIGN KEY (`id_donantefk`) REFERENCES `donante` (`id_usu_donante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `donante`
--
ALTER TABLE `donante`
  ADD CONSTRAINT `fk_donante_usuario` FOREIGN KEY (`id_usu_donante`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `id_donacion` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_solicitud_detalles` FOREIGN KEY (`id_solicitud_detalle`) REFERENCES `solicitudes_detalle` (`id_solicitud_detalle`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_stock` FOREIGN KEY (`id_stockfk`) REFERENCES `stock_productos` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_cargar` FOREIGN KEY (`id_carga_producto`) REFERENCES `cargar_productos` (`id_carga_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD CONSTRAINT `fk_receptor_usuario` FOREIGN KEY (`id_usu_receptor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `id_receptorfk` FOREIGN KEY (`id_receptorfk`) REFERENCES `receptor` (`id_usu_receptor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_detalle`
--
ALTER TABLE `solicitudes_detalle`
  ADD CONSTRAINT `id_solicitudfk` FOREIGN KEY (`id_solicitudfk`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_productos`
--
ALTER TABLE `stock_productos`
  ADD CONSTRAINT `fk_stock_donante` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  ADD CONSTRAINT `fk_spd_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spd_stock` FOREIGN KEY (`stock_productos`) REFERENCES `stock_productos` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_detallesDonacion` FOREIGN KEY (`id_detalle_donacion`) REFERENCES `detalles_donacion` (`id_secuencia`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
