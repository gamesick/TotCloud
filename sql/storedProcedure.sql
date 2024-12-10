DROP PROCEDURE IF EXISTS verificar_problemas_config;
DELIMITER $$

CREATE PROCEDURE verificar_problemas_config()
BEGIN
    -- Limpiar la tabla de auditoría para mostrar solo problemas actuales
    TRUNCATE TABLE PROBLEMAS;

    -- 1. Verificar que cada USUARIO está en un grupo válido
    INSERT INTO PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'USUARIO_GRUPO',
           CONCAT('Usuario ID=', USUARIO.idUsuario, ' con nombreUsuario=', USUARIO.nombreUsuario, ' no pertenece a un grupo válido.')
    FROM USUARIO
    LEFT JOIN GRUPO ON USUARIO.idGrupo = GRUPO.idGrupo
    WHERE USUARIO.idGrupo IS NULL OR GRUPO.idGrupo IS NULL;

    -- 2. Verificación en DB_CONFIG
    -- Condiciones:
    -- nombreDB != '', motor != '', direccionIP != ''
    -- cpu > 0, puerto >0, usuarios >0, almacenamiento >0
    INSERT INTO PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'DB_CONFIG',
           CONCAT('DB_CONFIG id=', DB_CONFIG.idDBConfig, ' (', DB_CONFIG.nombreDB, ') tiene campos vacíos o =0.')
    FROM DB_CONFIG
    WHERE nombreDB = '' OR motor = '' OR direccionIP = ''
       OR cpu <= 0 OR puerto <=0 OR usuarios <=0 OR almacenamiento <=0;

    -- 3. Verificación en CS_CONFIG y CLOUD_STORAGE
    -- CS_CONFIG: nombreCS != '', almacenamiento >0
    -- CLOUD_STORAGE: limiteSubida>0, velocidad>0, latencia>0
    INSERT INTO PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'CS_CONFIG',
           CONCAT('CS_CONFIG id=', CS_CONFIG.idCSConfig, ' (', CS_CONFIG.nombreCS, ') o su Cloud_Storage asociada tiene campos vacíos o =0.')
    FROM CS_CONFIG
    JOIN CLOUD_STORAGE ON CS_CONFIG.idCloudStorage = CLOUD_STORAGE.idCloudStorage
    WHERE CS_CONFIG.nombreCS = ''
       OR CS_CONFIG.almacenamiento <= 0
       OR CLOUD_STORAGE.limiteSubida <=0
       OR CLOUD_STORAGE.velocidad <=0
       OR CLOUD_STORAGE.latencia <=0;

    -- 4. Verificación en VC_CONFIG
    -- Campos texto: nombreVC != '', calidad != '', idioma != ''
    -- Campos numéricos: anchoBanda>0, maxParticipantes>0
    INSERT INTO PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'VC_CONFIG',
           CONCAT('VC_CONFIG id=', VC_CONFIG.idVCConfig, ' (', VC_CONFIG.nombreVC, ') tiene campos vacíos o =0.')
    FROM VC_CONFIG
    WHERE nombreVC = ''
       OR calidad = ''
       OR idioma = ''
       OR anchoBanda <=0
       OR maxParticipantes <=0;

    -- Mostrar los problemas detectados en esta ejecución
    SELECT * 
    FROM PROBLEMAS
    ORDER BY fecha DESC;

END$$

DELIMITER ;
