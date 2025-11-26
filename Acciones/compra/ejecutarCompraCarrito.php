<?php
include_once("../../configuracion.php");
$session = new Session();
$datos = data_submitted(); 
$respuesta = ['exito' => false, 'msg' => 'Error de sesión'];

if($session->activa()){
    $idUsuario = $session->getUsuario()->getIdUsuario();
    $abmCompra = new abmCompra();
    
    $respuesta = $abmCompra->finalizarCompra($idUsuario);
    
} else {
    $respuesta['msg'] = "La sesión ha expirado.";
}

echo json_encode($respuesta);
?>