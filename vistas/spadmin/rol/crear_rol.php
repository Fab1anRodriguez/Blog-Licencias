<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (isset($_POST['crear'])) {
    $nom_rol = trim($_POST['nom_rol']);
    
    if (empty($nom_rol)) {
        echo "<script>alert('El nombre del rol es obligatorio');
        window.location='../roles.php';</script>";
        exit();
    }
    
    $sql = $con->prepare("INSERT INTO roles (nom_rol) VALUES (?)");
    
    if ($sql->execute([$nom_rol])) {
        echo "<script>alert('Rol creado exitosamente');
        window.location='../roles.php';</script>";
    } else {
        echo "<script>alert('Error al crear el rol');
        window.location='../roles.php';</script>";
    }
}
?>