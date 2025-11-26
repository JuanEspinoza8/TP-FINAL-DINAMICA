<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$abmUsuario = new abmUsuario();

$resultado = $abmUsuario->registrarUsuario($datos);

if($resultado['exito']){
    header('Location: ../../Vista/login.php?msg=registrado');
} else {
    
    header('Location: ../../Vista/registro.php?error=1');
}
exit;
?>