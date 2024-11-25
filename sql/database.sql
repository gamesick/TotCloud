CREATE DATABASE TOTCLOUD; 


CREATE TABLE ORGANIZACION ( 
    idOrganizacion INT(8) PRIMARY KEY, 
    nombreOrganizacion VARCHAR(64) NOT NULL UNIQUE, 
    direccion VARCHAR(128) NOT NULL, 
    telefono VARCHAR(9) NOT NULL, 
    email VARCHAR(64) NOT NULL 
); 


CREATE TABLE USUARIO (
 	idUsuario INT(8) PRIMARY KEY, 
    nombreUsuario VARCHAR(64) NOT NULL UNIQUE, 
    idOrganizacion INT(8) NOT NULL, 
    FOREIGN KEY (idOrganizacion) REFERENCES ORGANIZACION (idOrganizacion) 
); 


CREATE TABLE PERSONAL ( 
    idPersonal INT(8) PRIMARY KEY, 
    nombrePersonal VARCHAR(64) NOT NULL UNIQUE 
); 


CREATE TABLE GRUPO ( 
    idGrupo INT(8) PRIMARY KEY, 
    nombreGrupo VARCHAR(64) NOT NULL UNIQUE, 
    descripcion VARCHAR(256), 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE PERSONA ( 
    idPersona INT(8) PRIMARY KEY, 
    nombre VARCHAR(64) NOT NULL, 
    apellido VARCHAR(64) NOT NULL, 
    email VARCHAR(64) NOT NULL, 
    idGrupo INT(8) NOT NULL, 
    FOREIGN KEY (idGrupo) REFERENCES GRUPO(idGrupo) 
); 


CREATE TABLE PRIVILEGIOS ( 
    idPrivilegio INT(8) PRIMARY KEY, 
    nombrePrivilegio VARCHAR(64) NOT NULL, 
    descripcion VARCHAR(255), 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE ETAPA ( 
    idEtapa INT(8) PRIMARY KEY, 
    nombreEtapa VARCHAR(64) NOT NULL, 
    estado VARCHAR(32) NOT NULL, 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE SERVICIO ( 
    idServicio INT PRIMARY KEY NOT NULL, 
    tipoServicio VARCHAR(255) NOT NULL, 
    descripcion VARCHAR(255), 
    idEtapa INT NOT NULL, 
    FOREIGN KEY (idEtapa) REFERENCES ETAPA(idEtapa) 
); 


CREATE TABLE CLOUD_STORAGE ( 
    idCloudStorage INT(8) PRIMARY KEY, 
    limiteSubida INT(16) NOT NULL, 
    velocidad INT(16) NOT NULL, latencia INT(16) NOT NULL 
); 


CREATE TABLE CS_CONFIG ( 
    idCSConfig INT(8) PRIMARY KEY, 
    almacenamiento INT(16) NOT NULL, 
    idCloudStorage INT(8) NOT NULL, 
    FOREIGN KEY (idCloudStorage) REFERENCES CLOUD_STORAGE(idCloudStorage)
); 

CREATE TABLE DATA_BASE ( 
    idDataBase INT(8) PRIMARY KEY 
); 


CREATE TABLE DB_CONFIG ( 
    idDBConfig INT(8) PRIMARY KEY, 
    nombreDB VARCHAR(64) NOT NULL, 
    motor VARCHAR(64) NOT NULL, 
    usuarios INT(16) NOT NULL, 
    almacenamiento INT(16) NOT NULL, 
    cpu INT(2) NOT NULL, 
    puerto INT(8) NOT NULL, 
    direccionIP VARCHAR(16) NOT NULL, 
    idDataBase INT(8) NOT NULL, 
    FOREIGN KEY (idDataBase) REFERENCES DATA_BASE(idDataBase) 
); 


CREATE TABLE VIDEO_CONFERENCE ( 
idVideoConference INT(8) PRIMARY KEY 
); 


CREATE TABLE VC_CONFIG ( 
    idVCConfig INT(8) PRIMARY KEY, 
    calidad VARCHAR(4) NOT NULL,
    anchoBanda INT(16) NOT NULL, 
    maxParticipantes INT(8) NOT NULL, 
    idioma VARCHAR(64) NOT NULL, 
    idVideoConference INT(8) NOT NULL,
    FOREIGN KEY (idVideoConference) REFERENCES VIDEO_CONFERENCE(idVideoConference)
); 


CREATE TABLE R_PERSONA_SERVICIO ( 
    idServicio INT(8) NOT NULL, 
    idPersona INT(8) NOT NULL, 
    PRIMARY KEY (idServicio, idPersona), 
    FOREIGN KEY (idServicio) REFERENCES SERVICIO(idServicio), 
    FOREIGN KEY (idPersona) REFERENCES PERSONA(idPersona)
); 




CREATE TABLE R_GRUPO_PRIVILEGIOS ( 
    idGrupo INT(8) NOT NULL, 
    idPrivilegio INT(8) NOT NULL,
    PRIMARY KEY (idGrupo, idPrivilegio), 
    FOREIGN KEY (idGrupo) REFERENCES GRUPO(idGrupo),
    FOREIGN KEY (idPrivilegio) REFERENCES PRIVILEGIOS(idPrivilegio)
);
