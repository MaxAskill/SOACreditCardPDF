<?php
// include 'submit.php';
// require_once 'PhpXlsxGenerator.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Filter the excel data
function filterData(&$str){
    // echo $str;
    $str = preg_replace("/\t/", "", $str);
    $str = preg_replace("/\r?\n/", "", $str);
    $str = preg_replace("<<br />>", "", $str);
    // $str = preg_replace('/\s{2,}/', ' ', $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

$tbSaleDate = unserialize(urldecode($_GET['tbSaleDate']));
// print_r($tbSaleDate);
$tbPostDate = unserialize(urldecode($_GET['tbPostDate']));
// print_r($tbPostDate);
$tbTransactionDetails = unserialize(urldecode($_GET['tbTransactionDetails']));
//  print_r($tbTransactionDetails);
$tbAmount = unserialize(urldecode($_GET['tbAmount']));
// print_r($tbAmount);
$name = unserialize(urldecode($_GET['name']));
// print_r($name);
$statementDate = unserialize(urldecode($_GET['statementDate']));
// print_r($tbAmount);
$outstandingBalance = unserialize(urldecode($_GET['outstandingBalance']));
// print_r($tbAmount);
$newTransactionCtr = unserialize(urldecode($_GET['newTransactionCtr']));
// print_r($newTransactionCtr);
// $counterarray = $counterarray;
$counterarray = $_GET['counterarray'];

$counter = $_SESSION['count'];


// Excel file name for download
$fileName = "members-data_" . date('Y-m-d') . ".xlsx";

// Create a spreadsheet
$spreadsheet = new Spreadsheet();

// Create the first sheet
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('PH Transaction');
$sheet->getCell('F5')->setValue($counterarray);
// $header = array($name, ' ', $statementDate, $outstandingBalance );
// $excelData = implode("\t", array_values($header)) . "\n";

// Column names
$fields1 = array('Sale Date', 'Post Date', 'Transaction Details', 'Amount');
$fields2 = array('Sale Date', 'Post Date', 'Transaction Details', 'Amount', 'Amount in (PHP)');

// Display column names as first row
$excelData = implode("\t", array_values($fields1)) . "\n";

// $space = 0;

foreach($tbPostDate as $x => $postDate){

    $lineData = array($tbSaleDate[$x], $postDate, $tbTransactionDetails[$x], $tbAmount[$x]);
    array_walk($lineData, 'filterData');
    $excelData .= implode("\t", array_values($lineData)) . "\n";
    // echo $excelData . "<br>";
    if($x > $counterarray) break;
}

// Split the data into rows
$dataRows = explode("\n", $excelData);
// Add each row to the worksheet starting from A2
foreach ($dataRows as $rowIndex => $rowData) {
    // Split the row data into cells
    $dataCells = explode("\t", $rowData);

    // Add the data to the worksheet
    foreach ($dataCells as $columnIndex => $cellData) {
        $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $cellData);

    }
}

// Add the first sheet to the Excel file
// $spreadsheet->getActiveSheet()->fromArray(explode("\n", $excelData), null, 'A2');

// Create the second sheet
$spreadsheet->createSheet();
$sheet2 = $spreadsheet->setActiveSheetIndex(1);
$sheet2->setTitle('Foreign Transaction');

// Display column names as first row
$excelData = implode("\t", array_values($fields2)) . "\n";


// inserting data into Sheet 2
foreach ($tbPostDate as $x => $postDate) {
    if($tbPostDate == 'TOTAL'){
        $sheet2->getCell('F5')->setValue(123.456);
        // $sheet2->setCellValue('F5', 'TOTAL');
    }
    if($x > $counterarray){
        $lineData = array($tbSaleDate[$x], $postDate, $tbTransactionDetails[$x], $tbAmount[$x]);
        array_walk($lineData, 'filterData');
        $excelData .= implode("\t", array_values($lineData)) . "\n";
    }
}

// Split the data into rows
$dataRows = explode("\n", $excelData);

// Add each row to Sheet 2 starting from A2
foreach ($dataRows as $rowIndex => $rowData) {
    // Split the row data into cells
    $dataCells = explode("\t", $rowData);

    // Add the data to Sheet 2
    foreach ($dataCells as $columnIndex => $cellData) {
        $sheet2->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $cellData);
    }
}

$sheet1->setCellValue('F2', $counter);


// Save the Excel file
$writer = new Xlsx($spreadsheet);
$writer->save($fileName);

// Headers for download
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
// echo $excelData;
// Export data to excel and download as xlsx file
// $xlsx = CodexWorld\PhpXlsxGenerator::fromArray($excelData);
// $xlsx->downloadAs($fileName);

// Read the file and output it
readfile($fileName);

// Delete the file after it's downloaded
unlink($fileName);

exit;
?>