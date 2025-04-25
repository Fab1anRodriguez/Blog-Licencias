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

if (!isset($_GET['id'])) {
    header('Location: ../tipos_licencia.php');
    exit();
}

$id_tipolicencia = $_GET['id'];

// obtener datos del tipo de licencia
$sql = $con->prepare("SELECT * FROM tipo_licencia WHERE id_tipolicencia = ?");
$sql->execute([$id_tipolicencia]);
$tipo = $sql->fetch(PDO::FETCH_ASSOC);

if (!$tipo) {
    echo "<script>alert('Tipo de licencia no encontrado');
    window.location='../tipos_licencia.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_tipolicencia = trim($_POST['nom_tipolicencia']);
    
    if (empty($nom_tipolicencia)) {
        echo "<script>alert('El nombre del tipo de licencia no puede estar vacío');
        window.location='editar_tipo_licencia.php?id=" . $id_tipolicencia . "';</script>";
        exit();
    }
    
    $sql = $con->prepare("UPDATE tipo_licencia SET nom_tipolicencia = ? WHERE id_tipolicencia = ?");
    
    if ($sql->execute([$nom_tipolicencia, $id_tipolicencia])) {
        echo "<script>alert('Tipo de licencia actualizado exitosamente');
        window.location='../tipos_licencia.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el tipo de licencia');
        window.location='editar_tipo_licencia.php?id=" . $id_tipolicencia . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tipo de Licencia</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Tipo de Licencia</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../tipos_licencia.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <h2>Editar Tipo de Licencia</h2>
            <form action="" method="post" class="form-tipo">
                <div class="form-group">
                    <label>Nombre del Tipo de Licencia</label>
                    <input type="text" name="nom_tipolicencia" value="<?php echo htmlspecialchars($tipo['nom_tipolicencia']); ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" name="editar">Guardar Cambios</button>
                    <a href="../tipos_licencia.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>