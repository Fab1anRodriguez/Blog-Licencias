<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
    header('Location: ../../../index.php');
    exit();
}

if (!isset($_GET['doc'])) {
    header('Location: ../usuarios.php');
    exit();
}

$doc_usu = $_GET['doc'];

// Eliminar usuario asegurando que pertenezca a la empresa del admin
$sql = $con->prepare("DELETE FROM usuarios WHERE doc_usu = ? AND nit_empresa = ? AND id_rol = 3");

if ($sql->execute([$doc_usu, $_SESSION['NIT']])) {
    echo "<script>alert('Usuario eliminado exitosamente');
    window.location='../usuarios.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el usuario');
    window.location='../usuarios.php';</script>";
}
?>