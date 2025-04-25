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

// obtener datos del estado
$sql = $con->prepare("SELECT * FROM estado WHERE id_estado = ?");
$sql->execute([$id_estado]);
$estado = $sql->fetch(PDO::FETCH_ASSOC);

if (!$estado) {
    echo "<script>alert('Estado no encontrado');
    window.location='../estados.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_estado = trim($_POST['nom_estado']);
    
    if (empty($nom_estado)) {
        echo "<script>alert('El nombre del estado no puede estar vacío');
        window.location='editar_estado.php?id=" . $id_estado . "';</script>";
        exit();
    }
    
    $sql = $con->prepare("UPDATE estado SET nom_estado = ? WHERE id_estado = ?");
    
    if ($sql->execute([$nom_estado, $id_estado])) {
        echo "<script>alert('Estado actualizado exitosamente');
        window.location='../estados.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el estado');
        window.location='editar_estado.php?id=" . $id_estado . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estado</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Estado</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../estados.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form method="post" class="form-empresa">
                <div class="form-group">
                    <label>Nombre del Estado</label>
                    <input type="text" name="nom_estado" value="<?php echo htmlspecialchars($estado['nom_estado']); ?>" required>
                </div>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>