<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['nit'])) {
    header('Location: empresas.php');
    exit();
}

$nit = $_GET['nit'];

$sql = $con->prepare("SELECT * FROM empresa WHERE NIT = ?");
$sql->execute([$nit]);
$empresa = $sql->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    echo "<script>alert('Empresa no encontrada');
    window.location='../empresas.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_empresa = trim($_POST['nom_empresa']);
    $direccion = trim($_POST['direccion']);
    $correo = trim($_POST['correo']);
    $id_estado = trim($_POST['id_estado']);
    
    if (empty($nom_empresa)) {
        echo "<script>alert('El nombre de la empresa no puede estar vacío');
        window.location='editar_empresa.php?nit=" . $nit . "';</script>";
        exit();
    }
    
    $sql = $con->prepare("UPDATE empresa SET nom_empresa = ?, direccion = ?, correo = ?, id_estado = ? WHERE NIT = ?");
    
    if ($sql->execute([$nom_empresa, $direccion, $correo, $id_estado, $nit])) {
        echo "<script>alert('Empresa actualizada exitosamente');
        window.location='../empresas.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la empresa');
        window.location='editar_empresa.php?nit=" . $nit . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Empresa</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../empresas.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form method="post" class="form-empresa">
                <div class="form-group">
                    <label>NIT</label>
                    <input type="text" value="<?php echo htmlspecialchars($empresa['NIT']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nom_empresa" value="<?php echo htmlspecialchars($empresa['nom_empresa']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($empresa['direccion']); ?>">
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($empresa['correo']); ?>">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado");
                        $sql_estado->execute();
                        $estados = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estados as $estado) {
                            $selected = ($estado['id_estado'] == $empresa['id_estado']) ? 'selected' : '';
                            echo "<option value='" . $estado['id_estado'] . "' $selected>" . $estado['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>