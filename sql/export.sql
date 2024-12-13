-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2024 a las 21:09:58
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
-- Base de datos: `totcloud`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cloud_storage`
--

CREATE TABLE `cloud_storage` (
  `idCloudStorage` int(8) NOT NULL,
  `nombreCS` varchar(64) NOT NULL,
  `limiteSubida` int(16) NOT NULL,
  `velocidad` int(16) NOT NULL,
  `latencia` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cloud_storage`
--

INSERT INTO `cloud_storage` (`idCloudStorage`, `nombreCS`, `limiteSubida`, `velocidad`, `latencia`) VALUES
(5, 'Drive', 300, 600, 5),
(6, 'SharePoint', 400, 800, 8),
(10, 'Drive2', 100, 100, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cs_config`
--

CREATE TABLE `cs_config` (
  `idCSConfig` int(8) NOT NULL,
  `nombreCS` varchar(64) NOT NULL,
  `almacenamiento` int(16) NOT NULL,
  `idCloudStorage` int(8) NOT NULL,
  `idPersona` int(8) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cs_config`
--

INSERT INTO `cs_config` (`idCSConfig`, `nombreCS`, `almacenamiento`, `idCloudStorage`, `idPersona`, `last_modified`) VALUES
(4, 'Drive', 3000, 5, 4, '2024-12-13 19:43:07'),
(5, 'SharePoint', 4000, 6, 4, '2024-12-13 19:43:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data_base`
--

CREATE TABLE `data_base` (
  `idDataBase` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `data_base`
--

INSERT INTO `data_base` (`idDataBase`) VALUES
(4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `db_config`
--

CREATE TABLE `db_config` (
  `idDBConfig` int(8) NOT NULL,
  `nombreDB` varchar(64) NOT NULL,
  `motor` varchar(64) NOT NULL,
  `usuarios` int(16) NOT NULL,
  `almacenamiento` int(16) NOT NULL,
  `cpu` int(2) NOT NULL,
  `puerto` int(8) NOT NULL,
  `direccionIP` varchar(16) NOT NULL,
  `idDataBase` int(8) NOT NULL,
  `idPersona` int(8) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `db_config`
--

INSERT INTO `db_config` (`idDBConfig`, `nombreDB`, `motor`, `usuarios`, `almacenamiento`, `cpu`, `puerto`, `direccionIP`, `idDataBase`, `idPersona`, `last_modified`) VALUES
(4, 'marketing_db', 'MariaDB', 7, 150, 4, 3307, '192.168.1.13', 4, 4, '2024-12-13 19:43:07'),
(5, 'hr_db', 'Oracle', 3, 75, 2, 1521, '192.168.1.14', 4, 4, '2024-12-13 19:43:07'),
(6, 'it_db', 'SQL Server', 6, 300, 6, 1433, '192.168.1.15', 4, 4, '2024-12-13 19:43:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapa`
--

CREATE TABLE `etapa` (
  `idEtapa` int(8) NOT NULL,
  `nombreEtapa` varchar(64) NOT NULL,
  `descripcion` varchar(256) NOT NULL,
  `idPersonal` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etapa`
--

INSERT INTO `etapa` (`idEtapa`, `nombreEtapa`, `descripcion`, `idPersonal`) VALUES
(4, 'En Pruebas', 'a', 4),
(5, 'Disponible', 'a', 4),
(6, 'No Disponible', 'a', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `idGrupo` int(8) NOT NULL,
  `nombreGrupo` varchar(64) NOT NULL,
  `descripcion` varchar(256) DEFAULT NULL,
  `idPersonal` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`idGrupo`, `nombreGrupo`, `descripcion`, `idPersonal`) VALUES
(4, 'Vip', 'Gente relacionada con la empresa', 4),
(5, 'Pro', 'Plan mas caro disponible', 4),
(6, 'Basic', 'Plan gratis', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historico_config`
--

CREATE TABLE `historico_config` (
  `idBackup` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `tabla` varchar(64) DEFAULT NULL,
  `idRegistro` int(11) DEFAULT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organizacion`
--

CREATE TABLE `organizacion` (
  `idOrganizacion` int(8) NOT NULL,
  `nombreOrganizacion` varchar(64) NOT NULL,
  `direccion` varchar(128) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `organizacion`
--

INSERT INTO `organizacion` (`idOrganizacion`, `nombreOrganizacion`, `direccion`, `telefono`, `email`) VALUES
(4, 'SkyNet', '101 Cyber Rd, Matrix City', '444555666', 'contact@skynet.com'),
(5, 'AlphaTech', '202 Innovation Blvd, Silicon Valley', '333444555', 'info@alphatech.com'),
(6, 'BetaSolutions', '303 Enterprise St, Business Town', '222333444', 'support@betasolutions.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `idPersona` int(8) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `apellido` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`idPersona`, `nombre`, `apellido`, `email`) VALUES
(4, 'Laura', 'King', 'laura.king@example.com'),
(5, 'Mike', 'Brown', 'mike.brown@example.com'),
(6, 'Sophie', 'Green', 'sophie.green@example.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `idPersonal` int(8) NOT NULL,
  `nombrePersonal` varchar(64) NOT NULL,
  `contrasenya` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`idPersonal`, `nombrePersonal`, `contrasenya`) VALUES
(4, 'laura.king', '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `privilegios`
--

CREATE TABLE `privilegios` (
  `idPrivilegio` int(8) NOT NULL,
  `nombrePrivilegio` varchar(64) NOT NULL,
  `descripcion` varchar(256) DEFAULT NULL,
  `idPersonal` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `privilegios`
--

INSERT INTO `privilegios` (`idPrivilegio`, `nombrePrivilegio`, `descripcion`, `idPersonal`) VALUES
(4, 'Tier 3', 'Privilegios para usar todo', 4),
(5, 'Tier 2', 'Privilegios para todo menos Cloud Storage', 4),
(6, 'Tier 1', 'Privilegios solo para usar las Video Conference', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `problemas`
--

CREATE TABLE `problemas` (
  `idProblema` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo_problema` varchar(64) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r_grupo_privilegios`
--

CREATE TABLE `r_grupo_privilegios` (
  `idGrupo` int(8) NOT NULL,
  `idPrivilegio` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `r_grupo_privilegios`
--

INSERT INTO `r_grupo_privilegios` (`idGrupo`, `idPrivilegio`) VALUES
(4, 4),
(5, 5),
(6, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r_persona_servicio`
--

CREATE TABLE `r_persona_servicio` (
  `idServicio` int(8) NOT NULL,
  `idPersona` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `r_persona_servicio`
--

INSERT INTO `r_persona_servicio` (`idServicio`, `idPersona`) VALUES
(4, 4),
(5, 5),
(6, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(8) NOT NULL,
  `tipoServicio` varchar(256) NOT NULL,
  `descripcion` varchar(256) DEFAULT NULL,
  `idEtapa` int(8) NOT NULL,
  `idPrivilegio` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idServicio`, `tipoServicio`, `descripcion`, `idEtapa`, `idPrivilegio`) VALUES
(4, 'Data Base', 'PAAS', 5, 5),
(5, 'Cloud Storage', 'SAAS', 5, 4),
(6, 'Cloud Storage', 'SAAS', 5, 4),
(7, 'Cloud Storage', 'SAAS', 5, 4),
(8, 'Video Conference', 'SAAS', 5, 6),
(9, 'Cloud Storage', 'SAAS', 5, 5),
(10, 'Cloud Storage', 'SAAS', 5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(8) NOT NULL,
  `nombreUsuario` varchar(64) NOT NULL,
  `contrasenya` varchar(256) NOT NULL,
  `idOrganizacion` int(8) NOT NULL,
  `idGrupo` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nombreUsuario`, `contrasenya`, `idOrganizacion`, `idGrupo`) VALUES
(5, 'mike.brown', '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a', 5, NULL),
(6, 'sophie.green', '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a', 6, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vc_config`
--

CREATE TABLE `vc_config` (
  `idVCConfig` int(8) NOT NULL,
  `nombreVC` varchar(64) NOT NULL,
  `calidad` varchar(8) NOT NULL,
  `anchoBanda` int(16) NOT NULL,
  `maxParticipantes` int(8) NOT NULL,
  `idioma` varchar(64) NOT NULL,
  `idVideoConference` int(8) NOT NULL,
  `idPersona` int(8) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vc_config`
--

INSERT INTO `vc_config` (`idVCConfig`, `nombreVC`, `calidad`, `anchoBanda`, `maxParticipantes`, `idioma`, `idVideoConference`, `idPersona`, `last_modified`) VALUES
(4, 'Zoom', '480p', 10000, 1000, 'Deutsch', 8, 4, '2024-12-13 19:43:07'),
(5, 'Discord', '1080p', 8000, 800, 'Italian', 8, 4, '2024-12-13 19:43:07'),
(6, 'Meet', '720p', 9000, 900, 'English', 8, 4, '2024-12-13 19:43:07'),
(7, 'DC', '1080p', 1000, 20, 'English', 8, 4, '2024-12-13 19:55:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video_conference`
--

CREATE TABLE `video_conference` (
  `idVideoConference` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `video_conference`
--

INSERT INTO `video_conference` (`idVideoConference`) VALUES
(8);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cloud_storage`
--
ALTER TABLE `cloud_storage`
  ADD PRIMARY KEY (`idCloudStorage`),
  ADD UNIQUE KEY `nombreCS` (`nombreCS`);

--
-- Indices de la tabla `cs_config`
--
ALTER TABLE `cs_config`
  ADD PRIMARY KEY (`idCSConfig`),
  ADD KEY `idCloudStorage` (`idCloudStorage`),
  ADD KEY `idPersona` (`idPersona`);

--
-- Indices de la tabla `data_base`
--
ALTER TABLE `data_base`
  ADD PRIMARY KEY (`idDataBase`);

--
-- Indices de la tabla `db_config`
--
ALTER TABLE `db_config`
  ADD PRIMARY KEY (`idDBConfig`),
  ADD KEY `idDataBase` (`idDataBase`),
  ADD KEY `idPersona` (`idPersona`);

--
-- Indices de la tabla `etapa`
--
ALTER TABLE `etapa`
  ADD PRIMARY KEY (`idEtapa`),
  ADD KEY `idPersonal` (`idPersonal`);

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`idGrupo`),
  ADD UNIQUE KEY `nombreGrupo` (`nombreGrupo`),
  ADD KEY `idPersonal` (`idPersonal`);

--
-- Indices de la tabla `historico_config`
--
ALTER TABLE `historico_config`
  ADD PRIMARY KEY (`idBackup`);

--
-- Indices de la tabla `organizacion`
--
ALTER TABLE `organizacion`
  ADD PRIMARY KEY (`idOrganizacion`),
  ADD UNIQUE KEY `nombreOrganizacion` (`nombreOrganizacion`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`idPersona`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`idPersonal`),
  ADD UNIQUE KEY `nombrePersonal` (`nombrePersonal`);

--
-- Indices de la tabla `privilegios`
--
ALTER TABLE `privilegios`
  ADD PRIMARY KEY (`idPrivilegio`),
  ADD KEY `idPersonal` (`idPersonal`);

--
-- Indices de la tabla `problemas`
--
ALTER TABLE `problemas`
  ADD PRIMARY KEY (`idProblema`),
  ADD UNIQUE KEY `tipo_problema` (`tipo_problema`,`descripcion`(200));

--
-- Indices de la tabla `r_grupo_privilegios`
--
ALTER TABLE `r_grupo_privilegios`
  ADD PRIMARY KEY (`idGrupo`,`idPrivilegio`),
  ADD KEY `idPrivilegio` (`idPrivilegio`);

--
-- Indices de la tabla `r_persona_servicio`
--
ALTER TABLE `r_persona_servicio`
  ADD PRIMARY KEY (`idServicio`,`idPersona`),
  ADD KEY `idPersona` (`idPersona`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idServicio`),
  ADD KEY `idPrivilegio` (`idPrivilegio`),
  ADD KEY `idEtapa` (`idEtapa`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `nombreUsuario` (`nombreUsuario`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `idOrganizacion` (`idOrganizacion`);

--
-- Indices de la tabla `vc_config`
--
ALTER TABLE `vc_config`
  ADD PRIMARY KEY (`idVCConfig`),
  ADD KEY `idVideoConference` (`idVideoConference`),
  ADD KEY `idPersona` (`idPersona`);

--
-- Indices de la tabla `video_conference`
--
ALTER TABLE `video_conference`
  ADD PRIMARY KEY (`idVideoConference`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cloud_storage`
--
ALTER TABLE `cloud_storage`
  MODIFY `idCloudStorage` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cs_config`
--
ALTER TABLE `cs_config`
  MODIFY `idCSConfig` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `data_base`
--
ALTER TABLE `data_base`
  MODIFY `idDataBase` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `db_config`
--
ALTER TABLE `db_config`
  MODIFY `idDBConfig` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `grupo`
--
ALTER TABLE `grupo`
  MODIFY `idGrupo` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historico_config`
--
ALTER TABLE `historico_config`
  MODIFY `idBackup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `idPersona` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `problemas`
--
ALTER TABLE `problemas`
  MODIFY `idProblema` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vc_config`
--
ALTER TABLE `vc_config`
  MODIFY `idVCConfig` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `video_conference`
--
ALTER TABLE `video_conference`
  MODIFY `idVideoConference` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cloud_storage`
--
ALTER TABLE `cloud_storage`
  ADD CONSTRAINT `cloud_storage_ibfk_1` FOREIGN KEY (`idCloudStorage`) REFERENCES `servicio` (`idServicio`);

--
-- Filtros para la tabla `cs_config`
--
ALTER TABLE `cs_config`
  ADD CONSTRAINT `cs_config_ibfk_1` FOREIGN KEY (`idCloudStorage`) REFERENCES `cloud_storage` (`idCloudStorage`),
  ADD CONSTRAINT `cs_config_ibfk_2` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `data_base`
--
ALTER TABLE `data_base`
  ADD CONSTRAINT `data_base_ibfk_1` FOREIGN KEY (`idDataBase`) REFERENCES `servicio` (`idServicio`);

--
-- Filtros para la tabla `db_config`
--
ALTER TABLE `db_config`
  ADD CONSTRAINT `db_config_ibfk_1` FOREIGN KEY (`idDataBase`) REFERENCES `data_base` (`idDataBase`),
  ADD CONSTRAINT `db_config_ibfk_2` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `etapa`
--
ALTER TABLE `etapa`
  ADD CONSTRAINT `etapa_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `privilegios`
--
ALTER TABLE `privilegios`
  ADD CONSTRAINT `privilegios_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `r_grupo_privilegios`
--
ALTER TABLE `r_grupo_privilegios`
  ADD CONSTRAINT `r_grupo_privilegios_ibfk_1` FOREIGN KEY (`idGrupo`) REFERENCES `grupo` (`idGrupo`),
  ADD CONSTRAINT `r_grupo_privilegios_ibfk_2` FOREIGN KEY (`idPrivilegio`) REFERENCES `privilegios` (`idPrivilegio`);

--
-- Filtros para la tabla `r_persona_servicio`
--
ALTER TABLE `r_persona_servicio`
  ADD CONSTRAINT `r_persona_servicio_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`),
  ADD CONSTRAINT `r_persona_servicio_ibfk_2` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_1` FOREIGN KEY (`idPrivilegio`) REFERENCES `privilegios` (`idPrivilegio`),
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`idEtapa`) REFERENCES `etapa` (`idEtapa`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`idGrupo`) REFERENCES `grupo` (`idGrupo`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`idOrganizacion`) REFERENCES `organizacion` (`idOrganizacion`),
  ADD CONSTRAINT `usuario_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `vc_config`
--
ALTER TABLE `vc_config`
  ADD CONSTRAINT `vc_config_ibfk_1` FOREIGN KEY (`idVideoConference`) REFERENCES `video_conference` (`idVideoConference`),
  ADD CONSTRAINT `vc_config_ibfk_2` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `video_conference`
--
ALTER TABLE `video_conference`
  ADD CONSTRAINT `video_conference_ibfk_1` FOREIGN KEY (`idVideoConference`) REFERENCES `servicio` (`idServicio`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
