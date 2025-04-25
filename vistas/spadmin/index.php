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

// obtener todas las licencias
$sql = $con->prepare("
    SELECT licencia.*, estado.nom_estado, tipo_licencia.nom_tipolicencia, empresa.nom_empresa 
    FROM licencia 
    LEFT JOIN estado ON licencia.id_estado = estado.id_estado
    LEFT JOIN tipo_licencia ON licencia.id_tipolicencia = tipo_licencia.id_tipolicencia
    LEFT JOIN empresa ON licencia.nit_empresa = empresa.NIT
    ORDER BY licencia.fecha_ini DESC
");
$sql->execute();
$licencias = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Licencias</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestion de Licencias</h1>
                    <p>Bienvenido Super Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
            <nav class="nav-actions">
                <a href="tipos_licencia.php" class="btn-nav">Tipos de Licencia</a>
                <a href="empresas.php" class="btn-nav">Empresas</a>
                <a href="usuarios.php" class="btn-nav">Usuarios</a>
                <a href="estados.php" class="btn-nav">Estados</a>
                <a href="roles.php" class="btn-nav">Roles</a>
            </nav>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nueva Licencia</h2>
            <form action="licencia/crear_licencia.php" method="post" class="form-licencia">
                <div class="form-group">
                    <label>Empresa</label>
                    <select name="nit_empresa" required>
                        <option value="">Seleccione empresa</option>
                        <?php
                        $sql_empresa = $con->prepare("SELECT * FROM empresa ORDER BY nom_empresa");
                        $sql_empresa->execute();
                        $empresas = $sql_empresa->fetchAll(PDO::FETCH_ASSOC);
                        foreach($empresas as $empresa) {
                            echo "<option value='" . $empresa['NIT'] . "'>" . $empresa['nom_empresa'] . " - NIT: " . $empresa['NIT'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_ini" required>
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="id_estado" required>
                        <option value="">Seleccione estado</option>
                        <?php
                        $sql_estado = $con->prepare("SELECT * FROM estado");
                        $sql_estado->execute();
                        $estado = $sql_estado->fetchAll(PDO::FETCH_ASSOC);
                        foreach($estado as $estados) {
                            echo "<option value='" . $estados['id_estado'] . "'>" . $estados['nom_estado'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipo de Licencia</label>
                    <select name="id_tipolicencia" required>
                        <option value="">Seleccione tipo</option>
                        <?php
                        $sql_tipo = $con->prepare("SELECT * FROM tipo_licencia");
                        $sql_tipo->execute();
                        $tipo = $sql_tipo->fetchAll(PDO::FETCH_ASSOC);
                        foreach($tipo as $tipos) {
                            echo "<option value='" . $tipos['id_tipolicencia'] . "'>" . $tipos['nom_tipolicencia'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="crear">Generar Licencia</button>
            </form>
        </div>

        <div class="licencias">
            <h2>Licencias Registradas</h2>
            <?php if ($licencias): ?>
                <?php foreach ($licencias as $licencia): ?>
                    <div class="licencia-card">
                        <div class="licencia-header">
                            <h3>Licencia: <?php echo htmlspecialchars($licencia['clave']); ?></h3>
                            <span class="estado-licencia estado-<?php echo $licencia['id_estado']; ?>">
                                <?php echo htmlspecialchars($licencia['nom_estado']); ?>
                            </span>
                        </div>
                        <div class="licencia-body">
                            <p><strong>NIT:</strong> <?php echo $licencia['nit_empresa']; ?></p>
                            <p><strong>Empresa:</strong> <?php echo $licencia['nom_empresa']; ?></p>
                            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($licencia['nom_tipolicencia']); ?></p>
                            <p><strong>Inicio:</strong> <?php echo date('d/m/Y', strtotime($licencia['fecha_ini'])); ?></p>
                            <p><strong>Fin:</strong> <?php echo date('d/m/Y', strtotime($licencia['fecha_fin'])); ?></p>
                        </div>
                        <div class="licencia-actions">
                            <a href="licencia/editar_licencia.php?clave=<?php echo urlencode($licencia['clave']); ?>" class="btn-editar">Editar</a>
                            <a href="licencia/eliminar_licencia.php?clave=<?php echo urlencode($licencia['clave']); ?>" 
                               class="btn-eliminar" 
                               onclick="return confirm('¿Esta seguro de eliminar esta licencia?')">
                                Eliminar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-licencias">No hay licencias registradas</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>