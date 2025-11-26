<?php
include_once("../../configuracion.php");
$datos = data_submitted();
$abmRol = new abmRol();

$lista = $abmRol->listarRolesVista();

echo json_encode($lista);
?>