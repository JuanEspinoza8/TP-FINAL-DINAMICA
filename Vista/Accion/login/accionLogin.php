<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();

if(isset($datos['usnombre']) && isset($datos['uspass'])){
    
    if($session->iniciar($datos['usnombre'], $datos['uspass'])){
        header('Location: ../../productos.php');
    } else {
        header('Location: ../../login.php?error=1');
    }
} else {
    header('Location: ../../login.php?error=vacios');
}
exit;
?>