CREATE DATABASE videojuegos;
USE videojuegos;
-- Tabla de Clientes
CREATE TABLE clientes (
    cliente_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100) UNIQUE
);

-- Tabla de Géneros
CREATE TABLE genero (
    genero_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de Plataformas
CREATE TABLE plataforma (
    plataforma_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de Productos
CREATE TABLE producto (
    producto_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
Foto text
);

-- Tabla de Pedidos
CREATE TABLE pedido (
    pedido_id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    fecha_pedido DATE NOT NULL,
    total DECIMAL(10, 2),
    estado VARCHAR(50),
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);



-- Tabla de Detalles de Pedido
CREATE TABLE detalles_pedido (
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) AS (cantidad * precio_unitario),
    PRIMARY KEY (pedido_id, producto_id),
    FOREIGN KEY (pedido_id) REFERENCES pedido(pedido_id),
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id)
);

Create table genero_juegos(
producto_id int,
Genero_id int,
 PRIMARY KEY (genero_id, producto_id),
 FOREIGN KEY (genero_id) REFERENCES genero(genero_id),
 FOREIGN KEY (producto_id) REFERENCES producto(producto_id)
);

Create table plataforma_juegos(
producto_id int,
plataforma_id int,
 PRIMARY KEY (plataforma_id , producto_id),
 FOREIGN KEY (plataforma_id ) REFERENCES plataforma(plataforma_id ),
 FOREIGN KEY (producto_id) REFERENCES producto(producto_id)
);


INSERT INTO plataforma (nombre) VALUES 
('PC'), 
('PlayStation 5'), 
('Xbox Series X'),
('Nintendo Switch'),
('PC VR'), 
('PlayStation 4'), 
('Xbox One'), 
('Mobile'), 
('Nintendo 3DS'); 



INSERT INTO genero (nombre) VALUES 
('Aventura'), 
('Acción'), 
('RPG'), 
('Deportes'), 
('Simulación'), 
('Terror'), 
('Estrategia'), 
('Lucha'), 
('Multijugador'); 

INSERT INTO clientes (nombre, contrasena, telefono, email) 
VALUES ('root', '$2y$10$IbXtFZ67Lkw6zJb/7dsTp.w7H.QLWYygI8U56XYrRb6YlQ.gQHzka', '928164348', 'root@email.com');


INSERT INTO
 producto (nombre, descripcion, precio, Foto) 
VALUES
('Elden Ring', 'RPG de acción en un mundo abierto creado por From Software', 69.99, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRHDFvncjXj70aYo2-wpsT3skUHjPDIw8dzxg&s'),

('Mario Kart 8 Deluxe', 'Juego de carreras con personajes icónicos de Nintendo', 59.99,'https://www.nintendo.com/eu/media/images/10_share_images/games_15/nintendo_switch_4/H2x1_NSwitch_MarioKart8Deluxe_image1600w.jpg'),

('hollow knight', 'Juego de deducción social y multijugador', 14.99,'https://www.nintendo.com/eu/media/images/10_share_images/games_15/wiiu_download_software_5/H2x1_WiiUDS_HollowKnight_image1600w.jpg'),

('Cyberpunk 2077', 'RPG futurista en un mundo distópico', 59.99,'https://cdn2.unrealengine.com/egs-cyberpunk2077-cdprojektred-s1-03-22-22-2560x1440-8b29e725f1ef.jpg?resize=1&w=480&h=270&quality=medium'),

('Hades', 'Roguelike de acción en el inframundo griego', 24.99,'https://www.nintendo.com/eu/media/images/10_share_images/games_15/nintendo_switch_download_software_1/H2x1_NSwitchDS_Hades_image1600w.png'),

('Mortal Kombat 11', 'Juego de lucha con personajes icónicos', 49.99,'https://store-images.s-microsoft.com/image/apps.31077.70804610839547354.8da93c46-fd13-4b16-8ebe-e8e02c53d93e.032a1c73-7961-4acf-a82a-89d2f3ccdd1f?q=90&w=480&h=270'),

('Animal Crossing: New Horizons', 'Simulación de vida en una isla paradisíaca', 59.99, 'https://www.nintendo.com/eu/media/images/10_share_images/games_15/nintendo_switch_4/H2x1_NSwitch_AnimalCrossingNewHorizons_image1600w.jpg'),

('Zelda: Breath of the Wild', 'Aventura épica en el mundo de Hyrule', 59.99, 'https://static.posters.cz/image/1300/pinturas-sobre-lienzo-the-legend-of-zelda-breath-of-the-wild-view-i111060.jpg');


INSERT INTO genero_juegos (producto_id, genero_id) VALUES
(1, 3), -- Elden Ring -> RPG
(2, 4), -- Mario Kart 8 Deluxe -> Deportes
(3, 8), -- Among Us -> Multijugador
(4, 3), -- Cyberpunk 2077 -> RPG
(5, 3), -- Hades -> RPG
(6, 7), -- Mortal Kombat 11 -> Lucha
(7, 5), -- Animal Crossing: New Horizons -> Simulación
(8, 1); -- Zelda: Breath of the Wild -> Aventura

INSERT INTO plataforma_juegos (producto_id, plataforma_id) VALUES
(1, 1), -- Elden Ring -> PC
(2, 4), -- Mario Kart 8 Deluxe -> Nintendo Switch
(3, 9), -- Among Us -> Mobile
(4, 1), -- Cyberpunk 2077 -> PC
(5, 4), -- Hades -> Nintendo Switch
(6, 3), -- Mortal Kombat 11 -> Xbox Series X
(7, 4), -- Animal Crossing: New Horizons -> Nintendo Switch
(8, 4); -- Zelda: Breath of the Wild -> Nintendo Switch