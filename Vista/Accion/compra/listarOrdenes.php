<?php
include_once("../../../configuracion.php");
$session = new Session();
$salida = [];

if($session->activa() && ($session->getRolActivo() == 1 || $session->getRolActivo() == 3)){
    $abmCompra = new abmCompra();
    
    
    $salida = $abmCompra->listarVentas();
}

echo json_encode($salida);
?>