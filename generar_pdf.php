<?php
require_once __DIR__ . '/vendor/autoload.php';

use TCPDF;

$conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

if (!isset($_GET['id'])) {
    die("Falta parametro ?id=...");
}
$ids = explode(",", $_GET['id']);
$ids = array_map('intval', $ids);
$id_list = implode(",", $ids);

$sql = "SELECT * FROM inscripciones WHERE id IN ($id_list)";
$resultado = $conexion->query($sql);

if (!$resultado || $resultado->num_rows === 0) {
    die("No se encontraron registros.");
}

$pdf = new TCPDF();
$pdf->SetCreator('WordPress');
$pdf->SetAuthor('Sistema de Inscripcion');
$pdf->SetTitle('Inscripcion');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

while ($fila = $resultado->fetch_assoc()) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Write(10, "Ficha de inscripcion ID #" . $fila['id'], '', 0, 'L', true);
    $pdf->Ln(2);

    $foto_path = __DIR__ . "/fotos/" . $fila["foto_nombre"];
    if (file_exists($foto_path)) {
        $pdf->Image($foto_path, 150, $pdf->GetY(), 40);
    }

    $pdf->SetFont('helvetica', '', 10);

    $pdf->Write(6, "Nombre del alumno: {$fila['alumno_nombre']} {$fila['alumno_apellido1']} {$fila['alumno_apellido2']}", '', 0, 'L', true);
    $pdf->Write(6, "DNI: {$fila['alumno_dni']}", '', 0, 'L', true);
    $pdf->Write(6, "Nacimiento: {$fila['alumno_nacimiento']} - Lugar: {$fila['alumno_lugar']}", '', 0, 'L', true);
    $pdf->Write(6, "Direccion: {$fila['alumno_domicilio']}, CP: {$fila['alumno_cp']} ({$fila['alumno_localidad']})", '', 0, 'L', true);
    $pdf->Write(6, "Curso solicitado: {$fila['curso_solicitado']}", '', 0, 'L', true);

    $pdf->Ln(4);
    $pdf->Write(6, "Estudios anteriores: {$fila['estudios_anteriores']} en {$fila['centro_estudios']}", '', 0, 'L', true);

    $pdf->Ln(6);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, "Datos del tutor", '', 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Write(6, "Nombre: {$fila['tutor_nombre']} {$fila['tutor_apellido1']} {$fila['tutor_apellido2']}", '', 0, 'L', true);
    $pdf->Write(6, "DNI: {$fila['tutor_dni']} - Nacimiento: {$fila['tutor_nacimiento']}", '', 0, 'L', true);
    $pdf->Write(6, "Telefono: {$fila['tutor_telefono']} - Email: {$fila['tutor_email']}", '', 0, 'L', true);
    $pdf->Ln(8);

    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(6);
}

$filename = count($ids) === 1
    ? "inscripcion_{$ids[0]}.pdf"
    : "inscripciones_" . date("Ymd_His") . ".pdf";

$pdf->Output($filename, 'I');
