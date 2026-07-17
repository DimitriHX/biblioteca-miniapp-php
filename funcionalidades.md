# Funcionalidades del Sistema e Implementaciones Extras

Este documento describe de manera breve y concisa las funcionalidades principales del Sistema de Gestión de Biblioteca, así como las implementaciones adicionales incorporadas para mejorar la robustez, portabilidad y experiencia de usuario del proyecto.

---

## 1. Funcionalidades Base del Sistema

El sistema implementa un modelo de programación orientada a objetos (OOP) y una interfaz basada en PHP para gestionar los procesos esenciales de una biblioteca:

*   **Gestión de Libros (CRUD completo):** Permite registrar nuevos libros (con título, autor, ISBN y stock inicial), listar los libros existentes, editarlos mediante carga dinámica en un formulario y eliminarlos.
*   **Gestión de Usuarios (CRUD básico):** Permite el registro de nuevos usuarios (nombre, correo electrónico y teléfono), listar los usuarios y eliminarlos del sistema.
*   **Control de Préstamos y Devoluciones:**
    *   **Préstamo:** Permite asociar un libro a un usuario, disminuyendo en una unidad el stock disponible del libro seleccionado.
    *   **Devolución:** Permite registrar la devolución de un libro por su ID de préstamo, restableciendo el stock disponible en los libros e insertando la fecha de devolución correspondiente.

---

## 2. Implementaciones Extras y Mejoras Adicionales

Para superar los requisitos mínimos solicitados en el [README.md](README.md), se incorporaron las siguientes mejoras técnicas:

### A. Detección Inteligente de Entorno (Docker / Local)
*   **Archivo:** [Database.php](classes/Database.php)
*   **Detalle:** El sistema detecta de forma automatizada si se está ejecutando dentro de un contenedor Docker mediante la verificación de la existencia de `/.dockerenv` o variables de entorno. Cambia de forma automática el host de base de datos de `127.0.0.1` a `db`. Esto facilita una portabilidad inmediata tanto en entornos locales tradicionales (como XAMPP/Laragon) como con Docker.

### B. Transacciones de Base de Datos (Garantía de Integridad)
*   **Archivo:** [Biblioteca.php](classes/Biblioteca.php)
*   **Detalle:** Todas las operaciones críticas de escritura (registro de préstamos, devoluciones, eliminaciones) se encapsulan dentro de bloques de transacciones de PDO (`beginTransaction()`, `commit()`, `rollBack()`). Si alguna consulta falla, la base de datos se revierte al estado anterior, impidiendo datos inconsistentes o registros huérfanos (por ejemplo, restar stock de libro sin registrar el préstamo).

### C. Prevención de Préstamos Duplicados y Validación de Stock
*   **Archivo:** [Biblioteca.php](classes/Biblioteca.php)
*   **Detalle:**
    *   Antes de conceder un préstamo, se verifica que el usuario no posea un préstamo activo previo del mismo libro. Si lo tiene, se impide la acción retornando el estado `'duplicado'`.
    *   Se valida que el libro posea stock disponible (`cantidad > 0`) antes de permitir el préstamo.

### D. Control de Estado del Libro (Activo/Inactivo)
*   **Archivos:** [Libro.php](classes/Libro.php), [Biblioteca.php](classes/Biblioteca.php) e [index.php](index.php)
*   **Detalle:** Se añadió el campo `estado` al modelo de libro. Un libro puede ser activado o desactivado ("En almacén" / "En librerías"). Solo los libros activos se muestran como elegibles para la generación de nuevos préstamos.

### E. Interfaz de Usuario Dinámica en Una Sola Página
*   **Archivo:** [index.php](index.php)
*   **Detalle:** 
    *   Se programó lógica JavaScript para cargar dinámicamente cualquier libro seleccionado en el listado directamente en el formulario superior, facilitando su edición, desactivación o borrado definitivo sin recargar la página.
    *   Se implementaron notificaciones de alerta interactivas para informar al usuario sobre el éxito o fallo de cada acción realizada.
    *   Se añadieron ventanas de confirmación nativas en JavaScript para prevenir la eliminación accidental de libros o usuarios.
