<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$abmUsuario = new abmUsuario();

$resultado = $abmUsuario->registrarUsuario($datos);

if($resultado['exito']){
    header('Location: ../../login.php?msg=registrado');
} else {
    
    header('Location: ../../registro.php?error=1');
}
exit;
?>