<?php
// controladores/ProveedorControlador.php
require_once __DIR__ . "/../modelos/ProveedorModelo.php";

class ProveedorControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new ProveedorModelo();
    }

    /* ================= LISTAR ================= */
    public function listar($mensaje = "")
    {
        $texto = trim($_GET["buscar"] ?? "");
        $proveedores = $this->modelo->obtenerProveedores($texto);

        $texto_busqueda = $texto;
        $alerta = $mensaje;

        include __DIR__ . "/../vistas/proveedores/listado_proveedores.php";
    }

    /* ================= NUEVO ================= */
    public function formularioNuevo($mensaje = "")
    {
        $proveedor = [
            "id_proveedor"     => 0,
            "tipo_documento"   => "",
            "numero_documento" => "",
            "razon_social"     => "",
            "nombre_contacto"  => "",
            "telefono"         => "",
            "correo"           => "",
            "direccion"        => "",
            "estado"           => 1,
        ];
        $modo = "nuevo";
        $alerta = $mensaje;

        include __DIR__ . "/../vistas/proveedores/formulario_proveedor.php";
    }

    public function guardarNuevo()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->listar();
            return;
        }

        $data = [
            "tipo_documento"   => trim($_POST["tipo_documento"] ?? ""),
            "numero_documento" => trim($_POST["numero_documento"] ?? ""),
            "razon_social"     => trim($_POST["razon_social"] ?? ""),
            "nombre_contacto"  => trim($_POST["nombre_contacto"] ?? ""),
            "telefono"         => trim($_POST["telefono"] ?? ""),
            "correo"           => trim($_POST["correo"] ?? ""),
            "direccion"        => trim($_POST["direccion"] ?? ""),
            "estado"           => isset($_POST["estado"]) ? 1 : 0,
        ];

        if ($data["razon_social"] === "") {
            $this->formularioNuevo("Debe ingresar la razón social del proveedor.");
            return;
        }

        $idNuevo = $this->modelo->insertar($data);

        if ($idNuevo === false) {
            $this->formularioNuevo("Error al registrar: " . $this->modelo->getUltimoError());
            return;
        }

        $this->listar("Proveedor registrado correctamente.");
    }

    /* ================= EDITAR ================= */
    public function formularioEditar($id, $mensaje = "")
    {
        $proveedor = $this->modelo->obtenerPorId($id);
        if (!$proveedor) {
            $this->listar("No se encontró el proveedor indicado.");
            return;
        }

        $modo = "editar";
        $alerta = $mensaje;

        include __DIR__ . "/../vistas/proveedores/formulario_proveedor.php";
    }

    public function guardarEdicion()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->listar();
            return;
        }

        $id = (int)($_POST["id_proveedor"] ?? 0);

        $data = [
            "tipo_documento"   => trim($_POST["tipo_documento"] ?? ""),
            "numero_documento" => trim($_POST["numero_documento"] ?? ""),
            "razon_social"     => trim($_POST["razon_social"] ?? ""),
            "nombre_contacto"  => trim($_POST["nombre_contacto"] ?? ""),
            "telefono"         => trim($_POST["telefono"] ?? ""),
            "correo"           => trim($_POST["correo"] ?? ""),
            "direccion"        => trim($_POST["direccion"] ?? ""),
            "estado"           => isset($_POST["estado"]) ? 1 : 0,
        ];

        if ($data["razon_social"] === "") {
            $this->formularioEditar($id, "Debe ingresar la razón social del proveedor.");
            return;
        }

        $ok = $this->modelo->actualizar($id, $data);

        if (!$ok) {
            $this->formularioEditar($id, "Error al actualizar: " . $this->modelo->getUltimoError());
            return;
        }

        $this->listar("Proveedor actualizado correctamente.");
    }

    /* ================= CAMBIAR ESTADO ================= */
    public function cambiarEstado($id, $estado)
    {
        $this->modelo->cambiarEstado($id, $estado);
        $this->listar();
    }
}
