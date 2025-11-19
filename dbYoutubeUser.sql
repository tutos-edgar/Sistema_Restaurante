CREATE DATABASE dbYoutubeUser;
CREATE DATABASE db_YoutubeUser;
USE dbYoutubeUser;


CREATE TABLE  roles_usuarios (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(255) NOT NULL UNIQUE,
    es_activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   
);

INSERT INTO roles_usuarios(nombre_rol) VALUES('USUARIO');
INSERT INTO roles_usuarios(nombre_rol) VALUES('ADMINISTRADOR');

CREATE TABLE parametros(
    id_parametro INT AUTO_INCREMENT PRIMARY KEY,
    nombre_parametro VARCHAR(200),
    valor_parametro VARCHAR(500),
    es_activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ALTER TABLE parametros ADD COLUMN es_activo BOOLEAN DEFAULT TRUE;

INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Limites de Intentos para iniciar sesion', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Cantidad Limite de caracteres para el password', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Tiempo de espera cuando la sesion se bloquea por intentos de acceso al login', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Limite de Tiempo de Bloqueo Temporalmente', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Limite de Tiempo Sesion Usuario', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Limites de Registro Video', '5');
INSERT INTO parametros (nombre_parametro, valor_parametro) VALUES('Limites de Registro Canales', '1');


-- DROP TABLE usuarios_youtube;
-- ALTER TABLE usuarios_youtube  COLUMN ADD   token_acceso VARCHAR(500) DEFAULT '';
CREATE TABLE  usuarios_youtube (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(200) NOT NULL,
    apellido_usuario VARCHAR(200) NOT NULL,
    email_usuario VARCHAR(200) NOT NULL UNIQUE,
    alias_usuario VARCHAR(200) NOT NULL UNIQUE,
    pass_usuario VARCHAR(200) NOT NULL,
    token_acceso VARCHAR(500) DEFAULT '',
    es_activo BOOLEAN DEFAULT TRUE,
    id_rol INT DEFAULT 1,
    terminos BOOLEAN DEFAULT TRUE,
    intento_login INT DEFAULT 0,
    estado_usuario INT DEFAULT 1,
    fecha_cambio_estado DATETIME,
    tipo_usuario VARCHAR(50) DEFAULT 'NORMAL',
    ultimo_acceso DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles_usuarios(id_rol) ON DELETE CASCADE
);

-- ALTER TABLE usuarios_youtube ADD COLUMN  tipo_usuario VARCHAR(50) DEFAULT 'NORMAL';

-- Tabla de perfiles de usuario
CREATE TABLE perfiles_usuarios (
    id_perfil_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_perfil VARCHAR(200),
    apellido_perfil VARCHAR(200),
    email_perfil VARCHAR(200),
    foto_perfil VARCHAR(255),
    bio_perfil TEXT,
    fecha_nacimiento DATE,
    happy_birthday DATE,
    token_recuperacion VARCHAR(500) DEFAULT '',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios_youtube(id_usuario) ON DELETE CASCADE
);

CREATE TABLE  token_recuperacion (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario VARCHAR(255) NOT NULL,
    token_recuperacion VARCHAR(255) NOT NULL,
    id_perfil INT NOT NULL UNIQUE,
    estado_token VARCHAR(2) DEFAULT 'A',
    fecha_vence DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE  canales_youtube (
    id_canal_youtube INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    nombre_canal VARCHAR(300) NOT NULL,
    url_canal VARCHAR(500) NOT NULL UNIQUE,
    idcanal VARCHAR(255) NOT NULL UNIQUE,
    descripcion_canal TEXT,
    suscriptores_count INT DEFAULT 0,
    vistas INT DEFAULT 0,
    es_activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,    
    FOREIGN KEY (id_usuario) REFERENCES usuarios_youtube(id_usuario) ON DELETE CASCADE
);

-- ALTER TABLE canales_youtube 
-- CHANGE suscritores_count suscriptores_count INT DEFAULT 0;
CREATE TABLE  videos_youtube (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_canal_youtube INT NOT NULL,
    titulo_video VARCHAR(300) NOT NULL,
    descripcion_video VARCHAR(500) ,
    url_video VARCHAR(500) NOT NULL,
    tiempo_duracion VARCHAR(100) NOT NULL,
    idvideo VARCHAR(255) NOT NULL UNIQUE,
    vistas INT DEFAULT 0,
    comentarios INT DEFAULT 0,
    likes INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_canal_youtube) REFERENCES canales_youtube(id_canal_youtube) ON DELETE CASCADE
); 

