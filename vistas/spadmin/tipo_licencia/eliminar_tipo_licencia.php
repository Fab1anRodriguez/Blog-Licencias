<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../tipos_licencia.php');
    exit();
}

$id_tipolicencia = $_GET['id'];

$sql = $con->prepare("DELETE FROM tipo_licencia WHERE id_tipolicencia = ?");

if ($sql->execute([$id_tipolicencia])) {
    echo "<script>alert('Tipo de licencia eliminado exitosamente');
    window.location='../tipos_licencia.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el tipo de licencia');
    window.location='../tipos_licencia.php';</script>";
}
?>