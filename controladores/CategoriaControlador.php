<?php
// controladores/CategoriaControlador.php
require_once __DIR__ . "/../modelos/CategoriaModelo.php";

class CategoriaControlador {

    private $modelo;

    public function __construct() {
        $this->modelo = new CategoriaModelo();
    }

    public function listar($mensaje = "") {
        // Texto que el usuario escribió para buscar
        $buscar = trim($_GET["buscar"] ?? "");

        // Orden que seleccionó (ASC o DESC)
        $orden = strtoupper($_GET["orden"] ?? "DESC");
        if ($orden !== "ASC" && $orden !== "DESC") {
            $orden = "DESC";
        }

        // Pedimos las categorías al modelo con búsqueda + orden
        $lista_categorias = $this->modelo->obtenerTodasCategorias($buscar, $orden);

        include __DIR__ . "/../vistas/categorias/listado_categorias.php";
    }

    public function formularioNuevo($mensaje = "") {
        $categoria = null;
        include __DIR__ . "/../vistas/categorias/formulario_categoria.php";
    }

    public function guardarNuevo() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = trim($_POST["nombre_categoria"] ?? "");
            $descripcion = trim($_POST["descripcion_categoria"] ?? "");

            if ($nombre === "") {
                $mensaje = "El nombre de la categoría es obligatorio.";
                $this->formularioNuevo($mensaje);
                return;
            }

            if ($this->modelo->registrarCategoria($nombre, $descripcion, 1)) {
                $mensaje = "Categoría registrada correctamente.";
            } else {
                $mensaje = "No se pudo registrar la categoría.";
            }

            $this->listar($mensaje);
        }
    }

    public function formularioEditar($id_categoria, $mensaje = "") {
        $categoria = $this->modelo->obtenerCategoriaPorId($id_categoria);
        include __DIR__ . "/../vistas/categorias/formulario_categoria.php";
    }

    public function guardarEdicion() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id_categoria = intval($_POST["id_categoria"]);
            $nombre = trim($_POST["nombre_categoria"] ?? "");
            $descripcion = trim($_POST["descripcion_categoria"] ?? "");

            if ($nombre === "") {
                $mensaje = "El nombre de la categoría es obligatorio.";
                $this->formularioEditar($id_categoria, $mensaje);
                return;
            }

            if ($this->modelo->actualizarCategoria($id_categoria, $nombre, $descripcion)) {
                $mensaje = "Categoría actualizada correctamente.";
            } else {
                $mensaje = "No se pudo actualizar la categoría.";
            }

            $this->listar($mensaje);
        }
    }

    public function cambiarEstado($id_categoria, $nuevo_estado) {
        if ($this->modelo->cambiarEstadoCategoria($id_categoria, $nuevo_estado)) {
            $mensaje = "Estado de la categoría actualizado.";
        } else {
            $mensaje = "No se pudo cambiar el estado de la categoría.";
        }
        $this->listar($mensaje);
    }
}