-- RENAME TABLE videos_yotube TO videos_youtube;
CREATE TABLE short_youtube (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_canal_youtube INT,
    titulo_video VARCHAR(300) NOT NULL,
    descripcion_video VARCHAR(500) ,
    url_video VARCHAR(500) NOT NULL,
    tiempo_duracion VARCHAR(100) NOT NULL,
    idvideo VARCHAR(255) NOT NULL UNIQUE,
    vistas INT DEFAULT 0,
    comentarios INT DEFAULT 0,
    likes INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_canal_youtube) REFERENCES canales_youtube(id_canal_youtube) ON DELETE CASCADE
); 

CREATE TABLE IF NOT EXISTS deudas_vistas_usuario (
    id_deuda_vista_usuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario_deudor INT UNIQUE,
    usuario_acreedor INT,
    cantidad_deuda INT,
    tipo_video ENUM('video', 'short') NOT NULL,   
    estado_deuda VARCHAR(50) NOT NULL, 
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_deudor) REFERENCES usuarios_youtube(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (usuario_acreedor) REFERENCES usuarios_youtube(id_usuario) ON DELETE CASCADE
);




CREATE TABLE IF NOT EXISTS tokens_acceso (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    token_generado VARCHAR(500),
    estado_token VARCHAR(2) DEFAULT 'A',
    tiempo_duracion INT,
    fecha_vence DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tokens_acceso (id_usuario, token_generado, tiempo_duracion, fecha_vence) VALUES('2','lsiekKjdy','900','2025-08-31')

CREATE TABLE IF NOT EXISTS tokens_acceso_sesion (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    token_generado VARCHAR(500),
    estado_token VARCHAR(2) DEFAULT 'A',
    tiempo_duracion INT,
    fecha_vence DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE  sesiones_usuarios (
    id_sesion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario VARCHAR(255) NOT NULL,
    token_sesion VARCHAR(255) NOT NULL UNIQUE,
    ip_usuario VARCHAR(255) NOT NULL UNIQUE,
    estadosesion VARCHAR(255) NOT NULL ,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS vistas (
    id_vista INT AUTO_INCREMENT PRIMARY KEY,
    id_video INT,
    tipo_video ENUM('video', 'short') NOT NULL,
    id_usuario_view INT,
    id_usuario_video INT,
    fecha_vista TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_video) REFERENCES videos_yotube(id_video) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS visualizaciones_usuarios (
    id_visualizacion_usuario INT AUTO_INCREMENT PRIMARY KEY,
    cantidad_visualizacion INT,
    typo_video ENUM('video', 'short') NOT NULL,
    id_usuario INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios_youtube(id_usuario) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS historial_vistas (
    id_historial_vista INT AUTO_INCREMENT PRIMARY KEY,
    id_video INT,
    tipo_video ENUM('video', 'short') NOT NULL,
    id_usuariov_view INT,
    id_usuario_video INT,
    fecha_vista TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    
    FOREIGN KEY (id_video) REFERENCES videos_yotube(id_video) ON DELETE CASCADE 
);


-- CREATE INDEX idx_usuario_id ON videos(usuario_id);
-- CREATE INDEX idx_video_id ON vistas(video_id);  

-- -- Índices para mejorar el rendimiento
-- CREATE INDEX idx_videos_channel_id ON videos(channel_id);
-- CREATE INDEX idx_videos_created_at ON videos(created_at);
-- CREATE INDEX idx_video_views_video_id ON video_views(video_id);
-- CREATE INDEX idx_video_views_user_id ON video_views(user_id);
-- CREATE INDEX idx_video_views_viewed_at ON video_views(viewed_at);
-- CREATE INDEX idx_user_recommendations_user_id ON user_recommendations(user_id);



-- GENERAR TOKEN
DROP FUNCTION IF EXISTS generar_token;
DELIMITER $$
CREATE FUNCTION generar_token(longitud INT) 
RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
    DECLARE chars VARCHAR(62) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    DECLARE token VARCHAR(255) DEFAULT '';
    DECLARE i INT DEFAULT 0;

    WHILE i < longitud DO
        SET token = CONCAT(token, SUBSTRING(chars, FLOOR(1 + RAND() * 62), 1));
        SET i = i + 1;
    END WHILE;

    RETURN token;
END$$
DELIMITER ;

SELECT generar_token(60) AS mi_token;


    

-- INSERTAR PERFIL USUARIO
DROP TRIGGER IF EXISTS insertar_perfil_usuario;
DELIMITER $$
CREATE TRIGGER insertar_perfil_usuario
AFTER INSERT ON usuarios_youtube
FOR EACH ROW
BEGIN
    DECLARE token_generado VARCHAR(255);
    DECLARE token_existe INT;
    -- Verificar si ya existe un registro para el usuario
    IF NOT EXISTS (
        SELECT 1 
        FROM perfiles_usuarios 
        WHERE id_usuario = NEW.id_usuario
    ) THEN
        -- SET token_generado = generar_token(32);
        REPEAT
            SET token_generado = generar_token(32);
            SELECT COUNT(*) INTO token_existe FROM perfiles_usuarios WHERE token_recuperacion = token_generado;
            UNTIL token_existe = 0
        END REPEAT;
        INSERT INTO perfiles_usuarios 
        (id_usuario, nombre_perfil, apellido_perfil, email_perfil, token_recuperacion)
        VALUES 
        (NEW.id_usuario, NEW.nombre_usuario, NEW.apellido_usuario, NEW.email_usuario, token_generado);    
    END IF;
END$$
DELIMITER ;

-- ACTUALIZAR EL PERFIL DE USUARIOO
DROP TRIGGER IF EXISTS actualizar_perfil_usuario;
DELIMITER $$
CREATE TRIGGER actualizar_perfil_usuario
AFTER UPDATE ON usuarios_youtube
FOR EACH ROW
BEGIN
    -- Verificar si ya existe un registro para el usuario
    IF EXISTS (
        SELECT 1 
        FROM perfiles_usuarios 
        WHERE id_usuario = NEW.id_usuario
    ) THEN
        UPDATE perfiles_usuarios 
        SET nombre_perfil = NEW.nombre_usuario, apellido_perfil = NEW.apellido_usuario, email_perfil = NEW.email_usuario WHERE id_usuario = NEW.id_usuario;  
    END IF;
END$$
DELIMITER ;













-- ACTUALIZAR TOKEN DE USUARIOS
DROP TRIGGER IF EXISTS actualizar_token_usuario;
DELIMITER $$
CREATE TRIGGER actualizar_token_usuario 
AFTER INSERT ON tokens_acceso
FOR EACH ROW
BEGIN
    -- Verificar si ya existe un registro para el usuario
    IF EXISTS (
        SELECT 1 
        FROM usuarios_youtube 
        WHERE id_usuario = NEW.id_usuario
    ) THEN
        UPDATE usuarios_youtube 
        SET token_acceso = NEW.token_generado WHERE id_usuario = NEW.id_usuario;  
    END IF;
END$$
DELIMITER ;


-- INSERTAR VISUALIZACIONES
DELIMITER $$

CREATE TRIGGER insertar_visualizacion_usuario
AFTER INSERT ON vistas
FOR EACH ROW
BEGIN
    -- Verificar si ya existe un registro para el usuario y tipo de video
    IF EXISTS (
        SELECT 1 
        FROM visualizaciones_usuarios 
        WHERE id_usuario = NEW.id_usuariov_view 
          AND typo_video = NEW.tipo_video
    ) THEN
        -- Si existe, actualizar cantidad_visualizacion
        UPDATE visualizaciones_usuarios
        SET cantidad_visualizacion = cantidad_visualizacion + 1,
            fecha_actualizacion = NOW()
        WHERE id_usuario = NEW.id_usuariov_view 
          AND typo_video = NEW.tipo_video;
    ELSE
        -- Si no existe, insertar un nuevo registro
        INSERT INTO visualizaciones_usuarios (cantidad_visualizacion, typo_video, id_usuario)
        VALUES (1, NEW.tipo_video, NEW.id_usuariov_view);
    END IF;
END$$

DELIMITER ;


CREATE TRIGGER insertar_historial_vista
AFTER INSERT ON vistas
FOR EACH ROW
BEGIN
    -- Verificar si ya existe un registro para el usuario y tipo de video
    IF EXISTS (
        SELECT 1 
        FROM visualizaciones_usuarios 
        WHERE id_usuario = NEW.id_usuariov_view 
          AND typo_video = NEW.tipo_video
    ) THEN
        -- Si existe, actualizar cantidad_visualizacion
        UPDATE visualizaciones_usuarios
        SET cantidad_visualizacion = cantidad_visualizacion + 1,
            fecha_actualizacion = NOW()
        WHERE id_usuario = NEW.id_usuariov_view 
          AND typo_video = NEW.tipo_video;
    ELSE
        -- Si no existe, insertar un nuevo registro
        INSERT INTO visualizaciones_usuarios (cantidad_visualizacion, typo_video, id_usuario)
        VALUES (1, NEW.tipo_video, NEW.id_usuariov_view);
    END IF;
END$$

DELIMITER ;



DELIMITER $$

CREATE TRIGGER tr_actualizar_estado_deuda
BEFORE UPDATE ON Deudas
FOR EACH ROW
BEGIN
  -- Si la cantidad llega a 0, marcar como pagado
  IF NEW.cantidad = 0 THEN
    SET NEW.estado = 'pagado';
  ELSE
    SET NEW.estado = 'pendiente';
  END IF;
END$$

DELIMITER ;



