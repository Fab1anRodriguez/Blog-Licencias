<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
    header('Location: ../../../index.php');
    exit();
}

if (!isset($_GET['doc'])) {
    header('Location: ../usuarios.php');
    exit();
}

$doc_usu = $_GET['doc'];

// obtener datos del usuario asegurando que pertenezca a la empresa del admin
$sql = $con->prepare("
    SELECT usuarios.* 
    FROM usuarios 
    WHERE doc_usu = ? AND NIT = ? AND id_rol = 1");
$sql->execute([$doc_usu, $_SESSION['NIT']]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<script>alert('Usuario no encontrado');
    window.location='../usuarios.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_usu = trim($_POST['nom_usu']);
    $correo = trim($_POST['correo']);
    $id_estado = trim($_POST['id_estado']);
    $codigo_barras = trim($_POST['codigo_barras']);
    $password = trim($_POST['password']);
    
    if (empty($nom_usu) || empty($correo)) {
        echo "<script>alert('El nombre y correo son obligatorios');
        window.location='editar_usuario.php?doc=" . $doc_usu . "';</script>";
        exit();
    }
    
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = $con->prepare("
            UPDATE usuarios 
            SET nom_usu = ?, 
                correo = ?, 
                password = ?, 
                id_estado = ?,
                codigo_barras = ?
            WHERE doc_usu = ? AND NIT = ?");
        $params = [$nom_usu, $correo, $password_hash, $id_estado, $codigo_barras, $doc_usu, $_SESSION['NIT']];
    } else {
        $sql = $con->prepare("
            UPDATE usuarios 
            SET nom_usu = ?, 
                correo = ?, 
                id_estado = ?,
                codigo_barras = ?
            WHERE doc_usu = ? AND NIT = ?");
        $params = [$nom_usu, $correo, $id_estado, $codigo_barras, $doc_usu, $_SESSION['NIT']];
    }
    
    if ($sql->execute($params)) {
        echo "<script>alert('Usuario actualizado exitosamente');
        window.location='../usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el usuario');
        window.location='editar_usuario.php?doc=" . $doc_usu . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Usuario</h1>
                </div>
                <div class="admin-actions">
                    <a href="../usuarios.php" class="btn-volver">Volver</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form method="post" class="form-empresa">
                <div class="form-group">
                    <label>Documento</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['doc_usu']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nom_usu" value="<?php echo htmlspecialchars($usuario['nom_usu']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado where id_estado = 1 OR id_estado = 2 order by id_estado asc");
                        $sql_estado->execute();
                        $estados = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estados as $estado) {
                            $selected = ($estado['id_estado'] == $usuario['id_estado']) ? 'selected' : '';
                            echo "<option value='" . $estado['id_estado'] . "' " . $selected . ">" . $estado['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Código de Barras</label>
                    <input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($usuario['codigo_barras']); ?>">
                    <?php if (!empty($usuario['codigo_barras'])) : 
                        $barcodeData = urlencode($usuario['codigo_barras']);
                        $externalBarcodeUrl = "https://barcode.tec-it.com/barcode.ashx?data={$barcodeData}&code=Code128&dpi=96";
                    ?>
                        <div class="barcode-container">
                            <img src="<?php echo $externalBarcodeUrl; ?>" alt="Código de barras">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>