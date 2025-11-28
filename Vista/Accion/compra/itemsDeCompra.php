<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$resultado = [];

if($session->activa() && isset($datos['idcompra'])){
    $abmCompra = new abmCompra();
    $resultado = $abmCompra->obtenerItemsDeCompra($datos['idcompra']);
}

echo json_encode($resultado);
?>