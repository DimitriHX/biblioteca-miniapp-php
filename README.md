# Tarea: Sistema de Gestión de Biblioteca (Mini-Aplicación OOP)

## Objetivo
Completar la implementación de un sistema de gestión de biblioteca utilizando PHP y Programación Orientada a Objetos (OOP). Se te proporciona la estructura base de archivos y clases. Tu misión es rellenar la lógica faltante.

## Configuración Inicial

1.  **Base de Datos**:
    *   Abre tu gestor de base de datos (phpMyAdmin, MySQL Workbench, etc.).
    *   Importa o ejecuta el script SQL contenido en el archivo `biblioteca.sql`.
    *   Esto creará la base de datos `biblioteca` y las tablas necesarias (`libros`, `usuarios`, `prestamos`).

2.  **Servidor Local**:
    *   Asegúrate de ejecutar este proyecto dentro de un servidor local (como XAMPP, Laragon, o el servidor integrado de PHP).

## Instrucciones de Implementación

Busca los comentarios `// TODO` en los archivos para saber exactamente qué implementar. Se recomienda seguir este orden:

### Paso 1: Conexión a Base de Datos
**Archivo:** `classes/Database.php`
*   Implementa el método `getConnection()` para retornar una conexión PDO válida a la base de datos `biblioteca`.
*   Asegúrate de que las credenciales (usuario/password) coincidan con tu configuración local.

### Paso 2: Clases de Modelo
**Archivos:** `classes/Libro.php`, `classes/Usuario.php`, `classes/Prestamo.php`
*   Completa los **constructores** para inicializar los atributos de la clase.
*   Implementa todos los métodos **Getters** y **Setters** para cada propiedad privada.

### Paso 3: Lógica de Negocio (Gestor)
**Archivo:** `classes/Biblioteca.php`
Esta clase centraliza la funcionalidad. Debes implementar:
*   **Constructor**: Inicializar la conexión a la base de datos usando la clase `Database`.
*   **CRUD de Libros**: Métodos `agregarLibro`, `editarLibro`, `eliminarLibro`, `obtenerLibros`.
*   **CRUD de Usuarios**: Métodos `agregarUsuario`, `editarUsuario`, `eliminarUsuario`, `obtenerUsuarios`.
*   **Gestión de Préstamos**:
    *   `prestarLibro($libro_id, $usuario_id)`: Debe crear el registro en la tabla `prestamos` Y disminuir el campo `cantidad` en la tabla `libros`.
    *   `devolverLibro($prestamo_id)`: Debe actualizar la `fecha_devolucion` y `estado` en `prestamos`, Y aumentar el campo `cantidad` en la tabla `libros`.

### Paso 4: Interfaz de Usuario
**Archivo:** `index.php`
*   Implementa la lógica para recibir parámetros (por ejemplo `?action=crear_libro`) y llamar a los métodos correspondientes de la clase `Biblioteca`.
*   Diseña la interfaz HTML para:
    *   Listar libros, usuarios y préstamos activos.
    *   Formularios para agregar nuevos libros y usuarios.
    *   Botones/Enlaces para "Prestar", "Devolver", "Editar" y "Eliminar".

## Requisitos
*   El código debe ser limpio y ordenado.
*   La lógica de negocio debe estar encapsulada en las clases, no en la vista (`index.php` debe usarse principalmente para mostrar datos y capturar input).
*   No es necesario un sistema de login/autenticación.

¡Mucho éxito con tu implementación!

## Instalación

Este proyecto está preparado para ejecutarse de manera portable en cualquier sistema operativo mediante Docker y Docker Compose.

### Requisitos previos obligatorios:
1. Tener instalado Docker Desktop (en Windows) o Docker Engine (en Linux).
2. Tener instalado Docker Compose v2 (integrado por defecto en versiones recientes).

### Instrucciones para Windows (PowerShell / Command Prompt)
1. Abra su terminal preferida en la carpeta raíz del proyecto (donde se encuentra el archivo `Docker-PHP.yml`).
2. Ejecute el siguiente comando para descargar imágenes, construir los servicios y levantar el entorno de desarrollo:
   ```powershell
   docker compose -f Docker-PHP.yml up -d --build
   ```
3. Abra su navegador de internet favorito e ingrese a la dirección:
   [http://localhost:8080](http://localhost:8080)
4. Si necesita detener el entorno y eliminar los datos persistidos de la base de datos para reiniciarla, ejecute:
   ```powershell
   docker compose -f Docker-PHP.yml down -v
   ```

### Instrucciones para Linux (Bash / Zsh)
1. Abra una terminal en el directorio raíz de este proyecto.
2. Inicie el entorno construyendo las imágenes y levantando los servicios en segundo plano ejecutando el siguiente comando:
   ```bash
   sudo docker compose -f Docker-PHP.yml up -d --build
   ```
3. Valide que los contenedores se encuentren activos y estables con:
   ```bash
   sudo docker ps
   ```
4. Acceda a la aplicación web del sistema de biblioteca mediante:
   [http://localhost:8080](http://localhost:8080)
5. Para detener y limpiar el entorno completo con su base de datos, corra:
   ```bash
   sudo docker compose -f Docker-PHP.yml down -v
   ```

