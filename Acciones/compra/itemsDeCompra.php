<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$session = new Session();
$resultado = [];

if($session->activa() && isset($datos['idcompra'])){
    $abmItem = new abmCompraItem();
    
    
    $listaItems = $abmItem->buscar(['idcompra' => $datos['idcompra']]);
    
    foreach($listaItems as $item){
        $prod = $item->getObjProducto();
        $resultado[] = [
            'pronombre' => $prod->getProDetalle(), 
            'cantidad' => $item->getCiCantidad(),
            'precio' => $prod->getProPrecio(), 
            'total' => $item->getCiCantidad() * $prod->getProPrecio() 
        ];
    }
}
echo json_encode($resultado);
?>