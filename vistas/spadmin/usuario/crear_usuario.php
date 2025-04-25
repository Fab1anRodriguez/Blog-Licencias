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
    $doc_usu = trim($_POST['doc_usu']);
    $nom_usu = trim($_POST['nom_usu']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $id_rol = trim($_POST['id_rol']);
    $id_estado = trim($_POST['id_estado']);
    $nit_empresa = trim($_POST['nit_empresa']);
    $codigo_barras= trim($_POST['codigo_barras']);
    
    if (empty($doc_usu) || empty($nom_usu) || empty($correo) || empty($password)) {
        echo "<script>alert('Todos los campos son obligatorios');
        window.location='../usuarios.php';</script>";
        exit();
    }
    
    // Verificar si el documento ya existe
    $check = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE doc_usu = ?");
    $check->execute([$doc_usu]);
    if ($check->fetchColumn() > 0) {
        echo "<script>alert('Ya existe un usuario con este documento');
        window.location='../usuarios.php';</script>";
        exit();
    }
    
    // Verificar si el correo ya existe
    $check = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ?");
    $check->execute([$correo]);
    if ($check->fetchColumn() > 0) {
        echo "<script>alert('Ya existe un usuario con este correo');
        window.location='../usuarios.php';</script>";
        exit();
    }
    
    // Encriptar contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = $con->prepare("INSERT INTO usuarios (doc_usu, nom_usu, correo, password, id_rol, id_estado, NIT, codigo_barras) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($sql->execute([$doc_usu, $nom_usu, $correo, $password_hash, $id_rol, $id_estado, $nit_empresa])) {
        echo "<script>alert('Usuario creado exitosamente');
        window.location='../usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al crear el usuario');
        window.location='../usuarios.php';</script>";
    }
}
?>