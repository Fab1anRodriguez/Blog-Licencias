<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
    header('Location: ../../index.php');
    exit();
}

// obtener usuarios de la empresa actual
$sql = $con->prepare("
    SELECT usuarios.*, roles.nom_rol, estado.nom_estado, empresa.nom_empresa 
    FROM usuarios 
    LEFT JOIN roles ON usuarios.id_rol = roles.id_rol
    LEFT JOIN estado ON usuarios.id_estado = estado.id_estado 
    LEFT JOIN empresa ON usuarios.NIT = empresa.NIT
    WHERE usuarios.NIT = ? AND usuarios.id_rol = 1
    ORDER BY usuarios.nom_usu
");
$sql->execute([$_SESSION['NIT']]);
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
<body onload="formuusu.doc_usu.focus()">
    <div class="container">
        <header>
            <div class="header-container">
                <div>
                    <h1>Gestión de Usuarios</h1>
                    <p>Bienvenido Administrador, <?php echo $_SESSION['nom_usu']; ?></p>
                </div>
                <div class="admin-actions">
                    <a href="index.php" class="btn-volver">Volver</a>
                    <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesion</a>
                </div>
            </div>
        </header>

        <div class="nuevo-post">
            <h2>Registrar Nuevo Usuario</h2>
            <form action="usuario/crear_usuario.php" name="formuusu" method="post" class="form-empresa">
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
                <div class="form-group">
                    <label>Código de Barras</label>
                    <input type="text" name="codigo_barras" placeholder="Escanee el código de barras del usuario">
                </div>
                <input type="hidden" name="id_rol" value="3">
                <input type="hidden" name="NIT" value="<?php echo $_SESSION['NIT']; ?>">
                <button type="submit" name="crear">Crear Usuario</button>
            </form>
        </div>

        <div class="usuarios">
            <h2>Usuarios Registrados</h2>

            <div class="buscador">
                <form method="GET" class="form-buscar">
                    <div class="form-group">
                        <input type="text" 
                               id="buscarUsuario" 
                               autocomplete="off"
                               placeholder="Buscar por documento o código de barras">
                    </div>
                </form>
            </div>

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
                                <p><strong>Código de Barras:</strong> <?php echo htmlspecialchars($usuario['codigo_barras']); ?></p>
                                
                                <?php if (!empty($usuario['codigo_barras'])) : 
                                    $barcodeData = urlencode($usuario['codigo_barras']);
                                    $externalBarcodeUrl = "https://barcode.tec-it.com/barcode.ashx?data={$barcodeData}&code=Code128&dpi=96";
                                ?>
                                    <div class="barcode-container">
                                        <img src="<?php echo $externalBarcodeUrl; ?>" alt="Código de barras">
                                    </div>
                                <?php endif; ?>
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
    
    <script>
    function buscarUsuarios() {
        let input = document.getElementById('buscarUsuario');
        let filtro = input.value.toLowerCase().trim();
        let cards = document.getElementsByClassName('empresa-card');

        for (let i = 0; i < cards.length; i++) {
            let card = cards[i];
            
            let documentoCompleto = card.querySelector('p:nth-child(1)').textContent;
            let codigoBarrasCompleto = card.querySelector('p:nth-child(5)').textContent;
            
            let documento = documentoCompleto.split(':')[1].trim().toLowerCase();
            let codigoBarras = codigoBarrasCompleto.split(':')[1].trim().toLowerCase();

            if (documento.includes(filtro) || codigoBarras.includes(filtro)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        }
    }

    document.querySelector('.form-buscar').addEventListener('submit', function(e) {
        e.preventDefault();
    });

    document.getElementById('buscarUsuario').addEventListener('keyup', buscarUsuarios);
    </script>
</body>
</html>