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
    header('Location: ../estados.php');
    exit();
}

$id_estado = $_GET['id'];

$sql = $con->prepare("DELETE FROM estado WHERE id_estado = ?");

if ($sql->execute([$id_estado])) {
    echo "<script>alert('Estado eliminado exitosamente');
    window.location='../estados.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el estado');
    window.location='../estados.php';</script>";
}
?>