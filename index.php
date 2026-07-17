<?php
require_once 'classes/Biblioteca.php';

$biblioteca = new Biblioteca();
$action = isset($_GET['action']) ? $_GET['action'] : 'libros';
$mensaje = '';

if (isset($_GET['delete_libro_bloqueado'])) {
    $mensaje = 'No se puede borrar un libro con prestamo.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_libro'])) {
        $biblioteca->editarLibro($_POST['libro_id'], [
            'titulo' => $_POST['titulo'],
            'autor' => $_POST['autor'],
            'isbn' => $_POST['isbn'],
            'cantidad' => intval($_POST['cantidad'])
        ]) ? $mensaje = 'Libro actualizado con exito.' : $mensaje = 'Error al actualizar el libro.';
    }

    if (isset($_POST['desactivar_libro'])) {
        $biblioteca->desactivarLibro($_POST['libro_id']) ? $mensaje = 'Libro desactivado con exito.' : $mensaje = 'Error al desactivar el libro.';
    }

    if (isset($_POST['activar_libro'])) {
        $biblioteca->activarLibro($_POST['libro_id']) ? $mensaje = 'Libro activado con exito.' : $mensaje = 'Error al activar el libro.';
    }

    if (isset($_POST['borrar_registro_libro'])) {
        if ($biblioteca->borrarRegistroLibro($_POST['libro_id'])) {
            $mensaje = 'Registro del libro borrado con exito.';
        } else {
            $mensaje = 'No se puede borrar un libro con prestamo.';
        }
    }

    if (isset($_POST['agregar_libro'])) {
        $libro = new Libro($_POST['titulo'],$_POST['autor'],$_POST['isbn'], intval($_POST['cantidad']));
        $biblioteca->agregarLibro($libro) ? $mensaje = "Libro agregado con exito. " : $mensaje = "Error al agregar el libro. "; 
    }

    if(isset($_POST['agregar_usuario'])){
        $usuario = new Usuario($_POST['nombre'],$_POST['email'],$_POST['telefono']);
        $biblioteca->agregarUsuario($usuario) ? $mensaje = "Usuario registrado con exito. " : $mensaje = "Error al agregar el usuario. ";
    }
    
    
    if (isset($_POST['realizar_prestamo'])) {
        $resultado = $biblioteca->prestarLibro($_POST['libro_id'], $_POST['usuario_id']);

        if ($resultado === true) {
            $mensaje = "Préstamo realizado con éxito.";
        } elseif ($resultado === 'duplicado') {
            $mensaje = "No puedes prestar el mismo libro dos veces.";
        } else {
            $mensaje = "No se pudo realizar el préstamo (Verifica si hay stock disponible).";
        }

        $action = 'prestamos';
    }
}

if (isset($_GET['delete_libro'])) {
    if ($biblioteca->eliminarLibro($_GET['delete_libro'])) {
        header("Location: index.php?action=libros");
    } else {
        header("Location: index.php?action=libros&delete_libro_bloqueado=1");
    }
    exit;
}

if (isset($_GET['desactivar_libro'])) {
    $biblioteca->desactivarLibro($_GET['desactivar_libro']);
    header("Location: index.php?action=libros");
    exit;
}

if (isset($_GET['activar_libro'])) {
    $biblioteca->activarLibro($_GET['activar_libro']);
    header("Location: index.php?action=libros");
    exit;
}

if (isset($_GET['delete_usuario'])) {
    $biblioteca->eliminarUsuario($_GET['delete_usuario']);
    header("Location: index.php?action=usuarios");
    exit;
}

