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
AFTER INSERT ON usuarios
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
        (id_usuario, email_perfil, token_recuperacion)
        VALUES 
        (NEW.id_usuario, NEW.email, token_generado);    
    END IF;
END$$
DELIMITER ;





