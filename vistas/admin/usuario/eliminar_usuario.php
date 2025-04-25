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

// Verificar que el usuario a eliminar pertenezca a la empresa del admin y sea usuario normal
$sql = $con->prepare("
    SELECT COUNT(*) 
    FROM usuarios 
    WHERE doc_usu = ? AND NIT = ? AND id_rol = 1");
$sql->execute([$doc_usu, $_SESSION['NIT']]);

if ($sql->fetchColumn() == 0) {
    echo "<script>alert('No tiene permiso para eliminar este usuario');
    window.location='../usuarios.php';</script>";
    exit();
}

// Eliminar el usuario
$sql = $con->prepare("DELETE FROM usuarios WHERE doc_usu = ? AND NIT = ? AND id_rol = 1");

if ($sql->execute([$doc_usu, $_SESSION['NIT']])) {
    echo "<script>alert('Usuario eliminado exitosamente');
    window.location='../usuarios.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el usuario');
    window.location='../usuarios.php';</script>";
}
?>