<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$abmUsuarioRol = new abmUsuarioRol();

// Verificar si ya tenemos  el rol
$existe = $abmUsuarioRol->buscar(['idusuario' => $datos['idusuario'], 'idrol' => $datos['idrol']]);

if(count($existe) == 0){
    $objUser = new Usuario();
    $objUser->setear($datos['idusuario'], null, null, null, null);
    
    $objRol = new Rol();
    $objRol->setear($datos['idrol'], null);
    
    if($abmUsuarioRol->alta(['objUsuario' => $objUser, 'objRol' => $objRol])){
        echo json_encode(['exito' => true, 'msg' => 'Rol asignado correctamente']);
    } else {
        echo json_encode(['exito' => false, 'msg' => 'Error al asignar rol']);
    }
} else {
    echo json_encode(['exito' => false, 'msg' => 'El usuario ya tiene ese rol']);
}
?>