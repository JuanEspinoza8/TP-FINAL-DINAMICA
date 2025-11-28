<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$respuesta = ['exito' => false, 'msg' => 'Acceso denegado o sesión expirada'];

if($session->activa()){
    if(isset($datos['idproducto']) && isset($datos['cantidad'])){
        
        $idUsuario = $session->getUsuario()->getIdUsuario();
        $idProducto = $datos['idproducto'];
        $cantidad = (int)$datos['cantidad'];
        
        $abmCompra = new abmCompra();
        $respuesta = $abmCompra->agregarProductoAlCarrito($idUsuario, $idProducto, $cantidad);
        
    } else {
        $respuesta['msg'] = "Faltan datos del producto.";
    }
}

echo json_encode($respuesta);
?>