<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../../index.php');
    exit();
}

// obtener la clave de la licencia a editar
if (!isset($_GET['clave'])) {
    header('Location: ../index.php');
    exit();
}

$clave = $_GET['clave'];

// obtener los datos de la licencia
$sql = $con->prepare("
    SELECT licencia.*, estado.nom_estado, tipo_licencia.nom_tipolicencia, empresa.nom_empresa 
    FROM licencia 
    LEFT JOIN estado ON licencia.id_estado = estado.id_estado
    LEFT JOIN tipo_licencia ON licencia.id_tipolicencia = tipo_licencia.id_tipolicencia
    LEFT JOIN empresa ON licencia.nit_empresa = empresa.NIT
    WHERE licencia.clave = ?
");
$sql->execute([$clave]);
$licencia = $sql->fetch(PDO::FETCH_ASSOC);

if (!$licencia) {
    echo "<script>alert('Licencia no encontrada');
    window.location='../index.php';</script>";
    exit();
}

// procesar el formulario de edicion
if (isset($_POST['editar'])) {
    $nit_empresa = $_POST['nit_empresa'];
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_estado = $_POST['id_estado'];
    $id_tipolicencia = $_POST['id_tipolicencia'];
    
    // validar fechas
    if (strtotime($fecha_fin) <= strtotime($fecha_ini)) {
        echo "<script>alert('La fecha de fin debe ser posterior a la fecha de inicio');
        window.location='editar_licencia.php?clave=" . urlencode($clave) . "';</script>";
        exit();
    }
    
    // actualizar la licencia
    $sql = $con->prepare("
        UPDATE licencia 
        SET nit_empresa = ?, fecha_ini = ?, fecha_fin = ?, id_estado = ?, id_tipolicencia = ? 
        WHERE clave = ?
    ");
    
    if ($sql->execute([$nit_empresa, $fecha_ini, $fecha_fin, $id_estado, $id_tipolicencia, $clave])) {
        echo "<script>alert('Licencia actualizada exitosamente');
        window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la licencia');
        window.location='editar_licencia.php?clave=" . urlencode($clave) . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Licencia</title>
    <link rel="stylesheet" href="../../../assets/css/blog.css">
    <link rel="stylesheet" href="../../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Editar Licencia</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../../index.php" class="btn-volver">Volver</a>
                    <a href="../../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="form-container">
            <h2>Editar Licencia: <?php echo htmlspecialchars($licencia['clave']); ?></h2>
            <form action="" method="post" class="form-licencia">
                <div class="form-group">
                    <label>Empresa</label>
                    <select name="nit_empresa" required>
                        <?php
                        $sql_empresa = $con->prepare("SELECT * FROM empresa ORDER BY nom_empresa");
                        $sql_empresa->execute();
                        $empresas = $sql_empresa->fetchAll(PDO::FETCH_ASSOC);
                        foreach($empresas as $empresa) {
                            $selected = ($empresa['NIT'] == $licencia['nit_empresa']) ? 'selected' : '';
                            echo "<option value='" . $empresa['NIT'] . "' " . $selected . ">" . 
                                 $empresa['nom_empresa'] . " - NIT: " . $empresa['NIT'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_ini" value="<?php echo $licencia['fecha_ini']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="<?php echo $licencia['fecha_fin']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado");
                        $sql_estado->execute();
                        $estados = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estados as $estado) {
                            $selected = ($estado['id_estado'] == $licencia['id_estado']) ? 'selected' : '';
                            echo "<option value='" . $estado['id_estado'] . "' " . $selected . ">" . 
                                 $estado['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipo de Licencia</label>
                    <select name="id_tipolicencia" required>
                        <?php
                        $sql_tipo = $con->prepare("SELECT * FROM tipo_licencia");
                        $sql_tipo->execute();
                        $tipos = $sql_tipo->fetchAll(PDO::FETCH_ASSOC);
                        foreach($tipos as $tipo) {
                            $selected = ($tipo['id_tipolicencia'] == $licencia['id_tipolicencia']) ? 'selected' : '';
                            echo "<option value='" . $tipo['id_tipolicencia'] . "' " . $selected . ">" . 
                                 $tipo['nom_tipolicencia'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" name="editar">Guardar Cambios</button>
                    <a href="../index.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>