if (isset($_GET['devolver'])) {
    $biblioteca->devolverLibro($_GET['devolver']);
    header("Location: index.php?action=prestamos");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Biblioteca</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #fdfdfd; color: #333; }
        .container { max-width: 1050px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05); border-radius: 8px; }
        h1 { color: #333; text-align: center; margin-bottom: 20px; }
        nav { margin-bottom: 20px; background: #eee; padding: 10px; border-radius: 4px; }
        nav a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; transition: color 0.2s; }
        nav a:hover { color: #666; }
        .alert { background: #e2f0d9; color: #385723; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ccdcc4; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .actions-cell { display: flex; gap: 6px; white-space: nowrap; border-bottom: 1px solid #eee; align-items: center; }
        th { background-color: #f5f5f5; color: #333; font-weight: bold; }
        .btn { display: inline-block; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; font-family: Arial, sans-serif; }
        .btn-primary { background: #555; color: white; }
        .btn-primary:hover { background: #333; }
        .btn-danger { background: #cc3333; color: white; }
        .btn-danger:hover { background: #992222; }
        .btn-success { background: #4caf50; color: white; }
        .btn-success:hover { background: #3b8b40; }
        form { background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #eee; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Biblioteca Mini-App</h1>
        
        <nav>
            <a href="index.php">Inicio / Libros</a>
            <a href="index.php?action=usuarios">Usuarios</a>
            <a href="index.php?action=prestamos">Préstamos</a>
        </nav>
        </nav>
            
        <?php if (!empty($mensaje)): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <div id="content">
            
            <!-- ================= SECCIÓN LIBROS ================= -->
            <?php if ($action === 'libros'): ?>
                <h2>Gestión de Libros</h2>
                <form id="libro-form" action="index.php?action=libros" method="POST">
                    <h3 id="libro-form-title">Agregar Nuevo Libro</h3>
                    <input type="hidden" name="libro_id" id="libro_id" value="">
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="titulo" id="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" name="autor" id="autor" required>
                    </div>
                    <div class="form-group">
                        <label>ISBN:</label>
                        <input type="text" name="isbn" id="isbn" required>
                    </div>
                    <div class="form-group">
                        <label>Cantidad Inicial Stock:</label>
                        <input type="number" name="cantidad" id="cantidad" value="1" min="1" required>
                    </div>
                    <div class="form-group" id="libro-form-actions" style="display:none;">
                        <button type="submit" name="actualizar_libro" class="btn btn-primary">Actualizar Libro</button>
                        <button type="submit" name="desactivar_libro" id="libro-desactivar-btn" class="btn btn-danger">Desactivar Libro</button>
                        <button type="submit" name="activar_libro" id="libro-activar-btn" class="btn btn-success" style="display:none;">Activar Libro</button>
                        <button type="submit" name="borrar_registro_libro" class="btn btn-danger" onclick="return confirm('¿Desea borrar definitivamente este registro?')">Borrar Registro</button>
                    </div>
                    <button type="submit" name="agregar_libro" id="libro-create-btn" class="btn btn-primary">Guardar Libro</button>
                </form>

                <h3>Listado de Libros</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($biblioteca->obtenerLibros() as $lib): ?>
                        <tr class="libro-row" onclick="cargarLibro('<?= $lib->getId() ?>','<?= htmlspecialchars($lib->getTitulo(), ENT_QUOTES) ?>','<?= htmlspecialchars($lib->getAutor(), ENT_QUOTES) ?>','<?= htmlspecialchars($lib->getIsbn(), ENT_QUOTES) ?>','<?= $lib->getCantidad() ?>','<?= $lib->getEstado() ?>')" style="cursor:pointer;">
                            <td><?= $lib->getId() ?></td>
                            <td><?= htmlspecialchars($lib->getTitulo()) ?></td>
                            <td><?= htmlspecialchars($lib->getAutor()) ?></td>
                            <td><?= htmlspecialchars($lib->getIsbn()) ?></td>
                            <td><?= $lib->getCantidad() ?></td>
                            <td><?= $lib->getEstado() === 'activo' ? 'En librerías' : 'En almacén' ?></td>
                            <td class="actions-cell">
                                <?php if ($lib->getEstado() === 'activo' && $lib->getCantidad() > 0): ?>
                                    <a href="index.php?action=prestamos&libro_id=<?= $lib->getId() ?>" class="btn btn-success" onclick="event.stopPropagation();">Prestar</a>
                                <?php endif; ?>
                                <?php if ($lib->getEstado() === 'inactivo'): ?>
                                    <a href="index.php?activar_libro=<?= $lib->getId() ?>" class="btn btn-success" onclick="event.stopPropagation();">Activar</a>
                                <?php endif; ?>
                                <button class="btn btn-primary" onclick="event.stopPropagation(); cargarLibro('<?= $lib->getId() ?>','<?= htmlspecialchars($lib->getTitulo(), ENT_QUOTES) ?>','<?= htmlspecialchars($lib->getAutor(), ENT_QUOTES) ?>','<?= htmlspecialchars($lib->getIsbn(), ENT_QUOTES) ?>','<?= $lib->getCantidad() ?>','<?= $lib->getEstado() ?>')">Editar</button>
                                <a href="index.php?delete_libro=<?= $lib->getId() ?>" class="btn btn-danger" onclick="event.stopPropagation(); return confirm('¿Seguro de eliminar este libro?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- ================= SECCIÓN USUARIOS ================= -->
            <?php elseif ($action === 'usuarios'): ?>
                <h2>Gestión de Usuarios</h2>
                <form action="index.php?action=usuarios" method="POST">
                    <h3>Registrar Nuevo Usuario</h3>
                    <div class="form-group">
                        <label>Nombre Completo:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" name="telefono">
                    </div>
                    <button type="submit" name="agregar_usuario" class="btn btn-primary">Registrar Usuario</button>
                </form>

                <h3>Listado de Usuarios</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($biblioteca->obtenerUsuarios() as $usu): ?>
                        <tr>
                            <td><?= $usu->getId() ?></td>
                            <td><?= htmlspecialchars($usu->getNombre()) ?></td>
                            <td><?= htmlspecialchars($usu->getEmail()) ?></td>
                            <td><?= htmlspecialchars($usu->getTelefono()) ?></td>
                            <td>
                                <a href="index.php?delete_usuario=<?= $usu->getId() ?>" class="btn btn-danger" onclick="return confirm('¿Seguro de eliminar este usuario?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- ================= SECCIÓN PRÉSTAMOS ================= -->
            <?php elseif ($action === 'prestamos'): ?>
                <h2>Control de Préstamos</h2>
                <form action="index.php?action=prestamos" method="POST">
                    <h3>Generar Nuevo Préstamo</h3>
                    <div class="form-group">
                        <label>Seleccionar Libro:</label>
                        <select name="libro_id" required>
                            <?php 
                            $selected_libro = isset($_GET['libro_id']) ? intval($_GET['libro_id']) : 0;
                            foreach($biblioteca->obtenerLibrosActivos() as $lib): 
                            ?>
                                <option value="<?= $lib->getId() ?>" <?= $lib->getCantidad() < 1 ? 'disabled' : '' ?> <?= $lib->getId() == $selected_libro ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lib->getTitulo()) ?> (Disponibles: <?= $lib->getCantidad() ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Usuario:</label>
                        <select name="usuario_id" required>
                            <?php foreach($biblioteca->obtenerUsuarios() as $usu): ?>
                                <option value="<?= $usu->getId() ?>"><?= htmlspecialchars($usu->getNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="realizar_prestamo" class="btn btn-success">Prestar</button>
                </form>

                <h3>Préstamos Activos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID Préstamo</th>
                            <th>Libro</th>
                            <th>Usuario</th>
                            <th>Fecha Préstamo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($biblioteca->obtenerPrestamosActivos() as $pres): ?>
                        <tr>
                            <td><?= $pres['id'] ?></td>
                            <td><?= htmlspecialchars($pres['libro_titulo']) ?></td>
                            <td><?= htmlspecialchars($pres['usuario_nombre']) ?></td>
                            <td><?= $pres['fecha_prestamo'] ?></td>
                            <td><strong><?= $pres['estado'] ?></strong></td>
                            <td>
                                <a href="index.php?devolver=<?= $pres['id'] ?>" class="btn btn-primary">Devolver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
<script>
function cargarLibro(id, titulo, autor, isbn, cantidad, estado) {
    document.getElementById('libro_id').value = id;
    document.getElementById('titulo').value = titulo;
    document.getElementById('autor').value = autor;
    document.getElementById('isbn').value = isbn;
    document.getElementById('cantidad').value = cantidad;
    document.getElementById('libro-form-title').textContent = 'Editar Libro';
    document.getElementById('libro-create-btn').style.display = 'none';
    document.getElementById('libro-form-actions').style.display = 'block';
    
    if (estado === 'activo') {
        document.getElementById('libro-desactivar-btn').style.display = 'inline-block';
        document.getElementById('libro-activar-btn').style.display = 'none';
    } else {
        document.getElementById('libro-desactivar-btn').style.display = 'none';
        document.getElementById('libro-activar-btn').style.display = 'inline-block';
    }
}
</script>
</html>
