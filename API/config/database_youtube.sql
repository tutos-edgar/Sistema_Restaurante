CREATE DATABASE db_youtube;

USE db_youtube;



DROP TABLE canales;
CREATE TABLE canales(
    id_canal INT AUTO_INCREMENT PRIMARY KEY,
    nombre_canal VARCHAR(100),
    descripcion_canal TEXT,
    link_canal VARCHAR(500),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO canales (nombre_canal, descripcion_canal, link_canal) VALUES('CODE SOFTWARE', 'FULL PROGRAMACION','https://www.youtube.com/@tutos-edgar');
INSERT INTO canales (nombre_canal, descripcion_canal, link_canal) VALUES('TODO ENTRETENIMIENTO', 'FULL ENTRETENIMIENTO','https://www.youtube.com/@todo-entretenimiento');

DROP TABLE videos;
CREATE TABLE videos(
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_canal VARCHAR(100),
    nombre_video TEXT,
    link_video TEXT,
    tiempo_video TEXT,
    consulta INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE videos_short;
CREATE TABLE videos_short(
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_canal VARCHAR(100),
    nombre_video TEXT,
    link_video TEXT,
    tiempo_video TEXT,
    consulta INT,
    consulta INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ALTER TABLE videos ADD COLUMN consulta INT DEFAULT 0;
-- ALTER TABLE videos_short ADD COLUMN consulta INT DEFAULT 0;

--http://code-software.mypressonline.com/youtube_video_api/view/datosCanales.php


-- VIDEOS CODE SOFTWARE

INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/Rxunt5-v0eE', '5:19');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/TMT8nFpTkSQ', '2:30');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/lU5tPgkFeZE', '2:38');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/zZlMn8_qdH0', '0:42');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/SysTzKLeo1I', '3:57');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/rYLt7NAdAwA', '5:32');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/ZL5omWKWkUY', '5:40');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/o1VyU6NpYqQ', '5:39');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/c59dw0_KtBw', '6:51');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/A5x2lxQqOpk', '2:54');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/LHijU1eEarw', '4:05');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/mIOaZaN9AT8', '4:11');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/3h1TbpsKQc0', '5:10');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/a0KhlDExWNQ', '4:21');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/Yunj3axWRfE', '7:12');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/3LA9P-Nh68o', '10:05');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/s_xpd_87kEA', '3:08');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/2pIhOiUqWh0', '1:22');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/i0txDGRlEpM', '3:23');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/NnrmZZJPpOk', '6:31');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/9tKopZUTq5c', '5:59');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/Tic2ZyOa84s', '10:11');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/lXwIE7tblpE', '2:58');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/p9CjhKNySj0', '6:06');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/RgJhd0UpTFY', '4:24');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/t8HD83XfpE8', '13:29');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/3EQVUhzM4pU', '7:49');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/9oLSDWSRJZM', '5:51');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/U9lU9l8SpvA', '3:39');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/JdhyRph_g1s', '4:50');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/-5moh3JdDpA', '4:14');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/FabVaxPkMJs', '5:55');
INSERT INTO videos(id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '', 'https://youtu.be/r-AqPDkcUEA', '4:07');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/bHBrIqh8Hp0', '6:32');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/KTU4tWqWBAA', '23:43');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/TTb3NUwTyoY', '4:12');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/sfD3NtmSmcQ', '6:15');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/pwQc_JR_yPc', '1:06');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/jhJ3PMV_jKw', '5:36');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/03Uoa09QjBU', '6:48');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/kMe75ggeh8w', '5:20');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/HuK5T2FjUsk', '7:42');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/Hta8oXHLksk', '5:25');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/x8-aPO2zMIc', '5:05');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/5CqKB1NRv8M', '5:46');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/8NM3nHi5cso', '9:29');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtu.be/7pnavjsixoc', '6:04');
INSERT INTO videos (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://www.youtube.com/watch?v=H5pmQ-ly2xs', '5:20');

INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','e0_bflezFe0', '');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/cFrd-Ln87lo', '0:44');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/g42cEzc7_TI', '0:57');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/9KLG2bjzK8E', '1:00');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/IucNE5Z8NMQ', '0:43');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/R9Z7DYj93qY', '0:58');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/FE_oS71AZJM', '0:51');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/KB09w67Sqtk', '0:49');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('1', '','https://youtube.com/shorts/zSNYjXYWiHs', '1:17');

-- VIDEOS TODO ENTRETENIMIENTO

INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/N3uQb9RJdqA', '0:37');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/CkbOveIRxks', '0:38');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/1iDZ3vwMbL0', '0:30');

INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/9KLG2bjzK8E', '1:00');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/IucNE5Z8NMQ', '0:43');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/R9Z7DYj93qY', '0:58');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/FE_oS71AZJM', '0:51');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/KB09w67Sqtk', '0:49');
INSERT INTO videos_short (id_canal, nombre_video, link_video, tiempo_video) VALUES('2', '','https://youtube.com/shorts/zSNYjXYWiHs', '1:17');



