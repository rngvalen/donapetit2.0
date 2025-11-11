-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2025 a las 20:31:25
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
-- Estructura de tabla para la tabla `catalogo_productos`
--

CREATE TABLE `catalogo_productos` (
  `id_catalogo` int(11) NOT NULL,
  `nom_producto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_unidad` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catalogo_productos`
--

INSERT INTO `catalogo_productos` (`id_catalogo`, `nom_producto`, `descripcion`, `id_categoria`, `id_unidad`, `estado`) VALUES
(1, 'Pan francés', 'Pan fresco del día', 1, 1, 'Activo'),
(2, 'Pan de molde', 'Pan de molde tajado', 1, 5, 'Activo'),
(3, 'Facturas', 'Facturas surtidas', 1, 22, 'Activo'),
(4, 'Leche entera', 'Leche entera pasteurizada', 2, 3, 'Activo'),
(5, 'Yogur natural', 'Yogur natural sin azúcar', 2, 5, 'Activo'),
(6, 'Queso fresco', 'Queso fresco cremoso', 2, 1, 'Activo'),
(7, 'Agua mineral', 'Agua mineral sin gas', 3, 3, 'Activo'),
(8, 'Gaseosa', 'Gaseosa cola', 3, 3, 'Activo'),
(9, 'Jugo de naranja', 'Jugo natural de naranja', 3, 3, 'Activo'),
(10, 'Manzanas', 'Manzanas rojas', 4, 1, 'Activo'),
(11, 'Bananas', 'Bananas maduras', 4, 1, 'Activo'),
(12, 'Naranjas', 'Naranjas para jugo', 4, 1, 'Activo'),
(13, 'Tomates', 'Tomates perita', 5, 1, 'Activo'),
(14, 'Lechuga', 'Lechuga crespa', 5, 5, 'Activo'),
(15, 'Papas', 'Papas blancas', 5, 1, 'Activo'),
(16, 'Arroz', 'Arroz blanco largo fino', 6, 1, 'Activo'),
(17, 'Fideos', 'Fideos secos tipo mostachol', 6, 6, 'Activo'),
(18, 'Aceite', 'Aceite de girasol', 6, 25, 'Activo'),
(19, 'Pollo entero', 'Pollo fresco entero', 7, 1, 'Activo'),
(20, 'Carne molida', 'Carne vacuna molida', 7, 1, 'Activo'),
(21, 'Atún enlatado', 'Atún al natural', 8, 23, 'Activo'),
(22, 'Arvejas enlatadas', 'Arvejas en lata', 8, 23, 'Activo'),
(23, 'Lentejas', 'Lentejas secas', 9, 1, 'Activo'),
(24, 'Garbanzos', 'Garbanzos secos', 9, 1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(1, 'Panificados'),
(2, 'Lácteos'),
(3, 'Bebidas'),
(4, 'Frutas'),
(5, 'Verduras'),
(6, 'Almacén'),
(7, 'Carnes'),
(8, 'Enlatados'),
(9, 'Granos y Cereales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle`
--

CREATE TABLE `detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_producto_solicitado` int(11) NOT NULL,
  `cantidad_donada` int(11) NOT NULL,
  `donacion_efectiva` tinyint(1) DEFAULT 0,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `detalle`
--
DELIMITER $$
CREATE TRIGGER `trigger_actualizar_stock` AFTER UPDATE ON `detalle` FOR EACH ROW BEGIN
  -- Declaraciones SIEMPRE al comienzo del bloque
  DECLARE idProdDonante INT;

  IF NEW.donacion_efectiva = 1 AND OLD.donacion_efectiva = 0 THEN
    -- buscar el producto del donante asociado al detalle
    SELECT ps.id_producto_donante
      INTO idProdDonante
      FROM productos_solicitados ps
     WHERE ps.id_producto_solicitado = NEW.id_producto_solicitado;

    -- descontar stock
    UPDATE productos_donante
       SET cantidad_disponible = cantidad_disponible - NEW.cantidad_donada
     WHERE id_producto_donante = idProdDonante;

    -- registrar movimiento
    INSERT INTO movimiento (id_producto_donante, tipo_movimiento, cantidad)
    VALUES (idProdDonante, 'Baja', NEW.cantidad_donada, 'Donación confirmada');
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL,
  `id_usuario_direcc` int(11) NOT NULL,
  `nom_calle` varchar(50) NOT NULL,
  `num_calle` int(11) DEFAULT NULL,
  `Latitud` decimal(10,8) DEFAULT NULL,
  `Longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id_direccion`, `id_usuario_direcc`, `nom_calle`, `num_calle`, `Latitud`, `Longitud`) VALUES
