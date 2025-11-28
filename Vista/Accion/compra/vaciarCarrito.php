<?php
include_once("../../../configuracion.php");
$session = new Session();
$respuesta = ['exito' => false, 'msg' => 'Error de sesión'];

if($session->activa()){
    $idUsuario = $session->getUsuario()->getIdUsuario();
    $abmCompra = new abmCompra();
    
    $respuesta = $abmCompra->vaciarCarrito($idUsuario);
}

echo json_encode($respuesta);
?>