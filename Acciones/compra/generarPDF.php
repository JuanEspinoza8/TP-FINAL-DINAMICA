<?php
include_once("../../configuracion.php");

include_once("../../Utiles/librerias/fpdf/fpdf.php"); 

$datos = data_submitted();
$session = new Session();

if($session->activa()){
    if(isset($datos['idcompra'])){
        
        $abmCompra = new abmCompra();
        $infoCompra = $abmCompra->obtenerDatosParaPDF($datos['idcompra']);

        if($infoCompra != null){
            
            
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            
            
            $pdf->Cell(190, 10, 'Orden de Compra #' . $infoCompra['idcompra'], 0, 1, 'C');
            $pdf->Ln(10);

            
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(50, 10, 'Fecha:', 0, 0);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, $infoCompra['fecha'], 0, 1);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(50, 10, 'Cliente:', 0, 0);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, $infoCompra['cliente_nombre'] . " (" . $infoCompra['cliente_mail'] . ")", 0, 1);
            
            $pdf->Ln(10);

            
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(80, 10, 'Producto', 1, 0, 'L', true);
            $pdf->Cell(30, 10, 'Precio', 1, 0, 'R', true);
            $pdf->Cell(30, 10, 'Cant.', 1, 0, 'C', true);
            $pdf->Cell(50, 10, 'Subtotal', 1, 1, 'R', true);

            
            $pdf->SetFont('Arial', '', 11);
            foreach($infoCompra['items'] as $item){
                $pdf->Cell(80, 10, utf8_decode($item['producto']), 1); // utf8_decode para tildes
                $pdf->Cell(30, 10, '$' . number_format($item['precio'], 2), 1, 0, 'R');
                $pdf->Cell(30, 10, $item['cantidad'], 1, 0, 'C');
                $pdf->Cell(50, 10, '$' . number_format($item['subtotal'], 2), 1, 1, 'R');
            }

            
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(140, 10, 'Total Final:', 0, 0, 'R');
            $pdf->Cell(50, 10, '$' . number_format($infoCompra['total'], 2), 0, 1, 'R');

            
            $pdf->Output('I', 'Orden_'.$infoCompra['idcompra'].'.pdf');
            
        } else {
            echo "Error: No se encontraron datos de la compra.";
        }
    } else {
        echo "Error: Falta ID de compra.";
    }
} else {
    header('Location: ../../Vista/login.php');
}
?>