<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// obtener todos los tipos de licencia
$sql = $con->prepare("SELECT * FROM tipo_licencia ORDER BY nom_tipolicencia");
$sql->execute();
$tipos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Licencia</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Tipos de Licencia</h1>
                    <p>Bienvenido Super Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-tipo">
            <h2>Registrar Nuevo Tipo de Licencia</h2>
            <form action="tipo_licencia/crear_tipo_licencia.php" method="post" class="form-tipo">
                <div class="form-group">
                    <label>Nombre del Tipo de Licencia</label>
                    <input type="text" name="nom_tipolicencia" required>
                </div>
                <button type="submit" name="crear">Crear Tipo de Licencia</button>
            </form>
        </div>

        <div class="tipos-licencia">
            <h2>Tipos de Licencia Registrados</h2>
            <?php if ($tipos): ?>
                <?php foreach ($tipos as $tipo): ?>
                    <div class="tipo-card">
                        <div class="tipo-info">
                            <h3><?php echo htmlspecialchars($tipo['nom_tipolicencia']); ?></h3>
                        </div>
                        <div class="tipo-actions">
                            <a href="tipo_licencia/editar_tipo_licencia.php?id=<?php echo $tipo['id_tipolicencia']; ?>" 
                               class="btn-editar">Editar</a>
                            <a href="tipo_licencia/eliminar_tipo_licencia.php?id=<?php echo $tipo['id_tipolicencia']; ?>" 
                               class="btn-eliminar"
                               onclick="return confirm('¿Está seguro de eliminar este tipo de licencia?')">
                                Eliminar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-tipos">No hay tipos de licencia registrados</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>