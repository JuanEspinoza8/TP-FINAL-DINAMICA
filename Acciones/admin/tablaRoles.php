<?php
include_once("../../configuracion.php");
$session = new Session();

// Validamos que haya sesión activa y sea Admin (Rol 1)
if($session->activa() && $session->getRolActivo() == 1){
    $objRol = new Rol();
    // Llamamos al metodo listar del Modelo Rol
    $listaRoles = $objRol->listar("");
    $salida = [];
    
    foreach($listaRoles as $rol){
        $salida[] = [
            'idrol' => $rol->getIdRol(),
            'rodescripcion' => $rol->getRoDescripcion()
        ];
    }
    echo json_encode($salida);
} else {
    // Si no es admin o no hay sesion, devolvemos array vacio
    echo json_encode([]);
}
?>