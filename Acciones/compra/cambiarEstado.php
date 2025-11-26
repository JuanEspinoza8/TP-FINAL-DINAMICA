<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$respuesta = ['exito' => false, 'msg' => 'Permisos insuficientes'];

// Verificamos si es Admin (1) o Depósito (3)
if($session->activa() && ($session->getRolActivo() == 1 || $session->getRolActivo() == 3)){
    
    if(isset($datos['idcompra']) && isset($datos['idestadotipo'])){
        $abmCE = new abmCompraEstado();
        $respuesta = $abmCE->cambiarEstadoCompra($datos['idcompra'], $datos['idestadotipo']);
    } else {
        $respuesta['msg'] = 'Datos incompletos.';
    }
}

echo json_encode($respuesta);
?>