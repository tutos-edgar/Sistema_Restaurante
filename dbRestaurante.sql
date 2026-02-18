-- Crear BD
CREATE DATABASE IF NOT EXISTS db_restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_restaurante;


-- Restaurantes (config general)
CREATE TABLE restaurante (
  id_restaurante INT AUTO_INCREMENT PRIMARY KEY,
  nombre_restaurante VARCHAR(200) NOT NULL,
  direccion_restaurante VARCHAR(255),
  telefono_restaurante VARCHAR(50),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- echo md5($user->getPassword());
-- 8f289bed9e3793429463a2986f047490 -> 896425
-- 3a824154b16ed7dab899bf000b80eeee -> 2022
-- 202cb962ac59075b964b07152d234b70 -> 123


-- Parametros Generales
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


-- Roles
CREATE TABLE  roles_usuarios (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(255) NOT NULL UNIQUE,
    es_activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   
);

INSERT INTO roles_usuarios(nombre_rol) VALUES('ADMINISTRADOR');
INSERT INTO roles_usuarios(nombre_rol) VALUES('USUARIO');

-- Usuarios (personal - mesero, cajero, admin)
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  alias VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  palabra_recuperacion VARCHAR(255),
  pregunta_seguridad VARCHAR(255),
  respuesta_seguridad VARCHAR(255),
  super_usuario BOOLEAN DEFAULT FALSE,
  id_rol INT NOT NULL,
  activo TINYINT(1) DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_rol) REFERENCES roles_usuarios(id_rol) ON DELETE CASCADE
);

ALTER TABLE usuarios ADD COLUMN email VARCHAR(200) NOT NULL UNIQUE 

INSERT INTO usuarios
(alias, password_hash, palabra_recuperacion, pregunta_seguridad, respuesta_seguridad, super_usuario, id_rol, activo)
VALUES
('edgar86', '8f289bed9e3793429463a2986f047490', 'mi esposa es buena persona', '', '', true, 1)


CREATE TABLE estados_perfil_usuario (
    id_estado_perfil INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(100) NOT NULL UNIQUE,
    descripcion_estado VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de perfiles de usuario
CREATE TABLE perfiles_usuarios (
    id_perfil_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    documento VARCHAR(100) UNIQUE,
    nombre_perfil VARCHAR(200),
    apellido_perfil VARCHAR(200),
    email_perfil VARCHAR(200),
    foto_perfil VARCHAR(255),
    nit VARCHAR(50) UNIQUE,
    bio_perfil TEXT,
    fecha_nacimiento DATE,
    happy_birthday DATE,
    id_estado_perfil INT DEFAULT 1,
    token_recuperacion VARCHAR(500) DEFAULT '',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_estado_perfil) REFERENCES estados_perfil_usuario(id_estado_perfil)
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


CREATE TABLE  sesiones_usuarios (
    id_sesion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario VARCHAR(255) NOT NULL,
    token_sesion VARCHAR(255) NOT NULL UNIQUE,
    ip_usuario VARCHAR(255) NOT NULL UNIQUE,
    estadosesion VARCHAR(255) NOT NULL ,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Categorias de platillos
CREATE TABLE categorias (
    id_categorias INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(120) NOT NULL,
    descripcion_categoria VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Platillos
CREATE TABLE platillos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT,
  nombre VARCHAR(200) NOT NULL,
  descripcion TEXT,
  activo TINYINT(1) DEFAULT 1,
  foto VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Precios por platillo (permite historial o variantes: small/med/large)
CREATE TABLE platillo_precios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  platillo_id INT NOT NULL,
  nombre_variant VARCHAR(80) DEFAULT 'Standard', -- eg. Small, Large
  precio DECIMAL(10,2) NOT NULL,
  vigente TINYINT(1) DEFAULT 1,
  desde TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (platillo_id) REFERENCES platillos(id)
);

-- Adicionales (ej: extra queso, extra salsa)
CREATE TABLE adicionales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  precio DECIMAL(10,2) DEFAULT 0.00,
  activo TINYINT(1) DEFAULT 1
);

-- Mesas
CREATE TABLE mesas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) UNIQUE, -- por ejemplo "MESA-1" o token visible
  nombre VARCHAR(100),
  estado VARCHAR(50) DEFAULT 'Libre', -- Libre, Ocupada, EnCobro, Cerrada
  token_qr VARCHAR(255) UNIQUE,
  capacidad INT DEFAULT 4
);

