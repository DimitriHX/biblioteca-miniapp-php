<?php
require_once 'Database.php';
require_once 'Libro.php';
require_once 'Usuario.php';
require_once 'Prestamo.php';

class Biblioteca {
    private $db;
    private $conn;

    public function __construct() {
        // TODO: Inicializar conexión a base de datos
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Gestión de Libros
    public function agregarLibro(Libro $libro) {
        // TODO: Insertar libro en base de datos
        $query = "INSERT INTO libros(titulo,autor,isbn,cantidad) VALUES (:titulo,:autor,:isbn,:cantidad)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':titulo'   => $libro->getTitulo(),
            ':autor'    => $libro->getAutor(),
            ':isbn'     => $libro->getIsbn(),
            ':cantidad' => $libro->getCantidad()
        ]);
    }

    public function editarLibro($id, $nuevosDatos) {
        // TODO: Actualizar libro en base de datos
        $query = "UPDATE libros SET titulo = :titulo, autor = :autor, isbn = :isbn, cantidad = :cantidad WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id'       => $id, 
            ':titulo'   => $nuevosDatos['titulo'],
            ':autor'    => $nuevosDatos['autor'],
            ':isbn'     => $nuevosDatos['isbn'],
            ':cantidad' => $nuevosDatos['cantidad']
        ]);
    }

    public function eliminarLibro($id) {
        try {
            $this->conn->beginTransaction();

            // 1. Eliminar préstamos asociados
            $queryPrestamos = "DELETE FROM prestamos WHERE libro_id = :id";
            $stmtP = $this->conn->prepare($queryPrestamos);
            $stmtP->execute([':id' => $id]);

            // 2. Eliminar el libro
            $query = "DELETE FROM libros WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $res = $stmt->execute([':id' => $id]);

            $this->conn->commit();
            return $res;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function obtenerLibros() {
        $query = "SELECT * FROM libros";
        $stmt = $this->conn->query($query);
        $libros = [];

        while($row = $stmt->fetch() )
        {
            $libro = new Libro($row['titulo'],$row['autor'],$row['isbn'],$row['cantidad']);
            $libro->setId($row['id']);
            $libro->setEstado($row['estado']);
            $libros[] = $libro;
        }
        return $libros;
    }

    public function buscarLibro($id) {
        $query = "SELECT * FROM libros WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        
        if ($row) {            
            $libro = new Libro($row['titulo'], $row['autor'], $row['isbn'], (int)$row['cantidad']);
            $libro->setId($row['id']);
            $libro->setEstado($row['estado']);
            return $libro;
        }
        return null;
    }

    public function desactivarLibro($id) {
        $query = "UPDATE libros SET estado = 'inactivo' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function activarLibro($id) {
        $query = "UPDATE libros SET estado = 'activo' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function borrarRegistroLibro($id) {
        return $this->eliminarLibro($id);
    }

    public function obtenerLibrosActivos() {
        $query = "SELECT * FROM libros WHERE estado = 'activo'";
        $stmt = $this->conn->query($query);
        $libros = [];

        while($row = $stmt->fetch() )
        {
            $libro = new Libro($row['titulo'],$row['autor'],$row['isbn'],$row['cantidad']);
            $libro->setId($row['id']);
            $libro->setEstado($row['estado']);
            $libros[] = $libro;
        }
        return $libros;
    }

    // Gestión de Usuarios
    public function agregarUsuario(Usuario $usuario) {
        // TODO: Insertar usuario en base de datos
        $query = "INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre'   =>  $usuario->getNombre(),
            ':email'    =>  $usuario->getEmail(),
            ':telefono' =>  $usuario->getTelefono()
        ]);
    }

    public function editarUsuario($id, $nuevosDatos) {
        // TODO: Actualizar usuario en base de datos
        $query = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id'       =>  $id, 
            ':nombre'   =>  $nuevosDatos['nombre'],
            ':email'    =>  $nuevosDatos['email'],
            ':telefono' =>  $nuevosDatos['telefono']
        ]);
    }

    public function eliminarUsuario($id) {
        try {
            $this->conn->beginTransaction();

            // 1. Eliminar préstamos asociados
            $queryPrestamos = "DELETE FROM prestamos WHERE usuario_id = :id";
            $stmtP = $this->conn->prepare($queryPrestamos);
            $stmtP->execute([':id' => $id]);

            // 2. Eliminar el usuario
            $query = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $res = $stmt->execute([':id' => $id]);

            $this->conn->commit();
            return $res;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function obtenerUsuarios() {
        // TODO: Retornar lista de usuarios
        $query = "SELECT * FROM usuarios";
        $stmt = $this->conn->query($query);
        $usuarios = [];

        while ($row = $stmt->fetch()) {
            $usuario = new Usuario($row['nombre'],$row['email'],$row['telefono']);
            $usuario->setId($row['id']);
            $usuarios[] = $usuario;
        }
        return $usuarios;
    }

    // Gestión de Préstamos
    public function prestarLibro($libro_id, $usuario_id) {
        // Verificar si el usuario ya tiene un préstamo activo de este mismo libro
        $queryCheck = "SELECT COUNT(*) FROM prestamos WHERE libro_id = :libro_id AND usuario_id = :usuario_id AND estado = 'activo'";
        $stmtC = $this->conn->prepare($queryCheck);
        $stmtC->execute([
            ':libro_id' => $libro_id,
            ':usuario_id' => $usuario_id
        ]);
        if ($stmtC->fetchColumn() > 0) {
            return 'duplicado';
        }

        // Verificar stock disponible antes de prestar
        $libro = $this->buscarLibro($libro_id);

        if (!$libro || $libro->getCantidad() < 1 || $libro->getEstado() !== 'activo') {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            // 1. Crear el préstamo
            $prestamo = new Prestamo($libro_id, $usuario_id);
            $queryPrestamo = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado) VALUES (:libro_id, :usuario_id, :fecha_prestamo, :estado)";
            $stmtP = $this->conn->prepare($queryPrestamo);
                        
            $stmtP->execute([
                ':libro_id'       => $prestamo->getLibroId(),
                ':usuario_id'     => $prestamo->getUsuarioId(),
                ':fecha_prestamo' => $prestamo->getFechaPrestamo(),
                ':estado'         => $prestamo->getEstado() // 'activo'
            ]);

            // 2. Decrementar stock del libro
            $queryLibro = "UPDATE libros SET cantidad = cantidad - 1 WHERE id = :libro_id";
            $stmtL = $this->conn->prepare($queryLibro);
            $stmtL->execute([':libro_id' => $libro_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function devolverLibro($prestamo_id) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtenemos los detalles del prestamo para saber que libro se devuelve
            $queryBuscar = "SELECT libro_id FROM prestamos WHERE id = :id AND estado = 'activo'";
            $stmtB = $this->conn->prepare($queryBuscar);
            $stmtB->execute([':id' => $prestamo_id]);
            $res = $stmtB->fetch();

            if (!$res) {
                $this->conn->rollBack();
                return false;
            }

            $libro_id = $res['libro_id'];
            $fecha_hoy = date('Y-m-d');

            // 2. Actualizar el estado del prestamo
            $queryPrestamo = "UPDATE prestamos SET estado = 'devuelto', fecha_devolucion = :fecha WHERE id = :id";
            $stmtP = $this->conn->prepare($queryPrestamo);
            $stmtP->execute([':fecha' => $fecha_hoy, ':id' => $prestamo_id]);

            // 3. Devolver el stock al libro 
            $queryLibro = "UPDATE libros SET cantidad = cantidad + 1 WHERE id = :libro_id";
            $stmtL = $this->conn->prepare($queryLibro);
            $stmtL->execute([':libro_id' => $libro_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e ) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function obtenerPrestamosActivos() {
        $query = " SELECT p.*, l.titulo AS libro_titulo, u.nombre AS usuario_nombre 
                   FROM prestamos p 
                   JOIN libros l ON p.libro_id = l.id 
                   JOIN usuarios u ON p.usuario_id = u.id 
                   WHERE p.estado = 'activo'
                   ORDER BY p.fecha_prestamo DESC";  
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
