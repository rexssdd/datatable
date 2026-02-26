<?php
require('fpdf/fpdf.php');
require 'db.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

$pdf->Cell(40,10,'Full Name');
$pdf->Cell(40,10,'Email');
$pdf->Cell(30,10,'Age');
$pdf->Cell(40,10,'Contact');
$pdf->Ln();

$stmt = $pdo->query("SELECT full_name,email,age,contact_number FROM users");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(40,10,$row['full_name']);
    $pdf->Cell(40,10,$row['email']);
    $pdf->Cell(30,10,$row['age']);
    $pdf->Cell(40,10,$row['contact_number']);
    $pdf->Ln();
}

$pdf->Output();