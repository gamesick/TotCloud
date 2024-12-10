DELIMITER $$

CREATE PROCEDURE verificar_problemas_config()
BEGIN
    -- Limpiar registros antiguos de auditoría (más de 30 días) para evitar crecimiento ilimitado
    DELETE FROM AUDITORIA_PROBLEMAS WHERE fecha < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- 1. Verificación de contraseñas de USUARIO
    -- Consideramos inválida una contraseña vacía ('') o nula.
    INSERT INTO AUDITORIA_PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'USUARIO_CONTRASENYA',
           CONCAT('Usuario ID=', USUARIO.idUsuario, ' con nombreUsuario=', USUARIO.nombreUsuario, ' tiene una contraseña inválida.')
    FROM USUARIO
    WHERE (contrasenya IS NULL OR contrasenya = '');
    
    -- 2. Verificación de DB_CONFIG
    -- Condiciones inválidas: direccionIP='0', cpu=0, puerto=0, usuarios=0, almacenamiento=0
    INSERT INTO AUDITORIA_PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'DB_CONFIG',
           CONCAT('Config DB con idDBConfig=', DB_CONFIG.idDBConfig, ' (', DB_CONFIG.nombreDB, ') tiene valores inválidos (IP=0 o CPU=0 o Puerto=0 o Usuarios=0 o Almacenamiento=0).')
    FROM DB_CONFIG
    WHERE direccionIP = '0'
       OR cpu = 0
       OR puerto = 0
       OR usuarios = 0
       OR almacenamiento = 0;
    
    -- 3. Verificar que todos los USUARIOS están en un grupo válido
    -- Un grupo válido significa que idGrupo no es nulo y que existe en la tabla GRUPO.
    INSERT INTO AUDITORIA_PROBLEMAS (fecha, tipo_problema, descripcion)
    SELECT NOW(), 'USUARIO_GRUPO',
           CONCAT('Usuario ID=', USUARIO.idUsuario, ' con nombreUsuario=', USUARIO.nombreUsuario, ' no pertenece a un grupo válido.')
    FROM USUARIO
    LEFT JOIN GRUPO ON USUARIO.idGrupo = GRUPO.idGrupo
    WHERE USUARIO.idGrupo IS NULL OR GRUPO.idGrupo IS NULL;
    
    -- Mostrar los problemas detectados en esta ejecución (últimos 5 minutos)
    SELECT * 
    FROM AUDITORIA_PROBLEMAS
    WHERE fecha >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY fecha DESC;

END$$

DELIMITER ;