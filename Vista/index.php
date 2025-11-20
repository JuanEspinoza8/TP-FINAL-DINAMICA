<?php
include_once("../configuracion.php");
$session = new Session();
if($session->activa()){
    header('Location: productos.php');
} else {
    header('Location: login.php');
}
?>