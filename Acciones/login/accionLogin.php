<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();

if(isset($datos['usnombre']) && isset($datos['uspass'])){
    
    if($session->iniciar($datos['usnombre'], $datos['uspass'])){
        header('Location: ../../Vista/productos.php');
    } else {
        header('Location: ../../Vista/login.php?error=1');
    }
} else {
    header('Location: ../../Vista/login.php?error=vacios');
}
exit;
?>