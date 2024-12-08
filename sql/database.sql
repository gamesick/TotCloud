CREATE DATABASE totcloud; 


CREATE TABLE ORGANIZACION ( 
    idOrganizacion INT(8) PRIMARY KEY, 
    nombreOrganizacion VARCHAR(64) NOT NULL UNIQUE, 
    direccion VARCHAR(128) NOT NULL, 
    telefono VARCHAR(9) NOT NULL, 
    email VARCHAR(64) NOT NULL 
); 


CREATE TABLE PERSONA ( 
    idPersona INT(8) AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(64) NOT NULL, 
    apellido VARCHAR(64) NOT NULL, 
    email VARCHAR(64) NOT NULL
); 


CREATE TABLE PERSONAL ( 
    idPersonal INT(8) PRIMARY KEY, 
    nombrePersonal VARCHAR(64) NOT NULL UNIQUE,
    contrasenya VARCHAR(256) NOT NULL,
    FOREIGN KEY (idPersonal) REFERENCES PERSONA(idPersona) 
); 


CREATE TABLE GRUPO ( 
    idGrupo INT(8) AUTO_INCREMENT PRIMARY KEY, 
    nombreGrupo VARCHAR(64) NOT NULL UNIQUE, 
    descripcion VARCHAR(256), 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE USUARIO (
 	idUsuario INT(8) PRIMARY KEY, 
    nombreUsuario VARCHAR(64) NOT NULL UNIQUE, 
    contrasenya VARCHAR(256) NOT NULL,
    idOrganizacion INT(8) NOT NULL, 
    idGrupo INT(8), 
    FOREIGN KEY (idGrupo) REFERENCES GRUPO(idGrupo), 
    FOREIGN KEY (idOrganizacion) REFERENCES ORGANIZACION (idOrganizacion),
    FOREIGN KEY (idUsuario) REFERENCES PERSONA(idPersona) 
); 


CREATE TABLE PRIVILEGIOS ( 
    idPrivilegio INT(8) PRIMARY KEY, 
    nombrePrivilegio VARCHAR(64) NOT NULL, 
    descripcion VARCHAR(256), 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE ETAPA ( 
    idEtapa INT(8) PRIMARY KEY, 
    nombreEtapa VARCHAR(64) NOT NULL, 
    descripcion VARCHAR(256) NOT NULL, 
    idPersonal INT(8) NOT NULL, 
    FOREIGN KEY (idPersonal) REFERENCES PERSONAL(idPersonal) 
); 


CREATE TABLE SERVICIO ( 
    idServicio INT(8) PRIMARY KEY, 
    tipoServicio VARCHAR(256) NOT NULL, 
    descripcion VARCHAR(256), 
    idEtapa INT(8) NOT NULL,
    idPrivilegio INT(8) NOT NULL,
    FOREIGN KEY (idPrivilegio) REFERENCES PRIVILEGIOS(idPrivilegio),
    FOREIGN KEY (idEtapa) REFERENCES ETAPA(idEtapa) 
); 


CREATE TABLE CLOUD_STORAGE ( 
    idCloudStorage INT(8) AUTO_INCREMENT PRIMARY KEY, 
    limiteSubida INT(16) NOT NULL, 
    velocidad INT(16) NOT NULL, latencia INT(16) NOT NULL,
    idServicio INT(8) NOT NULL,
    FOREIGN KEY (idServicio) REFERENCES SERVICIO(idServicio)
); 


CREATE TABLE CS_CONFIG ( 
    idCSConfig INT(8) AUTO_INCREMENT PRIMARY KEY,
    nombreCS VARCHAR(64) NOT NULL,
    almacenamiento INT(16) NOT NULL, 
    idCloudStorage INT(8) NOT NULL,
    idPersona INT(8) NOT NULL,
    FOREIGN KEY (idCloudStorage) REFERENCES CLOUD_STORAGE(idCloudStorage),
    FOREIGN KEY (idPersona) REFERENCES PERSONA(idPersona) 
); 

CREATE TABLE DATA_BASE ( 
    idDataBase INT(8) AUTO_INCREMENT PRIMARY KEY,
    idServicio INT(8) NOT NULL,
    FOREIGN KEY (idServicio) REFERENCES SERVICIO(idServicio)
); 


CREATE TABLE DB_CONFIG ( 
    idDBConfig INT(8) AUTO_INCREMENT PRIMARY KEY, 
    nombreDB VARCHAR(64) NOT NULL, 
    motor VARCHAR(64) NOT NULL, 
    usuarios INT(16) NOT NULL, 
    almacenamiento INT(16) NOT NULL, 
    cpu INT(2) NOT NULL, 
    puerto INT(8) NOT NULL, 
    direccionIP VARCHAR(16) NOT NULL, 
    idDataBase INT(8) NOT NULL, 
    idPersona INT(8) NOT NULL,
    FOREIGN KEY (idDataBase) REFERENCES DATA_BASE(idDataBase),
    FOREIGN KEY (idPersona) REFERENCES PERSONA(idPersona) 
); 


CREATE TABLE VIDEO_CONFERENCE ( 
    idVideoConference INT(8) AUTO_INCREMENT PRIMARY KEY,
    idServicio INT(8) NOT NULL,
    FOREIGN KEY (idServicio) REFERENCES SERVICIO(idServicio)
); 


CREATE TABLE VC_CONFIG ( 
    idVCConfig INT(8) AUTO_INCREMENT PRIMARY KEY,
    nombreVC VARCHAR(64) NOT NULL,
    calidad VARCHAR(8) NOT NULL,
    anchoBanda INT(16) NOT NULL, 
    maxParticipantes INT(8) NOT NULL, 
    idioma VARCHAR(64) NOT NULL, 
    idVideoConference INT(8) NOT NULL,
    idPersona INT(8) NOT NULL,
    FOREIGN KEY (idVideoConference) REFERENCES VIDEO_CONFERENCE(idVideoConference),
    FOREIGN KEY (idPersona) REFERENCES PERSONA(idPersona) 
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
