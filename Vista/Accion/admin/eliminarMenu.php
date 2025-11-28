<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$res = ['exito'=>false];

if($session->activa() && $session->getRolActivo() == 1){
    $abmMenu = new abmMenu();
    if($abmMenu->cambiarEstadoMenu($datos)){
        $res['exito'] = true;
    }
}
echo json_encode($res);
?>