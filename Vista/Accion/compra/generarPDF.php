<?php
include_once("../../../configuracion.php");
$datos = data_submitted();
$session = new Session();

if($session->activa()){
    if(isset($datos['idcompra'])){
        
        $abmCompra = new abmCompra();
        $infoCompra = $abmCompra->obtenerDatosParaPDF($datos['idcompra']);

        if($infoCompra != null){
            $generador = new PdfOrdenCompra();
            $generador->renderizar($infoCompra);
        } else {
            echo "Error: No se encontraron datos de la compra.";
        }
    } else {
        echo "Error: Falta ID de compra.";
    }
} else {
    
    header('Location: ../../../login.php');
}
?>