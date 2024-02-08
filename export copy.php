<?php
// include 'submit.php';
require_once 'PhpXlsxGenerator.php';

// Filter the excel data
function filterData(&$str){
    // echo $str;
    $str = preg_replace("/\t/", "", $str);
    $str = preg_replace("/\r?\n/", " ", $str);
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
// $counterBalance = unserialize(urldecode($_GET['counterBalance']));
// print_r($counterBalance);
// echo $counterBalance;
// $balanceArr = unserialize(urldecode($_GET['balanceArr']));
// print_r($balanceArr);

// Excel file name for download
$fileName = "members-data_" . date('Y-m-d') . ".xls";

$header = array($name, ' ', $statementDate, $outstandingBalance );

$excelData = implode("\t", array_values($header)) . "\n";
// Column names
$fields = array('Sale Date', 'Post Date', 'Transaction Details', 'Amount');

// Display column names as first row
$excelData = implode("\t", array_values($fields)) . "\n";

// $space = 0;

foreach($tbPostDate as $x => $postDate){
    // if($postDate == 'Post Date'){
        
    // }
    
    // echo $postDate."\n";
    $lineData = array($tbSaleDate[$x], $postDate, $tbTransactionDetails[$x], $tbAmount[$x]);
    array_walk($lineData, 'filterData');
    $excelData .= implode("\t", array_values($lineData)) . "\n";
    
    // echo $excelData . "<br>";
}

// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
echo $excelData;
// Export data to excel and download as xlsx file
// $xlsx = CodexWorld\PhpXlsxGenerator::fromArray($excelData);
// $xlsx->downloadAs($fileName);

exit;
?>