<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['nit'])) {
    header('Location: ../empresas.php');
    exit();
}

$nit = $_GET['nit'];

$sql = $con->prepare("DELETE FROM empresa WHERE NIT = ?");

if ($sql->execute([$nit])) {
    echo "<script>alert('Empresa eliminada exitosamente');
    window.location='../empresas.php';</script>";
} else {
    echo "<script>alert('Error al eliminar la empresa');
    window.location='../empresas.php';</script>";
}
?>