<?php
require_once __DIR__ . '/../db/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/gizmo/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ['Title', 'Discount', 'Colors', 'Old Price', 'Price', 'Monthly', 'Category', 'Subcategory'];
foreach ($headers as $i => $header) {
	$col = chr(65 + $i); // 65 = 'A'
	$sheet->setCellValue($col . '1', $header);
}

// Fetch products
$res = $conn->query("SELECT p.*, c.title as category_title, s.title as subcategory_title
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    ORDER BY p.id DESC");

$rowNum = 2;
while ($row = $res->fetch_assoc()) {
	$sheet->setCellValue('A' . $rowNum, $row['title']);
	$sheet->setCellValue('B' . $rowNum, $row['discount']);
	$sheet->setCellValue('C' . $rowNum, $row['colors']);
	$sheet->setCellValue('D' . $rowNum, $row['oldPrice']);
	$sheet->setCellValue('E' . $rowNum, $row['price']);
	$sheet->setCellValue('F' . $rowNum, $row['monthly']);
	$sheet->setCellValue('G' . $rowNum, $row['category_title']);
	$sheet->setCellValue('H' . $rowNum, $row['subcategory_title']);
	$rowNum++;
}

$conn->close();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="products.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
