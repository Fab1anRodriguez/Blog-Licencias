<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

if (isset($_POST['publicar'])) {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $doc_usu = $_SESSION['user_id'];
    $id_categoria = $_POST['id_categoria'];
    
    if (empty($titulo) || empty($contenido)) {
        echo "<script>alert('Por favor completa todos los campos');window.location='../index.php';</script>";
        exit();
    }
    
    $sql = $con->prepare("INSERT INTO post (titulo, contenido, doc_usu,id_categoria) VALUES (?, ?, ?,?)");
    if ($sql->execute([$titulo, $contenido, $doc_usu, $id_categoria])) {
        echo "<script>alert('Publicación creada exitosamente');
        window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error al crear la publicación');
        window.location='index.php';</script>";
    }
}
?>