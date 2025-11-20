<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$abmUsuario = new abmUsuario();

if($session->activa()){
    $usuarioActual = $session->getUsuario();

    
    if(isset($datos['idusuario']) && $datos['idusuario'] == $usuarioActual->getIdUsuario()){
        
       
        $passFinal = $usuarioActual->getUsPass();
        
        
        if(isset($datos['uspass']) && trim($datos['uspass']) != ""){
            $passFinal = md5($datos['uspass']);
        }

        $param = [
            'idusuario' => $datos['idusuario'],
            'usnombre' => $usuarioActual->getUsNombre(),
            'usmail' => $datos['usmail'],
            'uspass' => $passFinal
        ];

        if($abmUsuario->modificacion($param)){
           
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