(1, 1, 'Vaegas Gomez', 1652, -27.51201300, -58.77268500),
(2, 2, 'de Mayo 595', 25, -27.51201300, -58.77268500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donante`
--

CREATE TABLE `donante` (
  `id_usu_donante` int(11) NOT NULL,
  `nom_comercial` varchar(255) NOT NULL,
  `CUIT` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `donante`
--

INSERT INTO `donante` (`id_usu_donante`, `nom_comercial`, `CUIT`) VALUES
(1, 'Me llamo jose', '20-44826935-4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto_donante` int(11) NOT NULL,
  `tipo_movimiento` enum('Alta','Baja','Ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_donante`
--

CREATE TABLE `productos_donante` (
  `id_producto_donante` int(11) NOT NULL,
  `id_donante` int(11) NOT NULL,
  `id_catalogo` int(11) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_donante`
--

INSERT INTO `productos_donante` (`id_producto_donante`, `id_donante`, `id_catalogo`, `cantidad_disponible`, `fecha_vencimiento`, `fecha_registro`, `estado`) VALUES
(1, 1, 16, 1, '2025-11-19', '2025-11-10 16:30:19', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_solicitados`
--

CREATE TABLE `productos_solicitados` (
  `id_producto_solicitado` int(11) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `id_producto_donante` int(11) NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor`
--

CREATE TABLE `receptor` (
  `id_usu_receptor` int(11) NOT NULL,
  `nom_institucion` varchar(150) NOT NULL,
  `num_renacom` varchar(50) NOT NULL,
  `responsable` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receptor`
--

INSERT INTO `receptor` (`id_usu_receptor`, `nom_institucion`, `num_renacom`, `responsable`) VALUES
(2, 'Benjamin Iván Torres Cossani', 'wewe', 'wewe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `id_solicitud` int(11) NOT NULL,
  `id_receptor` int(11) NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE `unidades` (
  `id_unidad` int(11) NOT NULL,
  `nombre_unidad` varchar(50) NOT NULL,
  `abreviatura` varchar(10) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL
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
  `id_usuario` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `Latitud` decimal(10,8) DEFAULT NULL,
  `Longitud` decimal(11,8) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `Nombre`, `Email`, `contrasena`, `rol`, `telefono`, `Latitud`, `Longitud`, `activo`) VALUES
(1, 'Torres', 'ivanluxen76@gmail.com', '$2y$10$Tfjkhx0/5HdA4BmVArURJeqlrywjIlwZQUQpk.K105O3Ibv0sIiVi', 'donante', '3644883178', -27.51201280, -58.77268480, 1),
(2, 'Valen', 'ivanluxen@gmail.com', '$2y$10$Gr5HDmtIYmKLd9UtepckeeziflMbppALsp4hFLWbpU8Yuz54aFdLi', 'receptor', '3644883178', -27.51201280, -58.77268480, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verificar_contrasena`
--

CREATE TABLE `verificar_contrasena` (
  `id_cod` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_expiracion` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL,
  `codigo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_catalogo_disponible`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_catalogo_disponible` (
`id_catalogo` int(11)
,`nombre_producto` varchar(100)
,`categoria` varchar(100)
,`unidad` varchar(50)
,`abreviatura_unidad` varchar(10)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_disponibles`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_disponibles` (
`nombre_donante` varchar(255)
,`nom_producto` varchar(100)
,`categoria` varchar(100)
,`cantidad_disponible` int(11)
,`unidad` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_catalogo_disponible`
--
DROP TABLE IF EXISTS `vista_catalogo_disponible`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_catalogo_disponible`  AS SELECT `cp`.`id_catalogo` AS `id_catalogo`, `cp`.`nom_producto` AS `nombre_producto`, `c`.`nombre` AS `categoria`, `u`.`nombre_unidad` AS `unidad`, `u`.`abreviatura` AS `abreviatura_unidad` FROM ((`catalogo_productos` `cp` join `categorias` `c` on(`cp`.`id_categoria` = `c`.`id_categoria`)) join `unidades` `u` on(`cp`.`id_unidad` = `u`.`id_unidad`)) WHERE `cp`.`estado` = 'Activo' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_disponibles`
--
DROP TABLE IF EXISTS `vista_productos_disponibles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_disponibles`  AS SELECT `d`.`nom_comercial` AS `nombre_donante`, `cp`.`nom_producto` AS `nom_producto`, `c`.`nombre` AS `categoria`, `pd`.`cantidad_disponible` AS `cantidad_disponible`, `u`.`nombre_unidad` AS `unidad` FROM ((((`productos_donante` `pd` join `donante` `d` on(`pd`.`id_donante` = `d`.`id_usu_donante`)) join `catalogo_productos` `cp` on(`pd`.`id_catalogo` = `cp`.`id_catalogo`)) join `categorias` `c` on(`cp`.`id_categoria` = `c`.`id_categoria`)) join `unidades` `u` on(`cp`.`id_unidad` = `u`.`id_unidad`)) WHERE `pd`.`cantidad_disponible` > 0 AND `pd`.`estado` = 'Activo' ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `catalogo_productos`
--
ALTER TABLE `catalogo_productos`
  ADD PRIMARY KEY (`id_catalogo`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_producto_solicitado` (`id_producto_solicitado`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_usuario_direcc` (`id_usuario_direcc`);

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
  ADD KEY `id_producto_donante` (`id_producto_donante`);

--
-- Indices de la tabla `productos_donante`
--
ALTER TABLE `productos_donante`
  ADD PRIMARY KEY (`id_producto_donante`),
  ADD KEY `id_donante` (`id_donante`),
  ADD KEY `id_catalogo` (`id_catalogo`);

--
-- Indices de la tabla `productos_solicitados`
--
ALTER TABLE `productos_solicitados`
  ADD PRIMARY KEY (`id_producto_solicitado`),
  ADD KEY `id_solicitud` (`id_solicitud`),
  ADD KEY `id_producto_donante` (`id_producto_donante`);

--
-- Indices de la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD PRIMARY KEY (`id_usu_receptor`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_receptor` (`id_receptor`);

--
-- Indices de la tabla `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indices de la tabla `verificar_contrasena`
--
ALTER TABLE `verificar_contrasena`
  ADD PRIMARY KEY (`id_cod`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `catalogo_productos`
--
ALTER TABLE `catalogo_productos`
  MODIFY `id_catalogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle`
--
ALTER TABLE `detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_donante`
--
ALTER TABLE `productos_donante`
  MODIFY `id_producto_donante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos_solicitados`
--
ALTER TABLE `productos_solicitados`
  MODIFY `id_producto_solicitado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `verificar_contrasena`
--
ALTER TABLE `verificar_contrasena`
  MODIFY `id_cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `catalogo_productos`
--
ALTER TABLE `catalogo_productos`
  ADD CONSTRAINT `catalogo_productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `catalogo_productos_ibfk_2` FOREIGN KEY (`id_unidad`) REFERENCES `unidades` (`id_unidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD CONSTRAINT `detalle_ibfk_1` FOREIGN KEY (`id_producto_solicitado`) REFERENCES `productos_solicitados` (`id_producto_solicitado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`id_usuario_direcc`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `donante`
--
ALTER TABLE `donante`
  ADD CONSTRAINT `donante_ibfk_1` FOREIGN KEY (`id_usu_donante`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `movimiento_ibfk_1` FOREIGN KEY (`id_producto_donante`) REFERENCES `productos_donante` (`id_producto_donante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_donante`
--
ALTER TABLE `productos_donante`
  ADD CONSTRAINT `productos_donante_ibfk_1` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_donante_ibfk_2` FOREIGN KEY (`id_catalogo`) REFERENCES `catalogo_productos` (`id_catalogo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_solicitados`
--
ALTER TABLE `productos_solicitados`
  ADD CONSTRAINT `productos_solicitados_ibfk_1` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_solicitados_ibfk_2` FOREIGN KEY (`id_producto_donante`) REFERENCES `productos_donante` (`id_producto_donante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `receptor`
--
ALTER TABLE `receptor`
  ADD CONSTRAINT `receptor_ibfk_1` FOREIGN KEY (`id_usu_receptor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `solicitud_ibfk_1` FOREIGN KEY (`id_receptor`) REFERENCES `receptor` (`id_usu_receptor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `verificar_contrasena`
--
ALTER TABLE `verificar_contrasena`
  ADD CONSTRAINT `verificar_contrasena_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
