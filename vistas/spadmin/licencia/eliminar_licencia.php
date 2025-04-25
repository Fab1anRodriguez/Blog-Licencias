<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (!isset($_GET['clave'])) {
    header('Location: ../index.php');
    exit();
}

$clave = $_GET['clave'];

$sql = $con->prepare("DELETE FROM licencia WHERE clave = ?");

if ($sql->execute([$clave])) {
    echo "<script>alert('Licencia eliminada exitosamente');
    window.location='../index.php';</script>";
} else {
    echo "<script>alert('Error al eliminar la licencia');
    window.location='../index.php';</script>";
}
?>