-- 4. ORGANIZACION
INSERT INTO ORGANIZACION (idOrganizacion, nombreOrganizacion, direccion, telefono, email) VALUES
(4, 'SkyNet', '101 Cyber Rd, Matrix City', '444555666', 'contact@skynet.com'),
(5, 'AlphaTech', '202 Innovation Blvd, Silicon Valley', '333444555', 'info@alphatech.com'),
(6, 'BetaSolutions', '303 Enterprise St, Business Town', '222333444', 'support@betasolutions.com');

-- 8. PERSONA
INSERT INTO PERSONA (idPersona, nombre, apellido, email) VALUES
(4, 'Laura', 'King', 'laura.king@example.com'),
(5, 'Mike', 'Brown', 'mike.brown@example.com'),
(6, 'Sophie', 'Green', 'sophie.green@example.com');

-- 2. PERSONAL
INSERT INTO PERSONAL (idPersonal, contrasenya, nombrePersonal) VALUES
(4, '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a', 'laura.king');

-- 4. GRUPO
INSERT INTO GRUPO (idGrupo, nombreGrupo, descripcion, idPersonal) VALUES
(4, 'Vip', 'Gente relacionada con la empresa', 4),
(5, 'Pro', 'Plan mas caro disponible', 4),
(6, 'Basic', 'Plan gratis', 4);

-- 3. USUARIO
INSERT INTO USUARIO (idUsuario, nombreUsuario, contrasenya, idOrganizacion) VALUES
(5, 'mike.brown', '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a', 5),
(6, 'sophie.green', '$2y$10$JIvQpR5r8Ht96tCfR2s8w.WEZsERoLRRNAeGQ6/G.B/lMUyQhf25a', 6);

-- 5. PRIVILEGIOS
INSERT INTO PRIVILEGIOS (idPrivilegio, nombrePrivilegio, descripcion, idPersonal) VALUES
(4, 'Tier 3', 'Privilegios para usar todo', 4),
(5, 'Tier 2', 'Privilegios para todo menos Cloud Storage', 4),
(6, 'Tier 1', 'Privilegios solo para usar las Video Conference', 4);

-- 6. ETAPA
INSERT INTO ETAPA (idEtapa, nombreEtapa, descripcion, idPersonal) VALUES
(4, 'En Pruebas', 'a', 4),
(5, 'Disponible', 'a', 4),
(6, 'No Disponible', 'a', 4);

-- 7. SERVICIO
INSERT INTO SERVICIO (idServicio, tipoServicio, descripcion, idEtapa, idPrivilegio) VALUES
(4, 'Data Base', 'PAAS', 4, 5),
(5, 'Cloud Storage', 'SAAS', 4, 4),
(6, 'Cloud Storage', 'SAAS', 4, 4),
(7, 'Cloud Storage', 'SAAS', 4, 4),
(8, 'Video Conference', 'SAAS', 4, 6);

-- 9. CLOUD_STORAGE
INSERT INTO CLOUD_STORAGE (idCloudStorage, limiteSubida, velocidad, latencia) VALUES
(5, 3000, 600, 5, 5),
(6, 4000, 800, 8, 5),
(7, 5000, 1000, 3, 5);

-- 10. DATA_BASE
INSERT INTO DATA_BASE (idDataBase) VALUES
(4);

-- 11. VIDEO_CONFERENCE
INSERT INTO VIDEO_CONFERENCE (idVideoConference) VALUES
(8);

-- 12. CS_CONFIG
INSERT INTO CS_CONFIG (idCSConfig, nombreCS, almacenamiento, idCloudStorage, idPersona) VALUES
(4, 'Drive', 3000, 1, 4),
(5, 'SharePoint', 4000, 2, 4),
(6, 'OneDrive', 5000, 3, 4);

-- 13. DB_CONFIG
INSERT INTO DB_CONFIG (idDBConfig, nombreDB, motor, usuarios, almacenamiento, cpu, puerto, direccionIP, idDataBase, idPersona) VALUES
(4, 'marketing_db', 'MariaDB', 7, 150, 4, 3307, '192.168.1.13', 1, 4),
(5, 'hr_db', 'Oracle', 3, 75, 2, 1521, '192.168.1.14', 1, 4),
(6, 'it_db', 'SQL Server', 6, 300, 6, 1433, '192.168.1.15', 1, 4);

-- 14. VC_CONFIG
INSERT INTO VC_CONFIG (idVCConfig, nombreVC, calidad, anchoBanda, maxParticipantes, idioma, idVideoConference, idPersona) VALUES
(4, 'Zoom', '480p', 10000, 1000, 'Deutsch', 1, 4),
(5, 'Discord', '1080p', 8000, 800, 'Italian', 1, 4),
(6, 'Meet', '720p', 9000, 900, 'English', 1, 4);

-- 15. R_PERSONA_SERVICIO
INSERT INTO R_PERSONA_SERVICIO (idServicio, idPersona) VALUES
(4, 4),
(5, 5),
(6, 6);

-- 16. R_GRUPO_PRIVILEGIOS
INSERT INTO R_GRUPO_PRIVILEGIOS (idGrupo, idPrivilegio) VALUES
(4, 4),
(5, 5),
(6, 6);