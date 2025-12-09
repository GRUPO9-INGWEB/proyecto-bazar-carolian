<?php
// modelos/AuditoriaModelo.php
require_once __DIR__ . "/../config/conexion.php";

class AuditoriaModelo extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Registra un evento en la tabla tb_auditoria
     *
     * @param int         $id_usuario     ID del usuario que hace la acción
     * @param string      $modulo         Nombre del módulo (USUARIOS, VENTAS, LOGIN, etc.)
     * @param string      $accion         Acción realizada (CREAR, EDITAR, ELIMINAR, LOGIN, LOGOUT, etc.)
     * @param string      $descripcion    Texto descriptivo
     * @param string|null $tabla_afectada Nombre de la tabla (tb_usuarios, tb_ventas, ...)
     * @param int|null    $id_registro    ID del registro afectado
     * @return bool
     */
    public function registrarEvento(
        int $id_usuario,
        string $modulo,
        string $accion,
        string $descripcion,
        ?string $tabla_afectada = null,
        ?int $id_registro = null
    ): bool {
        // Obtener IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $sql = "INSERT INTO tb_auditoria
                    (id_usuario, fecha_hora, modulo, accion, descripcion,
                     tabla_afectada, id_registro_afectado, direccion_ip)
                VALUES
                    (:id_usuario, NOW(), :modulo, :accion, :descripcion,
                     :tabla_afectada, :id_registro, :direccion_ip)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id_usuario'     => $id_usuario,
            ':modulo'         => $modulo,
            ':accion'         => $accion,
            ':descripcion'    => $descripcion,
            ':tabla_afectada' => $tabla_afectada,
            ':id_registro'    => $id_registro,
            ':direccion_ip'   => $ip,
        ]);
    }

    /**
     * Listado simple (si lo quieres seguir usando en algún lado)
     */
    public function obtenerAuditoria($limite = 200): array
    {
        $sql = "SELECT a.*,
                       u.nombre_usuario
                FROM tb_auditoria a
                LEFT JOIN tb_usuarios u
                       ON a.id_usuario = u.id_usuario
                ORDER BY a.fecha_hora DESC
                LIMIT :limite";

        $stmt = $this->conexion->prepare($sql);
        $limite = (int)$limite;
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Búsqueda con filtros para el módulo Auditoría
     *
     * $filtros = [
     *   'fecha_desde' => 'Y-m-d',
     *   'fecha_hasta' => 'Y-m-d',
     *   'modulo'      => 'VENTAS' / 'TODOS',
     *   'texto'       => 'REGISTRAR',
     *   'usuario'     => 'admin'
     * ]
     */
    public function buscarEventos(array $filtros = []): array
    {
        $sql = "SELECT
                    a.*,
                    u.nombre_usuario
                FROM tb_auditoria a
                LEFT JOIN tb_usuarios u
                       ON a.id_usuario = u.id_usuario
                WHERE 1 = 1";

        $params = [];

        $fechaDesde = $filtros['fecha_desde'] ?? null;
        $fechaHasta = $filtros['fecha_hasta'] ?? null;
        $modulo     = $filtros['modulo']      ?? 'TODOS';
        $texto      = trim($filtros['texto']  ?? '');
        $usuario    = trim($filtros['usuario'] ?? '');

        if ($fechaDesde) {
            $sql .= " AND a.fecha_hora >= :desde";
            $params[':desde'] = $fechaDesde . " 00:00:00";
        }

        if ($fechaHasta) {
            $sql .= " AND a.fecha_hora <= :hasta";
            $params[':hasta'] = $fechaHasta . " 23:59:59";
        }

        if ($modulo !== 'TODOS' && $modulo !== '') {
            $sql .= " AND a.modulo = :modulo";
            $params[':modulo'] = $modulo;
        }

        if ($texto !== '') {
            $sql .= " AND (
                        a.accion        LIKE :texto
                     OR a.descripcion   LIKE :texto
                     OR a.tabla_afectada LIKE :texto
                    )";
            $params[':texto'] = '%' . $texto . '%';
        }

        if ($usuario !== '') {
            $sql .= " AND u.nombre_usuario LIKE :usuario";
            $params[':usuario'] = '%' . $usuario . '%';
        }

        $sql .= " ORDER BY a.fecha_hora DESC, a.id_auditoria DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
