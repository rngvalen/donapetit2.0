-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 22, 2025 at 03:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `donappetit`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre de la categoría.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
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
-- Table structure for table `codigo_verificacion`
--

CREATE TABLE `codigo_verificacion` (
  `id_cod` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `fecha_expiracion` datetime NOT NULL,
  `activo` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalles_donacion`
--

CREATE TABLE `detalles_donacion` (
  `id_secuencia` int(11) NOT NULL,
  `id_donacionfk` int(11) NOT NULL,
  `id_productofk` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `cantidad_donado` int(200) NOT NULL,
  `fecha_donacion` datetime NOT NULL,
  `id_retirofk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `direcciones`
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
-- Table structure for table `donacion`
--

CREATE TABLE `donacion` (
  `id_donacion` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donante`
--

CREATE TABLE `donante` (
  `id_usu_donante` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `nom_comercial` varchar(255) NOT NULL COMMENT 'Nombre comercial.',
  `CUIT` varchar(20) NOT NULL COMMENT 'CUIT.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estadistica`
--

CREATE TABLE `estadistica` (
  `id_estadistica` int(11) NOT NULL,
  `id_donacion` int(11) NOT NULL COMMENT 'FK donacion.id_donacion',
  `total_donado` int(11) NOT NULL,
  `frecuencia_mensual` int(11) NOT NULL,
  `frecuencia_anual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imagenes_productos`
--

CREATE TABLE `imagenes_productos` (
  `id_imagenes` int(11) NOT NULL,
  `id_productos` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `url` varchar(2083) NOT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `id_retirofk` int(11) DEFAULT NULL,
  `id_donacionfk` int(11) DEFAULT NULL,
  `create_at` datetime NOT NULL,
  `id_stockfk` int(11) NOT NULL,
  `id_productofk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `productos`
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
-- Table structure for table `receptor`
--

CREATE TABLE `receptor` (
  `id_usu_receptor` int(11) NOT NULL COMMENT 'FK usuarios.id_usuario',
  `num_renacom` varchar(50) NOT NULL,
  `nom_institucion` varchar(255) NOT NULL,
  `responsable` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retiros`
--

CREATE TABLE `retiros` (
  `id_retiro` int(11) NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_retiro` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL COMMENT '1=entregado, 0=no',
  `id_direcciones` int(11) NOT NULL COMMENT 'FK direcciones.id_direccion',
  `detalles_donacionfk` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `delete_at` datetime NOT NULL,
  `id_donacionfk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL,
  `id_donante` int(11) NOT NULL COMMENT 'FK donante.id_usu_donante',
  `id_productos` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `cantidad_solicitada` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_productos`
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
-- Table structure for table `stock_productos_donacion`
--

CREATE TABLE `stock_productos_donacion` (
  `id_stock_productos_donaciones` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL COMMENT 'FK productos.id_productos',
  `stock_productos` int(11) NOT NULL COMMENT 'FK stock_productos.id_stock',
  `fecha_venc` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unidades`
--

CREATE TABLE `unidades` (
  `id_unidad` int(11) NOT NULL,
  `nombre_unidad` varchar(50) NOT NULL COMMENT 'Kilogramo, Gramo, Litro, etc.',
  `abreviatura` varchar(10) NOT NULL COMMENT 'kg, g, lts, ml, etc.',
  `estado` tinyint(1) NOT NULL COMMENT '1=activo, 0=inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unidades`
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
-- Table structure for table `usuarios`
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
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `Nombre`, `Email`, `contrasena`, `rol`, `telefono`, `Latitud`, `Longitud`, `activo`) VALUES
(2, 'TestUser', 'test@example.com', '$2y$10$Rn59n7lAywj4Tcc3sAsOj.tYh2HA6VQeDPsbc7YMM2/19C3E8asZ2', 'donante', '123456789', 0.00000000, 0.00000000, '1'),
(3, 'Benjamin', 'ivanluxen76@gmail.com', '$2y$10$5v8ExOwsB0QIFwWIrMJZQO5Lp7lz3Et6ZWsM4B58F7BQfp2x7QPKu', 'donante', '3644883178', -27.44828195, -58.98502021, '1'),
(4, 'Benjamin', 'ivanluxen@gmail.com', '$2y$10$COopMyyoKRUB.ZD/jAq0aOqVq/mvhY8/WR/owWYW.zlwfr7TmrnIe', 'donante', '3644883178', -27.44827068, -58.98504788, '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `uq_categorias_nombre` (`nombre`);

--
-- Indexes for table `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  ADD PRIMARY KEY (`id_cod`),
  ADD KEY `idx_cod_usuario` (`id_usuario`);

--
-- Indexes for table `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  ADD PRIMARY KEY (`id_secuencia`),
  ADD KEY `id_donacionfk` (`id_donacionfk`),
  ADD KEY `id_productofk` (`id_productofk`),
  ADD KEY `id_retirofk` (`id_retirofk`);

--
-- Indexes for table `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `idx_dir_usuario` (`id_usuario_direcc`);

--
-- Indexes for table `donacion`
--
ALTER TABLE `donacion`
  ADD PRIMARY KEY (`id_donacion`);

--
-- Indexes for table `donante`
--
ALTER TABLE `donante`
  ADD PRIMARY KEY (`id_usu_donante`);

--
-- Indexes for table `estadistica`
--
ALTER TABLE `estadistica`
  ADD PRIMARY KEY (`id_estadistica`),
  ADD KEY `idx_estad_donacion` (`id_donacion`);

--
-- Indexes for table `imagenes_productos`
--
ALTER TABLE `imagenes_productos`
  ADD PRIMARY KEY (`id_imagenes`),
  ADD KEY `idx_img_producto` (`id_productos`);

--
-- Indexes for table `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_donacion` (`id_donacionfk`),
  ADD KEY `id_stock` (`id_stockfk`),
  ADD KEY `id_retiro` (`id_retirofk`),
  ADD KEY `id_producto` (`id_productofk`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_productos`),
  ADD KEY `id_unidadfk` (`id_unidad`),
  ADD KEY `id_categoriafk` (`id_categoria`);

--
-- Indexes for table `receptor`
--
ALTER TABLE `receptor`
  ADD PRIMARY KEY (`id_usu_receptor`);

--
-- Indexes for table `retiros`
--
ALTER TABLE `retiros`
  ADD PRIMARY KEY (`id_retiro`),
  ADD KEY `idx_retiro_direccion` (`id_direcciones`),
  ADD KEY `detalles_donacionfk` (`detalles_donacionfk`),
  ADD KEY `id_don` (`id_donacionfk`);

--
-- Indexes for table `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `idx_sol_donante` (`id_donante`),
  ADD KEY `idx_sol_producto` (`id_productos`);

--
-- Indexes for table `stock_productos`
--
ALTER TABLE `stock_productos`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `idx_stock_donante` (`id_donante`),
  ADD KEY `idx_stock_producto` (`id_producto`);

--
-- Indexes for table `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  ADD PRIMARY KEY (`id_stock_productos_donaciones`),
  ADD KEY `idx_spd_producto` (`id_producto`),
  ADD KEY `idx_spd_stock` (`stock_productos`);

--
-- Indexes for table `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id_unidad`),
  ADD UNIQUE KEY `uq_unidades_nombre` (`nombre_unidad`),
  ADD UNIQUE KEY `uq_unidades_abrev` (`abreviatura`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuarios_email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  MODIFY `id_cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  MODIFY `id_secuencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donacion`
--
ALTER TABLE `donacion`
  MODIFY `id_donacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estadistica`
--
ALTER TABLE `estadistica`
  MODIFY `id_estadistica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imagenes_productos`
--
ALTER TABLE `imagenes_productos`
  MODIFY `id_imagenes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id_productos` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID producto.';

--
-- AUTO_INCREMENT for table `retiros`
--
ALTER TABLE `retiros`
  MODIFY `id_retiro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_productos`
--
ALTER TABLE `stock_productos`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  MODIFY `id_stock_productos_donaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único.', AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `codigo_verificacion`
--
ALTER TABLE `codigo_verificacion`
  ADD CONSTRAINT `fk_codigo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Constraints for table `detalles_donacion`
--
ALTER TABLE `detalles_donacion`
  ADD CONSTRAINT `id_donacionfk` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`),
  ADD CONSTRAINT `id_productofk` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `id_retirofk` FOREIGN KEY (`id_retirofk`) REFERENCES `retiros` (`id_retiro`);

--
-- Constraints for table `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `fk_dir_usuario` FOREIGN KEY (`id_usuario_direcc`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Constraints for table `donante`
--
ALTER TABLE `donante`
  ADD CONSTRAINT `fk_donante_usuario` FOREIGN KEY (`id_usu_donante`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Constraints for table `estadistica`
--
ALTER TABLE `estadistica`
  ADD CONSTRAINT `fk_estad_donacion` FOREIGN KEY (`id_donacion`) REFERENCES `donacion` (`id_donacion`) ON UPDATE CASCADE;

--
-- Constraints for table `imagenes_productos`
--
ALTER TABLE `imagenes_productos`
  ADD CONSTRAINT `fk_img_producto` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `id_donacion` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`),
  ADD CONSTRAINT `id_producto` FOREIGN KEY (`id_productofk`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `id_retiro` FOREIGN KEY (`id_retirofk`) REFERENCES `retiros` (`id_retiro`),
  ADD CONSTRAINT `id_stock` FOREIGN KEY (`id_stockfk`) REFERENCES `stock_productos` (`id_stock`);

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `id_categoriafk` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `id_unidadfk` FOREIGN KEY (`id_unidad`) REFERENCES `unidades` (`id_unidad`);

--
-- Constraints for table `receptor`
--
ALTER TABLE `receptor`
  ADD CONSTRAINT `fk_receptor_usuario` FOREIGN KEY (`id_usu_receptor`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Constraints for table `retiros`
--
ALTER TABLE `retiros`
  ADD CONSTRAINT `detalles_donacionfk` FOREIGN KEY (`detalles_donacionfk`) REFERENCES `detalles_donacion` (`id_secuencia`),
  ADD CONSTRAINT `fk_retiro_direccion` FOREIGN KEY (`id_direcciones`) REFERENCES `direcciones` (`id_direccion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `id_don` FOREIGN KEY (`id_donacionfk`) REFERENCES `donacion` (`id_donacion`);

--
-- Constraints for table `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_sol_donante` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sol_producto` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_productos`
--
ALTER TABLE `stock_productos`
  ADD CONSTRAINT `fk_stock_donante` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_usu_donante`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_productos_donacion`
--
ALTER TABLE `stock_productos_donacion`
  ADD CONSTRAINT `fk_spd_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spd_stock` FOREIGN KEY (`stock_productos`) REFERENCES `stock_productos` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
