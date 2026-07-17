-- Base de datos para el Sistema de Gestión de Biblioteca

SET NAMES 'utf8mb4';

CREATE DATABASE IF NOT EXISTS biblioteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca;

-- Eliminar tablas existentes para garantizar el reinicio
DROP TABLE IF EXISTS prestamos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS libros;

-- Tabla de Libros
CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    cantidad INT DEFAULT 1,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Préstamos
CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE,
    estado ENUM('activo', 'devuelto') DEFAULT 'activo',
    FOREIGN KEY (libro_id) REFERENCES libros(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10 Registros de Libros
INSERT INTO libros (titulo, autor, isbn, cantidad, estado) VALUES
('El Quijote de la Mancha', 'Miguel de Cervantes', '9788420668611', 5, 'activo'),
('Cien años de soledad', 'Gabriel García Márquez', '9780307474728', 3, 'activo'),
('1984', 'George Orwell', '9780451524935', 4, 'activo'),
('El principito', 'Antoine de Saint-Exupéry', '9780156012195', 8, 'activo'),
('El señor de los anillos', 'J.R.R. Tolkien', '9780544003415', 2, 'activo'),
('Don Juan Tenorio', 'José Zorrilla', '9788420658766', 3, 'activo'),
('Crónica de una muerte anunciada', 'Gabriel García Márquez', '9781400034956', 1, 'activo'),
('Fahrenheit 451', 'Ray Bradbury', '9781451673319', 5, 'activo'),
('Orgullo y prejuicio', 'Jane Austen', '9780141439517', 3, 'inactivo'),
('El Hobbit', 'J.R.R. Tolkien', '9780345339683', 6, 'activo');

-- 10 Registros de Usuarios
INSERT INTO usuarios (nombre, email, telefono) VALUES
('Carlos Mendoza', 'carlos.mendoza@email.com', '555-0192'),
('Ana Rodríguez', 'ana.rodriguez@email.com', '555-0183'),
('Juan Pérez', 'juan.perez@email.com', '555-0174'),
('Luisa Martínez', 'luisa.martinez@email.com', '555-0165'),
('Pedro Gómez', 'pedro.gomez@email.com', '555-0156'),
('Sofía Castro', 'sofia.castro@email.com', '555-0147'),
('Miguel Hernández', 'miguel.hernandez@email.com', '555-0138'),
('María López', 'maria.lopez@email.com', '555-0129'),
('Diego Torres', 'diego.torres@email.com', '555-0110'),
('Laura Vargas', 'laura.vargas@email.com', '555-0101');

-- 10 Registros de Préstamos Activos (1 prestamo activo por usuario a libros diferentes)
-- Nota: Para mantener la coherencia del stock, se resta 1 a los libros prestados en el stock inicial en la BD
INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado) VALUES
(1, 1, CURDATE(), 'activo'),
(2, 2, CURDATE(), 'activo'),
(3, 3, CURDATE(), 'activo'),
(4, 4, CURDATE(), 'activo'),
(5, 5, CURDATE(), 'activo'),
(6, 6, CURDATE(), 'activo'),
(7, 7, CURDATE(), 'activo'),
(8, 8, CURDATE(), 'activo'),
(10, 9, CURDATE(), 'activo'),
(1, 10, CURDATE(), 'activo');

-- Actualizar stock para reflejar los préstamos realizados en la semilla
UPDATE libros SET cantidad = cantidad - 2 WHERE id = 1;
UPDATE libros SET cantidad = cantidad - 1 WHERE id IN (2, 3, 4, 5, 6, 7, 8, 10);
