<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['crear'])) {
    $nit = trim($_POST['nit']);
    $nom_empresa = trim($_POST['nom_empresa']);
    $direccion = trim($_POST['direccion']);
    $correo = trim($_POST['correo']);
    $id_estado = trim($_POST['id_estado']); // Estado por defecto al crear una empresa
    
    if (empty($nit) || empty($nom_empresa)) {
        echo "<script>alert('El NIT y el nombre de la empresa son obligatorios');
        window.location='../empresas.php';</script>";
        exit();
    }
    
    // Verificar si el NIT ya existe
    $sql = $con->prepare("SELECT COUNT(*) FROM empresa WHERE NIT = ?");
    $sql->execute([$nit]);
    if ($sql->fetchColumn() > 0) {
        echo "<script>alert('Ya existe una empresa con este NIT');
        window.location='../empresas.php';</script>";
        exit();
    }
    
    $sql = $con->prepare("INSERT INTO empresa (NIT, nom_empresa, direccion, correo, id_estado) VALUES (?, ?, ?, ?, ?)");
    
    if ($sql->execute([$nit, $nom_empresa, $direccion, $correo, $id_estado])) {
        echo "<script>alert('Empresa creada exitosamente');
        window.location='../empresas.php';</script>";
    } else {
        echo "<script>alert('Error al crear la empresa');
        window.location='../empresas.php';</script>";
    }
}
?>