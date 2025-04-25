<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../roles.php');
    exit();
}

$id_rol = $_GET['id'];

$sql = $con->prepare("DELETE FROM roles WHERE id_rol = ?");

if ($sql->execute([$id_rol])) {
    echo "<script>alert('Rol eliminado exitosamente');
    window.location='../roles.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el rol');
    window.location='../roles.php';</script>";
}
?>