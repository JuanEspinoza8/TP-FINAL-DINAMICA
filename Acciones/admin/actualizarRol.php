<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$abmUsuarioRol = new abmUsuarioRol();
$respuesta = ['exito' => false, 'msg' => 'Datos incompletos'];

if(isset($datos['idusuario']) && isset($datos['idrol'])){
    $respuesta = $abmUsuarioRol->agregarRol($datos['idusuario'], $datos['idrol']);
}

echo json_encode($respuesta);
?>