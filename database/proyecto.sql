-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-03-2026 a las 01:22:48
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `choferes`
--

DROP TABLE IF EXISTS `choferes`;
CREATE TABLE IF NOT EXISTS `choferes` (
  `ID_chofer` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  PRIMARY KEY (`ID_chofer`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `choferes`
--

INSERT INTO `choferes` (`ID_chofer`, `RIF_cedula`, `nombre`, `telefono`) VALUES
(1, 'V24464600', 'Neomar Brito', '04241911109'),
(2, 'V13534375', 'Neomar Brito', '04141803696'),
(3, 'V11040705', 'Renne Monroy', '04123153809'),
(4, 'V14310935', 'Antonio Reina', '04241961727');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `ID_cliente` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ID_cliente`),
  UNIQUE KEY `RIF_cedula` (`RIF_cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ID_cliente`, `RIF_cedula`, `nombre`, `telefono`) VALUES
(6, 'J001281487', 'Gran Saso', '04141234567'),
(7, 'J000000', 'Ponderosa', '04141234567'),
(8, 'J0000000', 'Tu Verdura', '04141234567'),
(9, 'J404814590', 'Fresco y Congelado', '04141234567'),
(10, 'J075436695', 'Afaltadora', '04141234567');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fletes`
--

DROP TABLE IF EXISTS `fletes`;
CREATE TABLE IF NOT EXISTS `fletes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `valor` int NOT NULL,
  `cancelado` tinyint NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `fletes`
--

INSERT INTO `fletes` (`id`, `id_cliente`, `origen`, `destino`, `estado`, `valor`, `cancelado`, `fecha`) VALUES
(1, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 1, '0000-00-00'),
(2, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(3, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(4, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(5, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(6, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `codigo` int NOT NULL,
  `nombre` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `precio_unidad` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Contraseña` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token_recuperacion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `mail` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Email`, `Contraseña`, `token_recuperacion`, `token_expiracion`) VALUES
(7, 'neybri@gmail.com', '$2y$10$UUkSwxvP.J4u7u/EGKsVp.A', NULL, NULL),
(8, 'Luisgalindez@gmail.com', '$2y$10$obPxscL8gNYl1h7YdI50uOD', NULL, NULL),
(13, 'a-j-v-r@hotmail.com', '$2y$10$lyjWFSaWQs.0PUL5TXjr2OdjIH8raOU9xIAKnQWmgL4kwpBACMCZ6', NULL, NULL),
(14, 'neybriramos@gmail.com', '$2y$10$zbPgtnoWf.M6c6icmH8OoeqxHjlA0IE69p20GXh1UA1r/4.uc25ES', '234976', '2026-03-01 00:02:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_id` int NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa` (`placa`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `placa`, `marca`, `modelo`, `cliente_id`) VALUES
(1, 'AGX05T', 'Chevrolet', 'optra', 0),
(3, 'AGTPO7', 'Chevrolet', 'optra', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
