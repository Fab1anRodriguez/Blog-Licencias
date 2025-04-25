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
    $nom_estado = trim($_POST['nom_estado']);
    
    if (empty($nom_estado)) {
        echo "<script>alert('El nombre del estado es obligatorio');
        window.location='../estados.php';</script>";
        exit();
    }
    
    $sql = $con->prepare("INSERT INTO estado (nom_estado) VALUES (?)");
    
    if ($sql->execute([$nom_estado])) {
        echo "<script>alert('Estado creado exitosamente');
        window.location='../estados.php';</script>";
    } else {
        echo "<script>alert('Error al crear el estado');
        window.location='../estados.php';</script>";
    }
}
?>