-- Clientes
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150),
  telefono VARCHAR(50),
  email VARCHAR(150),
  registrado TINYINT(1) DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pedidos
CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mesa_id INT NULL,
  cliente_id INT NULL,
  usuario_id INT NULL, -- quien creó/atendió (mesero/cajero)
  tipo ENUM('Estandar','Llevar','Domicilio','QR') DEFAULT 'Estandar',
  total DECIMAL(12,2) DEFAULT 0.00,
  estado ENUM('Nuevo','EnPreparacion','Listo','Servido','CuentaSolicitada','Pagado','Cancelado') DEFAULT 'Nuevo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  qr_token VARCHAR(255) NULL,
  notas TEXT,
  FOREIGN KEY (mesa_id) REFERENCES mesas(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Detalle de pedido (items)
CREATE TABLE pedido_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  platillo_id INT NOT NULL,
  platillo_precio_id INT NULL,
  nombre_snapshot VARCHAR(200) NOT NULL, -- nombre del platillo al momento
  precio_unit DECIMAL(10,2) NOT NULL,
  cantidad INT DEFAULT 1,
  subtotal DECIMAL(12,2) NOT NULL,
  estado_item ENUM('Pedido','EnCocina','Listo','Servido','Cancelado') DEFAULT 'Pedido',
  notas VARCHAR(255),
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (platillo_id) REFERENCES platillos(id),
  FOREIGN KEY (platillo_precio_id) REFERENCES platillo_precios(id)
);

-- Relación adicionales aplicados a items (adicionales por item)
CREATE TABLE pedido_item_adicionales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_item_id INT NOT NULL,
  adicional_id INT NOT NULL,
  nombre_snapshot VARCHAR(120),
  precio DECIMAL(10,2) DEFAULT 0.00,
  FOREIGN KEY (pedido_item_id) REFERENCES pedido_items(id),
  FOREIGN KEY (adicional_id) REFERENCES adicionales(id)
);

-- Tipos de pago
CREATE TABLE tipo_pago (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  descripcion VARCHAR(255)
);

-- Pagos (un pedido puede tener N pagos)
CREATE TABLE pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  tipo_pago_id INT NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  referencia VARCHAR(255) NULL, -- e.g. autorizacion tarjeta
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (tipo_pago_id) REFERENCES tipo_pago(id)
);

-- Auditoria simple
CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  accion VARCHAR(200),
  detalles TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Índices y datos iniciales
INSERT INTO roles (nombre, descripcion) VALUES ('Admin','Administrador total'), ('Cajero','Usuario caja'), ('Mesero','Atiende mesas'), ('Cocina','Personal cocina');
INSERT INTO tipo_pago (nombre, descripcion) VALUES ('Efectivo','Pago en efectivo'), ('Tarjeta','Pago con tarjeta'), ('Transferencia','Transferencia bancaria');

-- Ejemplos datos demo
INSERT INTO categorias (nombre, descripcion, orden) VALUES ('Entradas','Entradas y tapas',1), ('Principales','Platos fuertes',2), ('Bebidas','Bebidas frías y calientes',3);
INSERT INTO platillos (categoria_id,nombre,descripcion,activo) VALUES (1,'Nachos','Nachos con queso',1),(2,'Pollo a la brasa','Con papas',1),(3,'Coca Cola','Lata 330ml',1);
INSERT INTO platillo_precios (platillo_id, nombre_variant, precio, vigente) VALUES (1,'Standard',6.50,1),(2,'Standard',8.90,1),(3,'Lata',1.50,1);
INSERT INTO adicionales (nombre,precio) VALUES ('Extra Queso',0.80),('Salsa Picante',0.30);

-- Crear una mesa y token
INSERT INTO mesas (codigo,nombre,token_qr,estado,capacidad) VALUES ('MESA-1','Mesa 1','token-mesa-1','Libre',4);



-----------------




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





-- CREATE INDEX idx_usuario_id ON videos(usuario_id);
-- CREATE INDEX idx_video_id ON vistas(video_id);  

-- -- Índices para mejorar el rendimiento
-- CREATE INDEX idx_videos_channel_id ON videos(channel_id);
-- CREATE INDEX idx_videos_created_at ON videos(created_at);
-- CREATE INDEX idx_video_views_video_id ON video_views(video_id);
-- CREATE INDEX idx_video_views_user_id ON video_views(user_id);
-- CREATE INDEX idx_video_views_viewed_at ON video_views(viewed_at);
-- CREATE INDEX idx_user_recommendations_user_id ON user_recommendations(user_id);






    



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



