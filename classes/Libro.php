<?php

class Libro {
    private $id;
    private $titulo;
    private $autor;
    private $isbn;
    private $cantidad;
    private $estado;

    public function __construct($titulo = null, $autor = null, $isbn = null, $cantidad = 1) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->isbn = $isbn;
        $this->cantidad = $cantidad;
        $this->estado = 'activo';
    }

    // Getters y Setters
    public function getId() {
        return $this->id;
    }

    // metodo auxiliar para recuperar el id desde la bd 
    public function setId($id) {
        $this->id = $id;
    }

    public function getTitulo() {
        return $this->titulo; 
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo; 
    }

    public function getAutor() {
        return $this->autor;
    }

    public function setAutor($autor) {
        $this->autor = $autor; 
    }

    public function getIsbn() {
        return $this->isbn; 
    }

    public function setIsbn($isbn) {
        $this->isbn = $isbn;
    }

    public function getCantidad() {
        return $this->cantidad; 
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad; 
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}
