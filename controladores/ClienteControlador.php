<?php
// controladores/ClienteControlador.php
require_once __DIR__ . "/../modelos/ClienteModelo.php";

class ClienteControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new ClienteModelo();
    }

    public function listar($mensaje = "")
    {
        // Texto a buscar
        $buscar = trim($_GET["buscar"] ?? "");

        // Orden seleccionado (ASC o DESC)
        $orden = strtoupper($_GET["orden"] ?? "DESC");
        if ($orden !== "ASC" && $orden !== "DESC") {
            $orden = "DESC";
        }

        $lista_clientes = $this->modelo->obtenerClientes($buscar, $orden);

        // La vista usará $lista_clientes, $mensaje, $buscar y $orden
        include __DIR__ . "/../vistas/clientes/listado_clientes.php";
    }

    public function formularioNuevo($mensaje = "")
    {
        $cliente = null;
        include __DIR__ . "/../vistas/clientes/formulario_cliente.php";
    }

    public function guardarNuevo()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                "tipo_documento"   => $_POST["tipo_documento"] ?? "DNI",
                "numero_documento" => trim($_POST["numero_documento"] ?? ""),
                "nombres"          => trim($_POST["nombres"] ?? ""),
                "apellidos"        => trim($_POST["apellidos"] ?? ""),
                "razon_social"     => trim($_POST["razon_social"] ?? ""),
                "direccion"        => trim($_POST["direccion"] ?? ""),
                "correo"           => trim($_POST["correo"] ?? ""),
                "telefono"         => trim($_POST["telefono"] ?? ""),
                "estado"           => 1,
            ];

            if ($datos["tipo_documento"] === "RUC") {
                if ($datos["numero_documento"] === "" || $datos["razon_social"] === "") {
                    $mensaje = "Para RUC debe ingresar el número de RUC y la razón social.";
                    $this->formularioNuevo($mensaje);
                    return;
                }
            } else {
                if ($datos["numero_documento"] === "" || $datos["nombres"] === "") {
                    $mensaje = "Debe ingresar al menos el número de documento y los nombres del cliente.";
                    $this->formularioNuevo($mensaje);
                    return;
                }
            }

            if ($this->modelo->registrarCliente($datos)) {
                $mensaje = "Cliente registrado correctamente.";
            } else {
                $mensaje = "No se pudo registrar el cliente.";
            }

            // Después de guardar, volvemos con orden por defecto (más recientes)
            $_GET["buscar"] = "";
            $_GET["orden"]  = "DESC";
            $this->listar($mensaje);
        }
    }

    public function formularioEditar($id_cliente, $mensaje = "")
    {
        $cliente = $this->modelo->obtenerClientePorId($id_cliente);
        include __DIR__ . "/../vistas/clientes/formulario_cliente.php";
    }

    public function guardarEdicion()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                "id_cliente"       => intval($_POST["id_cliente"]),
                "tipo_documento"   => $_POST["tipo_documento"] ?? "DNI",
                "numero_documento" => trim($_POST["numero_documento"] ?? ""),
                "nombres"          => trim($_POST["nombres"] ?? ""),
                "apellidos"        => trim($_POST["apellidos"] ?? ""),
                "razon_social"     => trim($_POST["razon_social"] ?? ""),
                "direccion"        => trim($_POST["direccion"] ?? ""),
                "correo"           => trim($_POST["correo"] ?? ""),
                "telefono"         => trim($_POST["telefono"] ?? ""),
            ];

            if ($datos["tipo_documento"] === "RUC") {
                if ($datos["numero_documento"] === "" || $datos["razon_social"] === "") {
                    $mensaje = "Para RUC debe ingresar el número de RUC y la razón social.";
                    $this->formularioEditar($datos["id_cliente"], $mensaje);
                    return;
                }
            } else {
                if ($datos["numero_documento"] === "" || $datos["nombres"] === "") {
                    $mensaje = "Debe ingresar al menos el número de documento y los nombres del cliente.";
                    $this->formularioEditar($datos["id_cliente"], $mensaje);
                    return;
                }
            }

            if ($this->modelo->actualizarCliente($datos)) {
                $mensaje = "Cliente actualizado correctamente.";
            } else {
                $mensaje = "No se pudo actualizar el cliente.";
            }

            $_GET["buscar"] = "";
            $_GET["orden"]  = "DESC";
            $this->listar($mensaje);
        }
    }

    public function cambiarEstado($id_cliente, $nuevo_estado)
    {
        if ($this->modelo->cambiarEstadoCliente($id_cliente, $nuevo_estado)) {
            $mensaje = "Estado del cliente actualizado.";
        } else {
            $mensaje = "No se pudo cambiar el estado del cliente.";
        }
        $_GET["buscar"] = "";
        $_GET["orden"]  = "DESC";
        $this->listar($mensaje);
    }
}
