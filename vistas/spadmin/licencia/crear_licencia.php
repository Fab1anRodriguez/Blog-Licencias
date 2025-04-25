<?php
session_start();
require_once('../../../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// verificar si el usuario esta logueado y es superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

if (isset($_POST['crear'])) {
    // funcion para generar clave unica de licencia
    function generar_clave_licencia() {
        // definimos los caracteres permitidos: numeros y letras mayusculas
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        // variable para almacenar la clave final
        $clave = '';
        
        // generamos 4 bloques
        for($bloque = 0; $bloque < 6; $bloque++) {
            // cada bloque tiene 4 caracteres
            for($i = 0; $i < 4; $i++) {
                // selecciona un caracter aleatorio de la cadena $caracteres
                $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            // agrega un guion entre bloques, excepto al final
            if($bloque < 5) $clave .= '-';
        }
        return $clave;
    }

    // obtener datos del formulario
    $nit_empresa = $_POST['nit_empresa'];
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_estado = $_POST['id_estado'];
    $id_tipolicencia = $_POST['id_tipolicencia'];
    
    // generar clave unica
    $clave = generar_clave_licencia();
    
    // verificar que la clave no exista
    do {
        $verificar = $con->prepare("SELECT clave FROM licencia WHERE clave = ?");
        $verificar->execute([$clave]);

        //se verifica si la consulta devolvio alguna fila, lo que significa que encontro una clave igual
        if($verificar->rowCount() > 0) {
            $clave = generar_clave_licencia(); // generar nueva clave
        }
    } while($verificar->rowCount() > 0);
    
    // validar fechas
    if (strtotime($fecha_fin) <= strtotime($fecha_ini)) {
        echo "<script>alert('La fecha de fin debe ser posterior a la fecha de inicio');
        window.location='../index.php';</script>";
        exit();
    }
    
    // insertar la licencia
    $sql = $con->prepare("INSERT INTO licencia (clave, nit_empresa, fecha_ini, fecha_fin, id_estado, id_tipolicencia) 
                         VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($sql->execute([$clave, $nit_empresa, $fecha_ini, $fecha_fin, $id_estado, $id_tipolicencia])) {
        echo "<script>
            alert('Licencia generada exitosamente, Clave: " . $clave . "');
            window.location='../index.php';
        </script>";
    } else {
        echo "<script>alert('Error al generar la licencia');
        window.location='../index.php';</script>";
    }
}
?>