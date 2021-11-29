<?php
use Fpdf\Fpdf;
class PDF
{
    public function hacerPDF($lista){
        
        $pdf = new Fpdf(); 
        $pdf->AddPage();
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,4,'Alan E. Pucci');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4,'Simulacro segundo parcial');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40,10, 'Criptomoneda', 1);
        $pdf->Cell(40,10, 'Fecha', 1);
        $pdf->Cell(40,10, 'Cantidad', 1);
        $pdf->Cell(40,10, 'Precio', 1);
        $pdf->Cell(40,10, 'Nacionalidad', 1);
        $pdf->Cell(40,10, 'Mail', 1);
        $pdf->Ln();
        
        // PRODUCTOS
        foreach ($lista as $item) {
            $pdf->Cell(40,10, $item["criptomoneda"], 1);
            $pdf->Cell(40,10, $item["fecha"], 1);
            $pdf->Cell(40,10, $item["cantidad"], 1);
            $pdf->Cell(40,10, $item["precio"], 1);
            $pdf->Cell(40,10, $item["nacionalidad"], 1);
            $pdf->Cell(40,10, $item["mail"], 1);
            $pdf->Ln();
        }
        $pdf->Output('ventas.pdf','f');
        $pdf->Output('ventas.pdf','i');
        return;
    }
}