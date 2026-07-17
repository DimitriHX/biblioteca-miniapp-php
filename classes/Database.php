<?php

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'biblioteca';
    private $username = 'root';
    private $password = '';
    public $conn;

    // Método para obtener la conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        
        // deteccion inteligente, este proyecto se realizo en entorno ubuntu pero es utilizable en entorno windows
        $actual_host = $this->host;
        if(file_exists('/.dockerenv') || file_exists('./dockerenv') || getenv('DATABASE_CONTAINER') === 'true') {
            $actual_host = 'db';
        }

        // Creamos el DSN de Mysql para el PDO de php 
        $dsn = "mysql:host=" . $actual_host . ";dbname=" . $this->db_name . ";charset=utf8mb4";


        // opciones por defecto de PDO para un manejo seguro y limpio de datos 
        $options = [
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES      => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            // Manejo de excepciones en caso de que falle la conexion 
            die("Error de conexion: " . $exception->getMessage());
        }

        return $this->conn; 
    }
}
