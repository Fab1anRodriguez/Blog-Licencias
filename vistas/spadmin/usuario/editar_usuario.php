<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (!isset($_GET['doc'])) {
    header('Location: ../usuarios.php');
    exit();
}

$doc_usu = $_GET['doc'];

// obtener datos del usuario con la empresa
$sql = $con->prepare("
    SELECT usuarios.*, empresa.nom_empresa 
    FROM usuarios 
    LEFT JOIN empresa ON usuarios.NIT = empresa.NIT 
    WHERE usuarios.doc_usu = ?");
$sql->execute([$doc_usu]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<script>alert('Usuario no encontrado');
    window.location='../usuarios.php';</script>";
    exit();
}

if (isset($_POST['editar'])) {
    $nom_usu = trim($_POST['nom_usu']);
    $correo = trim($_POST['correo']);
    $id_rol = trim($_POST['id_rol']);
    $id_estado = trim($_POST['id_estado']);
    $password = trim($_POST['password']);
    $codigo_barras = trim($_POST['codigo_barras']);
    
    if (empty($nom_usu) || empty($correo)) {
        echo "<script>alert('El nombre y correo son obligatorios');
        window.location='editar_usuario.php?doc=" . $doc_usu . "';</script>";
        exit();
    }
    
    // Verificar si el correo existe para otro usuario
    $check = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ? AND doc_usu != ?");
    $check->execute([$correo, $doc_usu]);
    if ($check->fetchColumn() > 0) {
        echo "<script>alert('Ya existe otro usuario con este correo');
        window.location='editar_usuario.php?doc=" . $doc_usu . "';</script>";
        exit();
    }
    
    if (!empty($password)) {
        // Si se proporciona una nueva contraseña, actualizarla
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = $con->prepare("
            UPDATE usuarios 
            SET nom_usu = ?, 
                correo = ?, 
                password = ?, 
                id_rol = ?, 
                id_estado = ?, 
                NIT = ?,
                codigo_barras = ?
            WHERE doc_usu = ?");
        $params = [$nom_usu, $correo, $password_hash, $id_rol, $id_estado, $_POST['nit_empresa'], $codigo_barras, $doc_usu];
    } else {
        // Si no hay nueva contraseña, mantener la actual
        $sql = $con->prepare("
            UPDATE usuarios 
            SET nom_usu = ?, 
                correo = ?, 
                id_rol = ?, 
                id_estado = ?, 
                NIT = ?,
                codigo_barras = ?
            WHERE doc_usu = ?");
        $params = [$nom_usu, $correo, $id_rol, $id_estado, $_POST['nit_empresa'], $codigo_barras,$doc_usu];
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
<body onload="form-empresa.doc_usu.focus()">
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Usuario</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../usuarios.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form method="post" name="form-empresa" class="form-empresa">
                <div class="form-group">
                    <label>Documento</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['doc_usu']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" tabindex="0" name="nom_usu" value="<?php echo htmlspecialchars($usuario['nom_usu']); ?>" required>
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
                    <label>Rol</label>
                    <select name="id_rol" required>
                        <?php
                        $sql_rol = $con->prepare("SELECT * FROM roles");
                        $sql_rol->execute();
                        $roles = $sql_rol->fetchAll(PDO::FETCH_ASSOC);
                        foreach($roles as $rol) {
                            $selected = ($rol['id_rol'] == $usuario['id_rol']) ? 'selected' : '';
                            echo "<option value='" . $rol['id_rol'] . "' $selected>" . $rol['nom_rol'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado");
                        $sql_estado->execute();
                        $estados = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estados as $estado) {
                            $selected = ($estado['id_estado'] == $usuario['id_estado']) ? 'selected' : '';
                            echo "<option value='" . $estado['id_estado'] . "' $selected>" . $estado['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Empresa</label>
                    <select name="nit_empresa" required>
                        <option value="">Seleccione empresa</option>
                        <?php
                        $sql_empresa = $con->prepare("SELECT NIT, nom_empresa FROM empresa WHERE id_estado = 1");
                        $sql_empresa->execute();
                        $empresas = $sql_empresa->fetchAll(PDO::FETCH_ASSOC);
                        foreach($empresas as $empresa) {
                            $selected = ($empresa['NIT'] == $usuario['NIT']) ? 'selected' : '';
                            echo "<option value='" . $empresa['NIT'] . "' $selected>" . htmlspecialchars($empresa['nom_empresa']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Código de barras</label>
                    <input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($usuario['codigo_barras']); ?>" required>
                    
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