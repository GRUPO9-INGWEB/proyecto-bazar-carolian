<?php
class Conexion {
    private $host = "localhost";
    private $base_datos = "bd_bazarCarolian";
    private $usuario = "root";
    private $clave = "";
    public $conexion;

    public function __construct() {
        try {
            $this->conexion = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->base_datos . ";charset=utf8mb4",
                $this->usuario,
                $this->clave
            );
            // Modo de errores de PDO
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}


