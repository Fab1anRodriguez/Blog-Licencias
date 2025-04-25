<?php
session_start();
require_once('../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// obtener los posts del usuario
$doc_usu = $_SESSION['user_id'];
$sql = $con->prepare("
    SELECT post.*, categoria.nom_categoria 
    FROM post 
    LEFT JOIN categoria ON post.id_categoria = categoria.id_categoria 
    WHERE post.doc_usu = ? 
    ORDER BY post.id_post DESC
");
$sql->execute([$doc_usu]);
$posts = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Blog</title>
    <link rel="stylesheet" href="../../assets/css/blog.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Mi Blog</h1>
            <p>Bienvenido, <?php echo $_SESSION['nom_usu']; ?></p>
            <?php 
            //consultamos el nombre de la empresa a la que pertenece el usuario
            $sql_empresa = $con->prepare("
                SELECT usuarios.*, empresa.nom_empresa 
                FROM usuarios 
                INNER JOIN empresa ON usuarios.NIT = empresa.NIT 
                WHERE usuarios.doc_usu = ?");
            $sql_empresa->execute([$doc_usu]);
            $empresa = $sql_empresa->fetch(PDO::FETCH_ASSOC);
            ?>
            <p>Empresa: <?php echo htmlspecialchars($empresa['nom_empresa']); ?></p>
            <a href="../../includes/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesión</a>
        </header>

        <div class="nuevo-post">
            <h2>Crear Nueva Publicacion</h2>
            <form action="crear_post.php" class="tuki" method="post">
                <div class="form-group">
                    <input type="text" name="titulo" placeholder="Titulo de la publicacion" required>
                </div>
                <div class="form-group">
                    <select name="id_categoria" class="categoria" id="id_categoria" required>
                        <option value="">Seleccione una categoria</option>
                        <?php
                        // consulta para obtener todas las categorias
                        $sql_cat = $con->prepare("SELECT * FROM categoria ORDER BY nom_categoria asc");
                        $sql_cat->execute();
                        $categorias = $sql_cat->fetchAll(PDO::FETCH_ASSOC);
                        
                        // mostrar cada categoria en el select
                        foreach($categorias as $cat) {
                            echo "<option value=" . $cat['id_categoria'] . ">" .$cat['nom_categoria'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="contenido" placeholder="Contenido de la publicacion" required></textarea>
                </div>
                <button type="submit" name="publicar">Publicar</button>
            </form>
        </div>

        <div class="posts">
            <?php if ($posts):
                 foreach ($posts as $post): ?>
                    <article class="post">
                        <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                        <span class="categoria"><?php echo $post['nom_categoria']; ?></span>
                        <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-posts">no has creado ninguna publicacion</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>