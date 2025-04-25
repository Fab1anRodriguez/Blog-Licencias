<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2){
    header('Location: ../../index.php');
    exit();
}

// Obtener información de la empresa y su licencia
$sql = $con->prepare("
    SELECT e.*, l.fecha_fin, l.fecha_ini, l.id_estado as estado_licencia,
           DATEDIFF(l.fecha_fin, CURRENT_DATE()) as dias_restantes,
           el.nom_estado as estado_empresa,
           el2.nom_estado as estado_licencia_nombre
    FROM empresa e
    LEFT JOIN licencia l ON e.NIT = l.nit_empresa
    LEFT JOIN estado el ON e.id_estado = el.id_estado
    LEFT JOIN estado el2 ON l.id_estado = el2.id_estado
    WHERE e.NIT = ? AND l.id_estado = 1
    ORDER BY l.fecha_fin DESC LIMIT 1
");
$sql->execute([$_SESSION['NIT']]);
$empresa_info = $sql->fetch(PDO::FETCH_ASSOC);

// Obtener cantidad de usuarios de la empresa
$sql_usuarios = $con->prepare("
    SELECT COUNT(*) as total_usuarios 
    FROM usuarios 
    WHERE NIT = ? AND id_rol = 3
");
$sql_usuarios->execute([$_SESSION['NIT']]);
$total_usuarios = $sql_usuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
    <header>
            <div class="header-container">
                <div>
                    <h1>Gestion de Licencias</h1>
                    <p>Bienvenido Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
            <nav class="nav-actions">
                <a href="usuarios.php" class="btn-nav">Usuarios</a>
            </nav>
        </header>

        <div class="empresa-info">
            <div class="empresa-card">
                <div class="empresa-header">
                    <h3><?php echo htmlspecialchars($empresa_info['nom_empresa']); ?></h3>
                    <span class="estado-licencia estado-<?php echo $empresa_info['estado_licencia']; ?>">
                        <?php echo htmlspecialchars($empresa_info['estado_licencia_nombre']); ?>
                    </span>
                </div>
                <div class="empresa-body">
                    <p><strong>Estado de la empresa:</strong> 
                        <span class="estado-empresa">
                            <?php echo htmlspecialchars($empresa_info['estado_empresa']); ?>
                        </span>
                    </p>
                    <p><strong>NIT:</strong> <?php echo htmlspecialchars($empresa_info['NIT']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empresa_info['direccion']); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($empresa_info['correo']); ?></p>
                    <p><strong>Total Usuarios:</strong> <?php echo $total_usuarios; ?></p>
                    <p><strong>Días restantes de licencia:</strong> 
                        <?php echo $empresa_info['dias_restantes']; ?> días
                    </p>
                    <p><strong>Fecha de vencimiento:</strong> 
                        <?php echo date('d/m/Y', strtotime($empresa_info['fecha_fin'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>