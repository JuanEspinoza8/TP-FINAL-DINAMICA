<?php
// Asegúrate de que esta ruta sea correcta desde la carpeta Control
include_once("../Utiles/librerias/fpdf/fpdf.php");

class PdfOrdenCompra extends FPDF {
    
    // Cabecera de página automática
    function Header(){
        // Logo (si tuvieras)
        // $this->Image('logo.png',10,6,30);
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Tienda Online - Orden de Compra',0,1,'C');
        $this->Ln(5);
        
        // Línea divisoria
        $this->SetDrawColor(0,0,0);
        $this->Line(10, 25, 200, 25);
        $this->Ln(10);
    }

    // Pie de página automático
    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo(),0,0,'C');
    }

    // Función principal para dibujar el contenido
    public function renderizar($infoCompra){
        $this->AddPage();
        
        // --- Datos del Encabezado (Cliente y Fecha) ---
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, utf8_decode('N° Orden:'), 0, 0);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, '#' . $infoCompra['idcompra'], 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Fecha:', 0, 0);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $infoCompra['fecha'], 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Cliente:', 0, 0);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, utf8_decode($infoCompra['cliente_nombre']) . " (" . $infoCompra['cliente_mail'] . ")", 0, 1);
        
        $this->Ln(10);

        // --- Tabla de Items ---
        
        // Encabezados de Tabla
        $this->SetFillColor(50, 50, 50);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 11);
        
        $this->Cell(90, 10, 'Producto', 1, 0, 'L', true);
        $this->Cell(30, 10, 'Precio U.', 1, 0, 'R', true);
        $this->Cell(20, 10, 'Cant.', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Subtotal', 1, 1, 'R', true);

        // Cuerpo de Tabla
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 11);
        
        foreach($infoCompra['items'] as $item){
            $nombreProd = utf8_decode($item['producto']);
            // Si quieres mostrar el detalle: $nombreProd .= " (" . utf8_decode($item['detalle']) . ")";
            
            $this->Cell(90, 10, $nombreProd, 1);
            $this->Cell(30, 10, '$' . number_format($item['precio'], 2), 1, 0, 'R');
            $this->Cell(20, 10, $item['cantidad'], 1, 0, 'C');
            $this->Cell(50, 10, '$' . number_format($item['subtotal'], 2), 1, 1, 'R');
        }

        // --- Totales ---
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(140, 10, 'Total Final:', 0, 0, 'R');
        $this->SetTextColor(0, 100, 0); // Verde oscuro
        $this->Cell(50, 10, '$' . number_format($infoCompra['total'], 2), 0, 1, 'R');

        // Salida
        $this->Output('I', 'Orden_'.$infoCompra['idcompra'].'.pdf');
    }
}
?>