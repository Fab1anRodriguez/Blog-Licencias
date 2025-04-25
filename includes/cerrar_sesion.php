<?php 
session_start(); 
unset($_SESSION['user_id']); 
unset($_SESSION['nom_usu']); 
unset($_SESSION['rol']); 
session_destroy(); 
session_write_close(); 

header("Location: ../index.php"); 
?>