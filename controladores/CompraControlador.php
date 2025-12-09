<?php
// controladores/CompraControlador.php
require_once __DIR__ . "/../modelos/CompraModelo.php";

class CompraControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new CompraModelo();
    }

    /* ============= LISTAR ============= */
    public function listar($mensaje = "")
    {
        $texto       = trim($_GET["buscar"] ?? "");
        $orden       = $_GET["orden"] ?? "recientes";
        $tipo_filtro = $_GET["tipo_filtro"] ?? "TODOS";

        $lista_compras = $this->modelo->obtenerCompras($texto, $orden, $tipo_filtro);

        include __DIR__ . "/../vistas/compras/listado_compras.php";
    }

    /* ============= NUEVA COMPRA ============= */
    public function formularioNueva($mensaje = "")
    {
        $proveedores     = $this->modelo->obtenerProveedoresActivos();
        $tipos_comprobante = $this->modelo->obtenerTiposComprobanteActivos();
        $productos       = $this->modelo->obtenerProductosActivos();

        include __DIR__ . "/../vistas/compras/formulario_compra.php";
    }

    public function guardarNueva()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->listar();
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario    = $_SESSION["id_usuario"] ?? 0;
        $id_proveedor  = (int)($_POST["id_proveedor"] ?? 0);
        $id_tipo_comp  = (int)($_POST["id_tipo_comprobante"] ?? 0);
        $serie         = trim($_POST["serie_comprobante"] ?? "");
        $numero        = trim($_POST["numero_comprobante"] ?? "");

        if ($id_proveedor <= 0) {
            $this->formularioNueva("Debe seleccionar un proveedor.");
            return;
        }

        if ($id_tipo_comp <= 0) {
            $this->formularioNueva("Debe seleccionar un tipo de comprobante.");
            return;
        }

        // Detalle
        $idsProd       = $_POST["id_producto"] ?? [];
        $cantidades    = $_POST["cantidad"] ?? [];
        $preciosCompra = $_POST["precio_compra"] ?? [];

        $detalles = [];
        for ($i = 0; $i < count($idsProd); $i++) {
            $idProd = (int)$idsProd[$i];
            $cant   = (int)($cantidades[$i] ?? 0);
            $precio = (float)($preciosCompra[$i] ?? 0);

            if ($idProd > 0 && $cant > 0 && $precio >= 0) {
                $detalles[] = [
                    "id_producto"   => $idProd,
                    "cantidad"      => $cant,
                    "precio_compra" => $precio,
                ];
            }
        }

        if (empty($detalles)) {
            $this->formularioNueva("Debe agregar al menos un producto a la compra.");
            return;
        }

        $cabecera = [
            "id_usuario"          => $id_usuario,
            "id_proveedor"        => $id_proveedor,
            "id_tipo_comprobante" => $id_tipo_comp,
            "serie_comprobante"   => $serie,
            "numero_comprobante"  => $numero,
        ];

        $idCompra = $this->modelo->registrarCompra($cabecera, $detalles);

        if ($idCompra === false) {
            $error = $this->modelo->getUltimoError();
            if ($error === "") {
                $error = "No se pudo registrar la compra.";
            }
            $this->formularioNueva($error);
            return;
        }

        $this->listar("Compra registrada correctamente (ID: $idCompra).");
    }

    /* ============= VER DETALLE ============= */
    public function ver($id_compra)
    {
        $data = $this->modelo->obtenerCompraCompleta($id_compra);

        if ($data === false) {
            $this->listar("No se encontró la compra indicada.");
            return;
        }

        $compra   = $data["compra"];
        $detalles = $data["detalles"];

        include __DIR__ . "/../vistas/compras/ver_compra.php";
    }
}
