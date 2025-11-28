<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$respuesta = ['exito' => false, 'msg' => 'Error de sesión'];

if($session->activa()){
    if(isset($datos['idcompraitem'])){
        $abmCompra = new abmCompra();
        $respuesta = $abmCompra->quitarProducto($datos['idcompraitem']);
    } else {
        $respuesta['msg'] = 'Datos insuficientes.';
    }
}

echo json_encode($respuesta);
?>