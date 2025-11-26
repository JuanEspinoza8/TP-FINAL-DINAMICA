<?php
include_once("../../configuracion.php");
$session = new Session();
$resultado = [];

if($session->activa()){
    $idUsuario = $session->getUsuario()->getIdUsuario();
    $abmCompra = new abmCompra();
    
    
    $resultado = $abmCompra->obtenerListadoCarrito($idUsuario);
}

echo json_encode($resultado);
?>