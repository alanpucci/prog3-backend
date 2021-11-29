<?php
use Fpdf\Fpdf;
class PDF
{
    public function hacerPDF($lista){
        
        $pdf = new Fpdf(); 
        $pdf->AddPage();

        $pdf->SetFont('Helvetica','',16);
        $pdf->Cell(60,4,'Alan Ezequiel Pucci',0,1,'C');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4,'Segundo parcial',0,1,'C');
        $pdf->Cell(60,4,'Listado de ventas',0,1,'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(30,10, 'Criptomoneda', 1);
        $pdf->Cell(30,10, 'Fecha', 1);
        $pdf->Cell(30,10, 'Cantidad', 1);
        $pdf->Cell(30,10, 'Precio', 1);
        $pdf->Cell(30,10, 'Nacionalidad', 1);
        $pdf->Cell(30,10, 'Mail', 1);
        $pdf->Ln();
        
        // PRODUCTOS
        foreach ($lista as $item) {
            $pdf->Cell(30,10, $item["criptomoneda"], 1);
            $pdf->Cell(30,10, $item["fecha"], 1);
            $pdf->Cell(30,10, $item["cantidad"], 1);
            $pdf->Cell(30,10, $item["precio"], 1);
            $pdf->Cell(30,10, $item["nacionalidad"], 1);
            $pdf->Cell(30,10, $item["mail"], 1);
            $pdf->Ln();
        }
        $pdf->Output($this->destinoPDF(),'f');
        $pdf->Output($this->destinoPDF(),'i');
        return;
    }

    public function destinoPDF(){
        if(!file_exists("Ventas/")){
            mkdir("Ventas/",0777,true);
        }
        $date = new DateTime("now");
        $tiempoAhora = $date->format('Y-m-d-H_i_s');
        $nombreArchivo = "listaVentas_".$tiempoAhora.".pdf";
        $destino = "Ventas/".$nombreArchivo;
        return $destino;
    }
}