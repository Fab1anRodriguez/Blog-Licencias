<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (isset($_POST['crear'])) {
    $nom_tipolicencia = trim($_POST['nom_tipolicencia']);
    
    if (empty($nom_tipolicencia)) {
        echo "<script>alert('El nombre del tipo de licencia no puede estar vacío');
        window.location='../tipos_licencia.php';</script>";
        exit();
    }
    
    $sql = $con->prepare("INSERT INTO tipo_licencia (nom_tipolicencia) VALUES (?)");
    
    if ($sql->execute([$nom_tipolicencia])) {
        echo "<script>alert('Tipo de licencia creado exitosamente');
        window.location='../tipos_licencia.php';</script>";
    } else {
        echo "<script>alert('Error al crear el tipo de licencia');
        window.location='../tipos_licencia.php';</script>";
    }
}
?>