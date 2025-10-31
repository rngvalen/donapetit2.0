-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2025 a las 15:06:47
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
  `activo` varchar(1) NOT NULL,
  `codigo_verificacion` varchar(6) NOT NULL
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
  `fecha_donacion` datetime NOT NULL
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donacion`
--

CREATE TABLE `donacion` (
  `id_donacion` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
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
  `create_at` datetime NOT NULL COMMENT 'Creación.',
  `update_at` datetime NOT NULL COMMENT 'Última mod.',
  `comentario` varchar(255) NOT NULL COMMENT 'Marca, empaque, etc.',
  `id_unidad` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL COMMENT 'pendiente o realizado, haciendo referencia a si una solicitud fue retirada o no.',
  `id_donaciones` int(11) NOT NULL,
  `delete_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_detalle`
--

CREATE TABLE `solicitudes_detalle` (
  `id_secuenciaSoli` int(11) NOT NULL,
  `id_solicitudFk` int(11) NOT NULL,
  `id_stockDonacionFk` int(11) NOT NULL,
  `cantidad_solicitada` int(200) NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_retiro` datetime NOT NULL
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
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_productos_donacion`
--

CREATE TABLE `stock_productos_donacion` (
  `id_stock_productos_donaciones` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `stock_productos` int(11) NOT NULL COMMENT 'FK stock_productos.id_stock',
  `fecha_venc` datetime NOT NULL
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
(6, 'Paquete', 'pack', 1);

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
  `activo` varchar(1) NOT NULL DEFAULT '1' COMMENT '1=activo, 0=inactivo.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `Nombre`, `Email`, `contrasena`, `rol`, `telefono`, `Latitud`, `Longitud`, `activo`) VALUES
(2, 'TestUser', 'test@example.com', '$2y$10$Rn59n7lAywj4Tcc3sAsOj.tYh2HA6VQeDPsbc7YMM2/19C3E8asZ2', 'donante', '123456789', 0.00000000, 0.00000000, '1'),
(3, 'Benjamin', 'ivanluxen76@gmail.com', '$2y$10$5v8ExOwsB0QIFwWIrMJZQO5Lp7lz3Et6ZWsM4B58F7BQfp2x7QPKu', 'donante', '3644883178', -27.44828195, -58.98502021, '1'),
(4, 'Benjamin', 'ivanluxen@gmail.com', '$2y$10$COopMyyoKRUB.ZD/jAq0aOqVq/mvhY8/WR/owWYW.zlwfr7TmrnIe', 'donante', '3644883178', -27.44827068, -58.98504788, '1'),
(5, 'Candela Delvalle', 'candedelvalle46@gmail.com', '$2y$10$wS8aQEVe9AQdewZRjVVpmeNbHhG0xh3oq2u9pJVXJuZgm8EIOdk/C', 'donante', '3624753651', 0.00000000, 0.00000000, '1');

--
-- Índices para tablas volcadas
--

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
  ADD KEY `id_productofk` (`id_productofk`);

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
  ADD PRIMARY KEY (`id_donacion`);

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
  ADD KEY `id_producto` (`id_productofk`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_productos`),
  ADD KEY `id_unidadfk` (`id_unidad`),
  ADD KEY `id_categoriafk` (`id_categoria`);

--
-- Indices de la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD PRIMARY KEY (`id_usu_receptor`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`);

--
-- Indices de la tabla `solicitudes_detalle`
--
ALTER TABLE `solicitudes_detalle`
  ADD PRIMARY KEY (`id_secuenciaSoli`),
  ADD KEY `id_solicitudFk` (`id_solicitudFk`),
  ADD KEY `id_stockDonacionFk` (`id_stockDonacionFk`);

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
  ADD KEY `idx_spd_stock` (`stock_productos`);

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
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_secuenciaSoli` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único.', AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  ADD CONSTRAINT `fk_codigo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  ADD CONSTRAINT `id_donacionfk` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`),
  ADD CONSTRAINT `id_productofk` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`);

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `fk_dir_usuario` FOREIGN KEY (`id_usuario_direcc`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `donante`
--
ALTER TABLE `donante`
  ADD CONSTRAINT `fk_donante_usuario` FOREIGN KEY (`id_usu_donante`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `id_donacion` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`),
  ADD CONSTRAINT `id_producto` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `id_stock` FOREIGN KEY (`id_stockfk`) REFERENCES `stock_productos` (`id_stock`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `id_categoriafk` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `id_unidadfk` FOREIGN KEY (`id_unidad`) REFERENCES `unidades` (`id_unidad`);

--
-- Filtros para la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD CONSTRAINT `fk_receptor_usuario` FOREIGN KEY (`id_usu_receptor`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_detalle`
--
ALTER TABLE `solicitudes_detalle`
  ADD CONSTRAINT `id_solicitudFk` FOREIGN KEY (`id_solicitudFk`) REFERENCES `solicitudes` (`id_solicitud`) ON UPDATE CASCADE,
  ADD CONSTRAINT `id_stockDonacionFk` FOREIGN KEY (`id_stockDonacionFk`) REFERENCES `stock_productos_donacion` (`id_stock_productos_donaciones`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_productos`
--
ALTER TABLE `stock_productos`
  ADD CONSTRAINT `fk_stock_donante` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  ADD CONSTRAINT `fk_spd_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spd_stock` FOREIGN KEY (`stock_productos`) REFERENCES `stock_productos` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
