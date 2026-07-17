<?php

class Prestamo {
    private $id;
    private $libro_id;
    private $usuario_id;
    private $fecha_prestamo;
    private $fecha_devolucion;
    private $estado;

    public function __construct($libro_id = null, $usuario_id = null) {
        $this->libro_id = $libro_id; 
        $this->usuario_id = $usuario_id;
        $this->fecha_prestamo = date('Y-m-d');
        $this->estado = 'activo';
        $this->fecha_devolucion = null;
    }

    // Getters y Setters
    public function getId() {
        return $this->id;
    }

    // metodo setter auxiliar para recuperar el id desde la bd
    public function setId($id) {
        $this->id = $id;
    }

    public function getLibroId() {
        return $this->libro_id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function getFechaPrestamo() {
        return $this->fecha_prestamo;
    }

    // metodo setter auxiliar para cargar registros previos desde la bd 
    public function setFechaPrestamo($fecha){
        $this->fecha_prestamo = $fecha;
    }

    public function getFechaDevolucion() {
        return $this->fecha_devolucion;
    }

    public function setFechaDevolucion($fecha) {
        $this->fecha_devolucion = $fecha;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado; 
    }
}
