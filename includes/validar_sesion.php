<?php
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (isset($_POST['ingresar'])) {
    $doc_usu = $_POST['doc_usu'];
    $password = $_POST['password'];

    if (empty($doc_usu) || empty($password)) {
        echo "<script>alert('Existen datos vacíos')</script>";
        echo "<script>window.location = '../index.php'</script>";
    } else {
        $password_descr = htmlentities(addslashes($password));
        $sqlUser = $con->prepare("SELECT * FROM usuarios WHERE doc_usu = ?");
        $sqlUser->execute([$doc_usu]);
        $u = $sqlUser->fetch();

        if ($u && password_verify($password_descr, $u["password"])) {
            $_SESSION['user_id'] = $u['doc_usu'];
            $_SESSION['nom_usu'] = $u['nom_usu']; 
            $_SESSION['estado'] = $u['id_estado'];
            $_SESSION['rol'] = $u['id_rol'];
            $_SESSION['NIT'] = $u['NIT'];

            // Si es superadmin (rol 3) o el NIT es NULL, no validar empresa ni licencia
            if ($_SESSION['rol'] != 3 && $_SESSION['NIT'] !== null) {
                // se valida si la empresa del usuario esta activa
                $sql_empresa = $con->prepare("SELECT * FROM empresa WHERE NIT = ? AND id_estado = 1");
                $sql_empresa->execute([$_SESSION['NIT']]);
                $empresa = $sql_empresa->fetch(PDO::FETCH_ASSOC);
                
                if (!$empresa) {
                    echo "<script>alert('La empresa no esta activa')</script>";
                    echo "<script>window.location = '../index.php'</script>";
                    exit();
                }

                // se valida si la empresa del usuario tiene una licencia activa
                $sql_licencia = $con->prepare("
                    SELECT l.* 
                    FROM licencia l 
                    WHERE l.nit_empresa = ? 
                    AND l.id_estado = 1 
                    AND l.fecha_fin >= CURRENT_DATE()");
                $sql_licencia->execute([$_SESSION['NIT']]);
                $licencia = $sql_licencia->fetch(PDO::FETCH_ASSOC);

                if (!$licencia) {
                    echo "<script>alert('La empresa no tiene una licencia activa')</script>";
                    echo "<script>window.location = '../index.php'</script>";
                    exit();
                }
            }

            if ($_SESSION['rol'] == 1 && $_SESSION['estado'] == 1) {
                echo "<script>window.location = '../vistas/usuario/index.php'</script>";
            } 
            elseif ($_SESSION['rol'] == 2 && $_SESSION['estado'] == 1) {
                echo "<script>window.location = '../vistas/admin/index.php'</script>";
            }
            elseif ($_SESSION['rol'] == 3 && $_SESSION['estado'] == 1) {
                echo "<script>window.location = '../vistas/spadmin/index.php'</script>";
            }
            else {
                echo "<script>alert('Usuario inactivo, hable con el admin')</script>";
                echo "<script>window.location = '../index.php'</script>";
            }
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos')</script>";
            echo "<script>window.location = '../index.php'</script>";
        }
    }
}
?>