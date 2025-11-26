<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$res = ['exito'=>false, 'msg'=>'Permisos insuficientes'];

if($session->activa() && $session->getRolActivo() == 1){
    $abmMenu = new abmMenu();
    $res = $abmMenu->guardarMenuConRoles($datos);
}

echo json_encode($res);
?>