<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../roles.php');
    exit();
}

$id_rol = $_GET['id'];

$sql = $con->prepare("SELECT * FROM roles WHERE id_rol = ?");
$sql->execute([$id_rol]);
$rol = $sql->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    echo "<script>alert('Rol no encontrado');
    window.location='../roles.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_rol = trim($_POST['nom_rol']);
    
    if (empty($nom_rol)) {
        echo "<script>alert('El nombre del rol no puede estar vacío');
        window.location='editar_rol.php?id=" . $id_rol . "';</script>";
        exit();
    }
    
    $sql = $con->prepare("UPDATE roles SET nom_rol = ? WHERE id_rol = ?");
    
    if ($sql->execute([$nom_rol, $id_rol])) {
        echo "<script>alert('Rol actualizado exitosamente');
        window.location='../roles.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el rol');
        window.location='editar_rol.php?id=" . $id_rol . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rol</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Rol</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../roles.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form method="post" class="form-empresa">
                <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" name="nom_rol" value="<?php echo htmlspecialchars($rol['nom_rol']); ?>" required>
                </div>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>