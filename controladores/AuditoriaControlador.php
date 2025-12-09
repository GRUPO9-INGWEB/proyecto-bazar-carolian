<?php
// controladores/AuditoriaControlador.php
require_once __DIR__ . "/../modelos/AuditoriaModelo.php";

class AuditoriaControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new AuditoriaModelo();
    }

    public function listar()
    {
        // Rango de fechas por defecto: hoy
        $hoy = date('Y-m-d');

        $fecha_desde = $_GET['desde'] ?? $hoy;
        $fecha_hasta = $_GET['hasta'] ?? $hoy;

        // Filtro de módulo (LOGIN, VENTAS, PRODUCTOS, etc.)
        $modulo = $_GET['modulo_filtro'] ?? 'TODOS';

        // Texto libre (acción, descripción, tabla, etc.)
        $texto   = trim($_GET['texto']   ?? '');
        // Filtro por nombre de usuario
        $usuario = trim($_GET['usuario'] ?? '');

        // Para el <select> de módulos
        $modulosDisponibles = [
            'TODOS',
            'LOGIN',
            'VENTAS',
            'PRODUCTOS',
        ];

        // Arreglo de filtros para el modelo
        $filtros = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'modulo'      => $modulo,
            'texto'       => $texto,
            'usuario'     => $usuario,
        ];

        $eventos = $this->modelo->buscarEventos($filtros);

        // Cargamos la vista
        include __DIR__ . "/../vistas/auditoria/listado_auditoria.php";
    }
}
