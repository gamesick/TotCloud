SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS copia_incremental_diaria
ON SCHEDULE EVERY 1 DAY
STARTS '2024-12-11 19:00:00' 
DO
CALL realizar_copia_incremental();

SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS verificar_problemas_diarios
ON SCHEDULE EVERY 1 DAY
STARTS '2024-12-11 19:00:00' 
DO
CALL verificar_problemas_config();

