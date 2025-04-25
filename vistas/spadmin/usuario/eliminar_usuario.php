<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../login.php');
    exit();
}

if (!isset($_GET['doc'])) {
    header('Location: ../usuarios.php');
    exit();
}

$doc_usu = $_GET['doc'];

// eliminar el usuario directamente
$sql = $con->prepare("DELETE FROM usuarios WHERE doc_usu = ?");

if ($sql->execute([$doc_usu])) {
    echo "<script>alert('Usuario eliminado exitosamente');
    window.location='../usuarios.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el usuario');
    window.location='../usuarios.php';</script>";
}
?>