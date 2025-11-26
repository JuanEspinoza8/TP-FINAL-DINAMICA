<?php
include_once("../../configuracion.php");
$session = new Session();
$listaSalida = [];

if($session->activa()){
    $idRol = $session->getRolActivo();
    if($idRol == null){
        $idRol = 1; 
    }

    $abmMenu = new abmMenu();
    $listaSalida = $abmMenu->obtenerMenuesFormateadosPorRol($idRol);
}
echo json_encode($listaSalida);
?>