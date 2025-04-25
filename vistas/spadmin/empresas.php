<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// obtener todas las empresas
$sql = $con->prepare("
    SELECT empresa.*, estado.nom_estado 
    FROM empresa 
    LEFT JOIN estado ON empresa.id_estado = estado.id_estado 
    ORDER BY empresa.nom_empresa
");
$sql->execute();
$empresas = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empresas</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Empresas</h1>
                    <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nueva Empresa</h2>
            <form action="empresa/crear_empresa.php" method="post" class="form-empresa">
                <div class="form-group">
                    <label>NIT</label>
                    <input type="text" name="nit" required placeholder="Ingrese el NIT">
                </div>
                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nom_empresa" required placeholder="Ingrese el nombre de la empresa">
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" placeholder="Ingrese la direccion">
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" placeholder="ejemplo@empresa.com">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <option value="">Seleccione estado</option>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado");
                        $sql_estado->execute();
                        $estados = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estados as $estado) {
                            echo "<option value='" . $estado['id_estado'] . "'>" . $estado['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="crear">Crear Empresa</button>
            </form>
        </div>

        <div class="empresas">
            <h2>Empresas Registradas</h2>
            <?php if ($empresas): ?>
                <div class="grid-empresas">
                    <?php foreach ($empresas as $empresa): ?>
                        <div class="empresa-card">
                            <div class="empresa-header">
                                <h3><?php echo htmlspecialchars($empresa['nom_empresa']); ?></h3>
                                <span class="estado-licencia estado-<?php echo $empresa['id_estado']; ?>">
                                <?php echo htmlspecialchars($empresa['nom_estado']); ?>
                            </span>
                            </div>
                            <div class="empresa-body">
                                <p><strong>NIT:</strong> <?php echo htmlspecialchars($empresa['NIT']); ?></p>
                                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion'] ?: 'No especificada'); ?></p>
                                <p><strong>Correo:</strong> <?php echo htmlspecialchars($empresa['correo'] ?: 'No especificado'); ?></p>
                            </div>
                            <div class="empresa-actions">
                                <a href="empresa/editar_empresa.php?nit=<?php echo urlencode($empresa['NIT']); ?>" class="btn-editar">Editar</a>
                                <a href="empresa/eliminar_empresa.php?nit=<?php echo urlencode($empresa['NIT']); ?>" 
                                   class="btn-eliminar" 
                                   onclick="return confirm('¿Está seguro de eliminar esta empresa?')">Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-empresas">No hay empresas registradas</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>