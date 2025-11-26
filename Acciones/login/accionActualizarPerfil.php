<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();

if($session->activa()){
    $usuarioActual = $session->getUsuario();
    $abmUsuario = new abmUsuario();

    if(isset($datos['idusuario'])){
        $resultado = $abmUsuario->actualizarPerfil($datos, $usuarioActual);
        
        if($resultado['exito']){
            header('Location: ../../Vista/modificarPerfil.php?msg=actualizado');
        } else {
            header('Location: ../../Vista/modificarPerfil.php?error=db');
        }
    } else {
        header('Location: ../../Vista/index.php');
    }
} else {
    header('Location: ../../Vista/login.php');
}
?>