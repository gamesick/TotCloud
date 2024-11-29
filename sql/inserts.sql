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
(4, '$2y$10$M9PdDxEX.n5VEKz4JDB09OEd57h59NhriVqvljNAdEUL9U2MgshE6', 'laura.king');

-- 4. GRUPO
INSERT INTO GRUPO (idGrupo, nombreGrupo, descripcion, idPersonal) VALUES
(4, 'Marketing', 'Group for marketing team', 4),
(5, 'HR', 'Human Resources group', 4),
(6, 'IT Support', 'Group for IT support staff', 4);

-- 3. USUARIO
INSERT INTO USUARIO (idUsuario, nombreUsuario, contrasenya, idOrganizacion) VALUES
(5, 'mike.brown', '$2y$10$M9PdDxEX.n5VEKz4JDB09OEd57h59NhriVqvljNAdEUL9U2MgshE6', 5),
(6, 'sophie.green', '$2y$10$M9PdDxEX.n5VEKz4JDB09OEd57h59NhriVqvljNAdEUL9U2MgshE6', 6);

-- 5. PRIVILEGIOS
INSERT INTO PRIVILEGIOS (idPrivilegio, nombrePrivilegio, descripcion, idPersonal) VALUES
(4, 'ACCESS_API', 'Privilege to access APIs', 4),
(5, 'MANAGE_USERS', 'Privilege to manage user accounts', 4),
(6, 'VIEW_ANALYTICS', 'Privilege to view analytics data', 4);

-- 6. ETAPA
INSERT INTO ETAPA (idEtapa, nombreEtapa, estado, idPersonal) VALUES
(4, 'Integraci贸n', 'en_proceso', 4),
(5, 'Testing', 'completada', 4),
(6, 'Deploy', 'en_proceso', 4);

-- 7. SERVICIO
INSERT INTO SERVICIO (idServicio, tipoServicio, descripcion, idEtapa) VALUES
(4, 'Correo Electr贸nico', 'Servicio de correo electr贸nico empresarial', 4),
(5, 'Backup', 'Servicio de respaldo de datos en la nube', 5),
(6, 'Streaming', 'Servicio de transmisi贸n de video en tiempo real', 6);

-- 9. CLOUD_STORAGE
INSERT INTO CLOUD_STORAGE (idCloudStorage, limiteSubida, velocidad, latencia) VALUES
(4, 3000, 600, 5),
(5, 4000, 800, 8),
(6, 5000, 1000, 3);

-- 10. DATA_BASE
INSERT INTO DATA_BASE (idDataBase) VALUES
(4),
(5),
(6);

-- 11. VIDEO_CONFERENCE
INSERT INTO VIDEO_CONFERENCE (idVideoConference) VALUES
(4),
(5),
(6);

-- 12. CS_CONFIG
INSERT INTO CS_CONFIG (idCSConfig, almacenamiento, idCloudStorage) VALUES
(4, 3000, 4),
(5, 4000, 5),
(6, 5000, 6);

-- 13. DB_CONFIG
INSERT INTO DB_CONFIG (idDBConfig, nombreDB, motor, usuarios, almacenamiento, cpu, puerto, direccionIP, idDataBase) VALUES
(4, 'marketing_db', 'MariaDB', 7, 150, 4, 3307, '192.168.1.13', 4),
(5, 'hr_db', 'Oracle', 3, 75, 2, 1521, '192.168.1.14', 5),
(6, 'it_db', 'SQL Server', 6, 300, 6, 1433, '192.168.1.15', 6);

-- 14. VC_CONFIG
INSERT INTO VC_CONFIG (idVCConfig, calidad, anchoBanda, maxParticipantes, idioma, idVideoConference) VALUES
(4, 'UltraHD', 10000, 1000, 'German', 4),
(5, 'HD', 8000, 800, 'Italian', 5),
(6, 'FullHD', 9000, 900, 'Japanese', 6);

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