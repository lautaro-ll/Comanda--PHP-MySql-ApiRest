-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 17, 2021 at 08:56 PM
-- Server version: 8.0.13-4
-- PHP Version: 7.2.24-0ubuntu0.18.04.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `K0xjxR4mKN`
--

-- --------------------------------------------------------

--
-- Table structure for table `encuesta`
--

CREATE TABLE `encuesta` (
  `id` int(11) NOT NULL,
  `codigo-mesa` varchar(5) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `codigo-pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `calif-mesa` int(11) NOT NULL,
  `calif-resto` int(11) NOT NULL,
  `calid-mozo` int(11) NOT NULL,
  `calif-cocinero` int(11) NOT NULL,
  `experiencia` varchar(66) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `codigo-identificacion` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `codigo-pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mesas`
--

INSERT INTO `mesas` (`id`, `codigo-identificacion`, `codigo-pedido`, `estado`) VALUES
(1, 'blue', 'SCIxC', 'con cliente esperando pedido'),
(2, 'green', 'jUO5F', 'con cliente esperando pedido'),
(3, 'red', 'zUDjP', 'cerrada'),
(4, 'black', 'eUV0k', 'con cliente comiendo'),
(5, 'white', 'QOhxp', 'cerrada');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `foto` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `codigo-pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `idmesa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `estado` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `tiempo-estimado` datetime DEFAULT NULL,
  `tiempo-finalizado` datetime DEFAULT NULL,
  `tiempo-entregado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente`, `foto`, `codigo-pedido`, `idmesa`, `idproducto`, `precio`, `idusuario`, `estado`, `tiempo-estimado`, `tiempo-finalizado`, `tiempo-entregado`) VALUES
(4, 'Pepe', 'link1', 'eUV0k', 4, 7, 430, 25, 'servido', '2021-05-17 00:20:00', '2021-05-17 00:15:00', '2021-05-17 00:35:00'),
(5, 'Pepe', 'link1', 'eUV0k', 4, 9, 450, 26, 'servido', '2021-05-17 00:40:00', '2021-05-17 00:33:00', '2021-05-17 00:35:00'),
(6, 'Tato', 'link2', 'SCIxC', 1, 6, 120, 27, 'en preparacion', '2021-05-17 00:20:00', NULL, NULL),
(7, 'Tato', 'link2', 'SCIxC', 1, 5, 250, 20, 'listo para servir', '2021-05-17 00:05:00', '2021-05-17 00:02:00', NULL),
(8, 'Chiche', 'link3', 'jUO5F', 2, 23, 860, 24, 'cancelado', '2021-05-17 00:45:00', NULL, NULL),
(9, 'Chiche', 'link3', 'jUO5F', 2, 20, 480, 24, 'en preparacion', '2021-05-17 00:30:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `producto` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `tipo-usuario` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `precio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `tipo`, `producto`, `tipo-usuario`, `precio`) VALUES
(5, 'Bebida', 'Irish Red Ale', 'Cervecero', 250),
(6, 'Comida', 'Empanada', 'Cocinero', 120),
(7, 'Bebida', 'Negroni', 'Bartender', 430),
(8, 'Bebida', 'Golden Ale', 'Cervecero', 250),
(9, 'Comida', 'Rabas', 'Cocinero', 450),
(10, 'Bebida', 'Gin Tonic', 'Bartender', 320),
(16, 'Bebida', 'American IPA', 'Cervecero', 250),
(19, 'Comida', 'Cornalitos', 'Cocinero', 380),
(20, 'Comida', 'Tortilla de papa', 'Cocinero', 480),
(21, 'Bebida', 'Dry Stout', 'Cervecero', 250),
(22, 'Bebida', 'Ferroviario', 'Bartender', 260),
(23, 'Comida', 'Langostinos empanados', 'Cocinero', 860);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `usuario` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `fecha-alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha-baja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `tipo`, `nombre`, `usuario`, `clave`, `fecha-baja`) VALUES
(17, 'Socio', 'Collete Melbourne', 'cmelbourne0', 'g91WvBCQV', NULL),
(18, 'Cervecero', 'Shelli Voce', 'svoce1', 'rfooTj', NULL),
(19, 'Mozo', 'Elie Mildner', 'emildner2', 'xXWWc2dWk0', NULL),
(20, 'Cervecero', 'Faustine Lowdiane', 'flowdiane3', 'CTMmtrp4', NULL),
(21, 'Mozo', 'Chet Austick', 'caustick4', 'ItHFZaqGeMzJ', NULL),
(22, 'Mozo', 'Courtnay Keasley', 'ckeasley5', 'U0rwUb7XbNZ', NULL),
(23, 'Bartender', 'Inglis Moorwood', 'imoorwood6', '0P7rxr', NULL),
(24, 'Cocinero', 'Amity Maydwell', 'amaydwell7', 'WNz1HVHiwIf', NULL),
(25, 'Bartender', 'Francklin Dale', 'fdale8', 'G4302enQ0AJ', NULL),
(26, 'Cocinero', 'Shelden Gleed', 'sgleed9', 'uBXTTDLcY', NULL),
(27, 'Cocinero', 'Donn Barnsdall', 'dbarnsdalla', '1lCn0BjD', NULL),
(28, 'Socio', 'Judith Colbourn', 'jcolbournb', 'jqE0RCp3CJN', NULL),
(29, 'Socio', 'Emalia Sogg', 'esoggc', 'P5hcVG', NULL),
(30, 'Mozo', 'Brad Lyfield', 'blyfieldd', '6K1ohTWddw', NULL),
(31, 'Bartender', 'Cybil Duffet', 'cduffete', 'd4TuDhM9Rrz', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
