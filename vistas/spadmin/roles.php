<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// obtener todos los roles
$sql = $con->prepare("SELECT * FROM roles ORDER BY nom_rol");
$sql->execute();
$roles = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Roles</h1>
                    <p>Bienvenido Super Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nuevo Rol</h2>
            <form action="rol/crear_rol.php" method="post" class="form-empresa">
                <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" name="nom_rol" required placeholder="Ingrese el nombre del rol">
                </div>
                <button type="submit" name="crear">Crear Rol</button>
            </form>
        </div>

        <div class="roles">
            <h2>Roles Registrados</h2>
            <?php if ($roles): ?>
                <div class="grid-empresas">
                    <?php foreach ($roles as $rol): ?>
                        <div class="empresa-card">
                            <div class="empresa-header">
                                <h3><?php echo htmlspecialchars($rol['nom_rol']); ?></h3>
                            </div>
                            <div class="empresa-actions">
                                <a href="rol/editar_rol.php?id=<?php echo urlencode($rol['id_rol']); ?>" 
                                   class="btn-editar">Editar</a>
                                <a href="rol/eliminar_rol.php?id=<?php echo urlencode($rol['id_rol']); ?>" 
                                   class="btn-eliminar" 
                                   onclick="return confirm('¿Está seguro de eliminar este rol?')">Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-empresas">No hay roles registrados</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>