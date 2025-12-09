<?php
// controladores/ProductoControlador.php
require_once __DIR__ . "/../modelos/ProductoModelo.php";
require_once __DIR__ . "/../modelos/AuditoriaModelo.php"; // auditoría

class ProductoControlador
{
    private $modelo;
    private $auditoriaModelo; // auditoría

    public function __construct()
    {
        $this->modelo          = new ProductoModelo();
        $this->auditoriaModelo = new AuditoriaModelo();
    }

    /* ================= LISTAR ================= */

    public function listar($mensaje = "")
    {
        $buscar = trim($_GET["buscar"] ?? "");

        $orden = strtoupper($_GET["orden"] ?? "DESC");
        if ($orden !== "ASC" && $orden !== "DESC") {
            $orden = "DESC";
        }

        $lista_productos = $this->modelo->obtenerTodosProductos($buscar, $orden);
        include __DIR__ . "/../vistas/productos/listado_productos.php";
    }

    /* ================= NUEVO ================= */

    public function formularioNuevo($mensaje = "")
    {
        $producto         = null;
        $lista_categorias = $this->modelo->obtenerCategoriasActivas();
        include __DIR__ . "/../vistas/productos/formulario_producto.php";
    }

    public function guardarNuevo()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $fecha_caducidad = $_POST["fecha_caducidad"] ?? null;
            if ($fecha_caducidad === "") {
                $fecha_caducidad = null;
            }

            $datos = [
                "id_categoria"         => intval($_POST["id_categoria"]),
                "codigo_interno"       => trim($_POST["codigo_interno"] ?? ""),
                "codigo_barras"        => trim($_POST["codigo_barras"] ?? ""),
                "nombre_producto"      => trim($_POST["nombre_producto"] ?? ""),
                "descripcion_producto" => trim($_POST["descripcion_producto"] ?? ""),
                "fecha_caducidad"      => $fecha_caducidad,
                "stock_actual"         => intval($_POST["stock_actual"] ?? 0),
                "stock_minimo"         => intval($_POST["stock_minimo"] ?? 0),
                "precio_compra"        => floatval($_POST["precio_compra"] ?? 0),
                "precio_venta"         => floatval($_POST["precio_venta"] ?? 0),
                "afecta_igv"           => isset($_POST["afecta_igv"]) ? 1 : 0,
                "estado"               => 1,
            ];

            if ($datos["id_categoria"] <= 0 || $datos["nombre_producto"] === "") {
                $mensaje = "Debe seleccionar una categoría y escribir el nombre del producto.";
                $this->formularioNuevo($mensaje);
                return;
            }

            if ($this->modelo->registrarProducto($datos)) {
                $mensaje = "Producto registrado correctamente.";

                // AUDITORÍA: registro de producto
                $this->registrarAuditoriaProductos(
                    'REGISTRAR',
                    'Registro de producto: ' . $datos['nombre_producto'],
                    null
                );
            } else {
                $mensaje = "No se pudo registrar el producto.";
            }

            $this->listar($mensaje);
        }
    }

    /* ================= EDITAR ================= */

    public function formularioEditar($id_producto, $mensaje = "")
    {
        $producto         = $this->modelo->obtenerProductoPorId($id_producto);
        $lista_categorias = $this->modelo->obtenerCategoriasActivas();
        include __DIR__ . "/../vistas/productos/formulario_producto.php";
    }

    public function guardarEdicion()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $fecha_caducidad = $_POST["fecha_caducidad"] ?? null;
            if ($fecha_caducidad === "") {
                $fecha_caducidad = null;
            }

            $datos = [
                "id_producto"          => intval($_POST["id_producto"]),
                "id_categoria"         => intval($_POST["id_categoria"]),
                "codigo_interno"       => trim($_POST["codigo_interno"] ?? ""),
                "codigo_barras"        => trim($_POST["codigo_barras"] ?? ""),
                "nombre_producto"      => trim($_POST["nombre_producto"] ?? ""),
                "descripcion_producto" => trim($_POST["descripcion_producto"] ?? ""),
                "fecha_caducidad"      => $fecha_caducidad,
                "stock_actual"         => intval($_POST["stock_actual"] ?? 0),
                "stock_minimo"         => intval($_POST["stock_minimo"] ?? 0),
                "precio_compra"        => floatval($_POST["precio_compra"] ?? 0),
                "precio_venta"         => floatval($_POST["precio_venta"] ?? 0),
                "afecta_igv"           => isset($_POST["afecta_igv"]) ? 1 : 0,
            ];

            if ($datos["id_categoria"] <= 0 || $datos["nombre_producto"] === "") {
                $mensaje = "Debe seleccionar una categoría y escribir el nombre del producto.";
                $this->formularioEditar($datos["id_producto"], $mensaje);
                return;
            }

            if ($this->modelo->actualizarProducto($datos)) {
                $mensaje = "Producto actualizado correctamente.";

                // AUDITORÍA: edición de producto
                $this->registrarAuditoriaProductos(
                    'EDITAR',
                    'Edición de producto ID ' . $datos['id_producto'],
                    (int)$datos['id_producto']
                );
            } else {
                $mensaje = "No se pudo actualizar el producto.";
            }

            $this->listar($mensaje);
        }
    }

    /* ================= CAMBIAR ESTADO ================= */

    public function cambiarEstado($id_producto, $nuevo_estado)
    {
        $id_producto  = (int)$id_producto;
        $nuevo_estado = (int)$nuevo_estado;

        if ($this->modelo->cambiarEstadoProducto($id_producto, $nuevo_estado)) {
            $mensaje = "Estado del producto actualizado.";

            $textoEstado = $nuevo_estado === 1 ? 'ACTIVAR' : 'DESACTIVAR';

            $this->registrarAuditoriaProductos(
                'CAMBIAR_ESTADO',
                $textoEstado . ' producto ID ' . $id_producto,
                $id_producto
            );
        } else {
            $mensaje = "No se pudo cambiar el estado del producto.";
        }

        $this->listar($mensaje);
    }

    /* ================== AUDITORÍA (HELPER PRIVADO) ================== */

    private function registrarAuditoriaProductos(
        string $accion,
        string $descripcion,
        ?int $idRegistro = null
    ): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        if (!$idUsuario) {
            return;
        }

        // Llamada POSICIONAL, sin IP (se guarda NULL en direccion_ip)
        $this->auditoriaModelo->registrarEvento(
            $idUsuario,        // id_usuario
            'PRODUCTOS',       // modulo
            $accion,           // accion
            $descripcion,      // descripcion
            'tb_productos',    // tabla_afectada
            $idRegistro,       // id_registro_afectado
        );
    }
}
