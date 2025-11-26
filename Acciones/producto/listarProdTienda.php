<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$abmProducto = new abmProducto();


$arregloSalida = $abmProducto->listarProductosTienda($datos);

echo json_encode($arregloSalida);
?>