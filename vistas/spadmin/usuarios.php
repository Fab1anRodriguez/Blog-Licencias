<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// obtener todos los usuarios con sus roles
$sql = $con->prepare("
    SELECT usuarios.*, roles.nom_rol, estado.nom_estado, empresa.nom_empresa 
    FROM usuarios 
    LEFT JOIN roles ON usuarios.id_rol = roles.id_rol
    LEFT JOIN estado ON usuarios.id_estado = estado.id_estado 
    LEFT JOIN empresa ON usuarios.NIT = empresa.NIT
    ORDER BY usuarios.nom_usu
");
$sql->execute();
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
    <link rel="stylesheet" href="../../assets/css/licencias.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Usuarios</h1>
                    <p>Bienvenido Super Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nuevo Usuario</h2>
            <form action="usuario/crear_usuario.php" method="post" class="form-empresa">
                <div class="form-group">
                    <label>Documento de Identidad</label>
                    <input type="text" name="doc_usu" required placeholder="Ingrese el documento">
                </div>
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nom_usu" required placeholder="Ingrese el nombre">
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" required placeholder="ejemplo@correo.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="Ingrese la contraseña">
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="id_rol" required>
                        <option value="">Seleccione rol</option>
                        <?php
                        $sql_rol = $con->prepare("SELECT * FROM roles");
                        $sql_rol->execute();
                        $roles = $sql_rol->fetchAll(PDO::FETCH_ASSOC);
                        foreach($roles as $rol) {
                            echo "<option value='" . $rol['id_rol'] . "'>" . $rol['nom_rol'] . "</option>";
                        }
                        ?>
                    </select>
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
                <!-- Agregar el nuevo campo de empresa -->
                <div class="form-group">
                    <label>Empresa</label>
                    <select name="nit_empresa" required>
                        <option value="">Seleccione empresa</option>
                        <?php
                        $sql_empresa = $con->prepare("SELECT NIT, nom_empresa FROM empresa WHERE id_estado = 1");
                        $sql_empresa->execute();
                        $empresas = $sql_empresa->fetchAll(PDO::FETCH_ASSOC);
                        foreach($empresas as $empresa) {
                            echo "<option value='" . $empresa['NIT'] . "'>" . $empresa['nom_empresa'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="crear">Crear Usuario</button>
            </form>
        </div>

        <div class="usuarios">
            <h2>Usuarios Registrados</h2>
            <?php if ($usuarios): ?>
                <div class="grid-empresas">
                    <?php foreach ($usuarios as $usuario): ?>
                        <div class="empresa-card">
                            <div class="empresa-header">
                                <h3><?php echo htmlspecialchars($usuario['nom_usu']); ?></h3>
                                <span class="estado-licencia estado-<?php echo $usuario['id_estado']; ?>">
                                    <?php echo htmlspecialchars($usuario['nom_estado']); ?>
                                </span>
                            </div>
                            <div class="empresa-body">
                                <p><strong>Documento:</strong> <?php echo htmlspecialchars($usuario['doc_usu']); ?></p>
                                <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
                                <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['nom_rol']); ?></p>
                                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($usuario['nom_empresa']); ?></p>
                            </div>
                            <div class="empresa-actions">
                                <a href="usuario/editar_usuario.php?doc=<?php echo urlencode($usuario['doc_usu']); ?>" 
                                   class="btn-editar">Editar</a>
                                <a href="usuario/eliminar_usuario.php?doc=<?php echo urlencode($usuario['doc_usu']); ?>" 
                                   class="btn-eliminar" 
                                   onclick="return confirm('¿Está seguro de eliminar este usuario?')">Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-empresas">No hay usuarios registrados</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>