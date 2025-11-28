<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$respuesta = ['exito' => false, 'msg' => 'Permisos insuficientes'];

if($session->activa()){
    
    $abmProducto = new abmProducto();
    $respuesta = $abmProducto->actualizarDatosProducto($datos, $_FILES);
    
}

echo json_encode($respuesta);
?>