<?php
class Conexion {
    private $host       = "localhost";
    private $base_datos = "bd_bazarCarolian";
    private $usuario    = "root";
    private $clave      = "";
    
    /** @var PDO */
    public $conexion;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->base_datos};charset=utf8mb4";

        $opciones = [
            // Lanzar excepciones ante errores
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Devolver arrays asociativos por defecto
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Usar prepared statements nativos (mejor para seguridad)
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conexion = new PDO($dsn, $this->usuario, $this->clave, $opciones);
        } catch (PDOException $e) {
            // Para proyecto local está bien mostrar el mensaje:
            die("Error de conexión a la base de datos: " . $e->getMessage());

            // En producción sería mejor algo como:
            // error_log("Error DB: " . $e->getMessage());
            // die("Error de conexión. Intente más tarde.");
        }
    }
}
