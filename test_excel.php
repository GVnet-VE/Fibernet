<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', '¡Excel generado con éxito!');

$writer = new Xlsx($spreadsheet);
$writer->save('test.xlsx');

echo "Archivo Excel creado: test.xlsx";
?>