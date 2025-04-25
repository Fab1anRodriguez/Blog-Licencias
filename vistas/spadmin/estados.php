<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// obtener todos los estados
$sql = $con->prepare("SELECT * FROM estado ORDER BY nom_estado");
$sql->execute();
$estados = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Estados</h1>
                    <p>Bienvenido Super Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nuevo Estado</h2>
            <form action="estado/crear_estado.php" method="post" class="form-empresa">
                <div class="form-group">
                    <label>Nombre del Estado</label>
                    <input type="text" name="nom_estado" required placeholder="Ingrese el nombre del estado">
                </div>
                <button type="submit" name="crear">Crear Estado</button>
            </form>
        </div>

        <div class="estados">
            <h2>Estados Registrados</h2>
            <?php if ($estados): ?>
                <div class="grid-empresas">
                    <?php foreach ($estados as $estado): ?>
                        <div class="empresa-card">
                            <div class="empresa-header">
                                <h3><?php echo htmlspecialchars($estado['nom_estado']); ?></h3>
                            </div>
                            <div class="empresa-actions">
                                <a href="estado/editar_estado.php?id=<?php echo urlencode($estado['id_estado']); ?>" 
                                   class="btn-editar">Editar</a>
                                <a href="estado/eliminar_estado.php?id=<?php echo urlencode($estado['id_estado']); ?>" 
                                   class="btn-eliminar" 
                                   onclick="return confirm('¿Está seguro de eliminar este estado?')">Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-empresas">No hay estados registrados</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>