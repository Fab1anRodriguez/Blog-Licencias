<?php
session_start();
require_once('conex/conex.php');
$conex = new Database;
$con = $conex->conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="caja">
        <div class="formu">
            <form action="includes/validar_sesion.php" class="formulario" method="post" id="formulario_login">
                <h1>INICIAR SESIÓN</h1>

                <div class="formulario__grupo" id="grupo__doc_usu">
                    <div class="formulario__grupo-input">
                        <input type="text" name="doc_usu" class="doc_usu" placeholder="Documento de Identidad" id="doc_usu">
                        <i class="bi formulario__validacion-estado"></i>
                    </div>
                    <p class="formulario__input-error">Ingrese su documento de identidad.</p>
                </div>

                <div class="formulario__grupo" id="grupo__password">
                    <div class="formulario__grupo-input">
                        <input type="password" name="password" class="password" placeholder="Contraseña" id="password">
                        <i class="bi formulario__validacion-estado"></i>
                    </div>
                    <p class="formulario__input-error">Ingrese su contraseña.</p>
                </div>

                <button type="submit" name="ingresar" id="ingresar" href="includes/validar_sesion.php"><strong>INGRESAR</strong></button>

                <p class="form-footer">¿No tienes una cuenta? 
                    <a href="index.php" class="login-link"> Registrate aqui</a></p>
            </form>
        </div>
    </div>

</body>
</html>