<?php
include_once("../../configuracion.php");
$session = new Session();
$salida = [];

if($session->activa() && $session->getRolActivo() == 1){
    $abmMenu = new abmMenu();
    $salida = $abmMenu->listarMenuesConRoles();
}
echo json_encode($salida);
?>