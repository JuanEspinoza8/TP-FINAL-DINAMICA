<?php
include_once("../../../configuracion.php");
$abmUsuario = new abmUsuario();


$salida = $abmUsuario->listarUsuariosConRoles();

echo json_encode($salida);
?>