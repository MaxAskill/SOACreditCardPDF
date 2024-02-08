<?php
require_once 'vendor/autoload.php';
// Include XLSX generator library
require_once 'PhpXlsxGenerator.php';

$pdfText = '';
$cont = 0;
if(isset($_POST['submit'])){
    // If file is selected
    if(!empty($_FILES["pdf_file"]["name"])){
        // File upload path
        $fileName = basename($_FILES["pdf_file"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('pdf');
        if(in_array($fileType, $allowTypes)){
            // Include autoloader file
            // include 'vendor/autoload.php';

            // Initialize and load PDF Parser library
            $parser = new \Smalot\PdfParser\Parser();

            // Source PDF file to extract text
            $file = $_FILES["pdf_file"]["tmp_name"];

            // Parse pdf file using Parser library
            $pdf = $parser->parseFile($file);

            // Extract text from PDF
            $text = $pdf->getText();

            // Add line break
            $pdfText = nl2br($text);
        }else{
            $statusMsg = '<p>Sorry, only PDF file is allowed to upload.</p>';
        }
    }else{
        $statusMsg = '<p>Please select a PDF file to extract text.</p>';
    }
}

$toString = strval($pdfText);

// // Define the regular expression pattern
$pattern = '/(\d{2}\/\d{2}\/\d{2}\s\d{2}\/\d{2}\/\d{2})/';

// Use preg_split to split the text based on the pattern
$transactions = preg_split($pattern, $toString, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

// print_r($transactions);

$dates = [];
$transactionDetails = [];
foreach($transactions as $x => $date) {
    if($x == 0){
    }
    else if($x % 2 != 0){
        array_push($dates, $date);
    }else{

        $diff = preg_split('/For more details/', $date, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if(count($diff) == 2){
            $date = $diff[0];
        }
        array_push($transactionDetails, $date);
    }
}

$name = explode("Credit Cards", $toString)[0];

// Use preg_replace to remove text enclosed within asterisks
$pattern = '/\*([^*]+)\*/';
$name = preg_replace($pattern, '', $name);
?>

<!-- TOP DETAILS -->
<div class="row" style="margin-left: 14.5%;margin-right: 14.5%">
  <div class="column">

    <?php
      echo  $name;
    ?>
  </div>

  <div class="column">
    <?php

    $breakVISA = explode("VISA GOLD CORPORATE", $toString)[1];
    $VISA = explode("Statement Date", $breakVISA)[0];
    $VISA = $VISA;
    // echo $VISA;

    // get the Statement Date
    $breakStatementDate = explode("Statement Date", $toString)[1];
    $statementDate = explode("Card Number", $breakStatementDate)[0];
    $statementDate = "STATEMENT DATE:" . $statementDate;
    echo $statementDate ;

    // get the Card Number
    $breakCardNumber = explode("Card Number", $toString)[1];
    $CardNumber = explode("Interest Rate per Month", $breakCardNumber)[0];
    $CardNumber = "CARD NUMBER: ". $CardNumber;
    echo $CardNumber;

    // get the Outstanding Balance
    $breakOutsatndingBalance = explode("Outstanding Balance", $toString)[1];
    $outstandingBalance = explode("Payment Due", $breakOutsatndingBalance)[0];
    $outstandingBalance = "OUTSTANDING BALANCE:" . $outstandingBalance;
    // echo  $outstandingBalance;

    //get Payment Due Date
    $breakPaymentDueDate = explode("Payment Due Date", $toString)[1];
    $paymentDueDate = explode("Your Account Summary", $breakPaymentDueDate)[0];
    $paymentDueDate = "PAYMENT DUE DATE: ". $paymentDueDate;
    echo "<b> $paymentDueDate </b>";

    $from_currency = "SGD";
    $to_currency = "PHP";
    $amount = 1;
    $response = convertCurrency($from_currency,$to_currency,$amount);

    echo "PESO TO SGD: " . $response . "<br>";

    $from_currency = "USD";
    $to_currency = "PHP";
    $amount = 1;
    $response = convertCurrency($from_currency,$to_currency,$amount);

    echo "PESO TO USD: " . $response;

    // get the currency
    $breakCurrency = explode("500.00 ", $toString)[1];
    $currencys = explode(" DOLLARS", $breakCurrency)[0];
    // $currencys = "Currency: ". $currencys;
    // echo $currencys;

    ?>
  </div>
</div>

<!--  -->
<?php

// For Conversion
function convertCurrency($from_currency,$to_currency,$amount){

  // $req_url = 'http://api.exchangerate.host/convert?access_key=d9bb9f1a0c752461ce0784cbe1160fdb&from='.$from_currency.'&to='.$to_currency.'&amount='.$amount;
  $req_url = 'https://v6.exchangerate-api.com/v6/ffd5649913ca0880bd83f9c0/pair/'.$from_currency.'/'.$to_currency.'/'.$amount;

  $response_json = file_get_contents($req_url);
  $converted_amount = 0;


  if(false !== $response_json) { //Check if have values
      try {
        // print_r($response_json);
          $response = json_decode($response_json); //Decode the response and change it into Json

          if($response->result === "success") { //Check the result if it is success
               // Read conversion rate
              //  echo $response;
               $converted_amount = $response->conversion_result; //Transfer the value to converted_amount

          }

      } catch(Exception $e) {
          // Handle JSON parse error...

      }
  }

  $formattedAmount = number_format($converted_amount, 2, '.', ','); // format the amount into currency that have comma and decimal
  return $formattedAmount;
}

$from_currency = "SGD";
$to_currency = "PHP";
// $amount = 1;
// $date = date_create('06/29/23');
// $date = date_format($date, 'Y-d-m');
// echo $date;
// $response = convertCurrency($from_currency,$to_currency,$amount);

// echo "<pre>";
// print_r($response);
// echo "</pre>";

 $nameOnly = preg_split('/NEW BARBIZON FASHION INC/', $name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)[0];
 $nameOnly = str_replace(' ', '-', $nameOnly);
//  echo $nameOnly;

$toCurrency;
$fromCurrency = "USD";
$fromAmount;

// getting the currency
$counterCurrency = 0;
$currencyBreak = explode("VISA GOLD CORPORATE", $toString);
$currencyArr = [];
foreach($currencyBreak as $x => $currency){
  if($x != 0){
    $currency = explode("Statement Date", $currency)[0];
    array_push($currencyArr, $currency);

    // echo $currency;
    // print_r($currencyArr);
  }
}





// Getting all the balance
$counterBalance = 0;
$balanceBreak = explode("PREVIOUS STATEMENT BALANCE", $toString);
$balanceArr = [];
foreach($balanceBreak as $x => $balance){
    if($x != 0){
        $balance = explode("CARD NUMBER", $balance)[0];
        array_push($balanceArr, $balance);

        // echo $balance;
        // print_r($balanceArr);
    }


}

// array for each column
$tbSaleDate = [];
$tbPostDate = [];
$tbTransactionDetails = [];
$tbAmount = [];
$tbCoverted = [];
$tbAmountConverted = [];
$counterarray;



// pushing value into array
array_push($tbSaleDate, ' ');

array_push($tbPostDate, ' ');

array_push($tbTransactionDetails, "PREVIOUS STATEMENT BALANCE ".$CardNumber);

// print_r($tbTransactionDetails);
// echo $CardNumber;
// print_r(explode("CARD NUMBER", $balanceBreak));

array_push($tbAmount, $balanceArr[$counterBalance]);
?>

<!-- <button class="export-button" type='button' onclick="document.location='export.php'">EXPORT</button> -->
<!-- <form action="export.php" method="post">
<input class ="export-button" type='submit' value="EXPORT"/>

-->


<?php
  $counterarray = 0;
  // export button
  // this is for printing the result in the excel
  // for Sheet 1
  foreach($transactionDetails as $x => $transaction){

    $date = preg_split('/(\s)/', $dates[$x]);

    array_push($tbSaleDate, $date[0]);

    array_push($tbPostDate, $date[1]);

    $referenceCount = preg_split('/(Reference:|500.00 SINGAPORE DOLLARS|SUBTOTAL)\s.*?/', $transaction);
    $referenceCounts = preg_split('/(Reference:|SUBTOTAL)\s.*?/', $transaction);
    $amounts = preg_split('/(\s)/', $referenceCount[0]);
    $amountCount = count($amounts);

    // Use preg_replace to remove the amount from the string
    // $pattern = '/\d+(?:,\d+)?\.\d+/';

    // remove the amount and except the 500.00 in SINGAPORE DOLLAR
    $pattern = '/\b(?!500\.00)\d+(?:,\d+)?\.\d+\b/';
    $referenceCounts[0] = preg_replace($pattern, '', $referenceCounts[0]);
    array_push($tbTransactionDetails, preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]));

    $counterarray = $counterarray + 1;
    // echo preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]) . '-- ' . $counterarray . ' --' .'<br>';

    // split the reference
    if (preg_match('/\bReference:\s*(\S+)/', $transaction, $matches)) {
        $reference = $matches[0];

        $tbTransactionDetails[$x + 1] = preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]) .  $reference;

        $test = preg_replace('/(\n.*)/', '', $tbTransactionDetails[$x + 1]) . $reference;

        // echo $test;
        // $test = preg_split('/(\s)/', $tbTransactionDetails[$x -1]);
        // // print_r($test);
        // // echo "<br>";
        // foreach($test as $ex){
        //   if($ex != "/>")
        //   $ex .= $ex . " ";

        // }
        // echo $ex;
        // echo "<br>";
        // echo $referenceCount[0] . $reference;
        // $tbTransactionDetails[$x + 1] = str_replace(["\r", "\n", "\s"], " ", $tbTransactionDetails[$x + 1]);
        // echo $tbTransactionDetails[$x + 1];
        // print_r($tbTransactionDetails);
        // echo $transaction;
    }



    array_push($tbAmount, $amounts[$amountCount - 4]);
    if ($counterBalance != 0) {
        $amount = abs(intval($amounts[$amountCount - 4])); // Getting the absolute amount
        $isNegative = intval($amounts[$amountCount - 4]) < 0; // Checking if negative
        $response = convertCurrency($from_currency,$to_currency,$amount);
        if ($isNegative) // Checking if Negative
                $response = '-' . $response; // Add negative sign
        array_push($tbAmountConverted, $response);
        // echo 'Amount Converted <br>';
    }
    // if match in the SUBTOTAL
    if (preg_match('/(SUBTOTAL\s.*)/', $transaction, $matches)) {
        $subTotal = $matches[0];
        array_push($tbSaleDate, ' ');
        array_push($tbPostDate, ' ');
        array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $subTotal));
        array_push($tbAmount, explode("SUBTOTAL", $subTotal)[1]);
        if ($counterBalance != 0) {
          $amount = intval(explode("SUBTOTAL", $subTotal)[1]);
          $response = convertCurrency($from_currency,$to_currency,$amount);
          array_push($tbAmountConverted, $response);

      }
    }

    // if match in the TOTAL
    if (preg_match('/(TOTAL\s.*)/', $transaction, $matches)) {
      $total = $matches[0];
      $counterBalance = $counterBalance + 1;
      array_push($tbSaleDate, ' ');
      array_push($tbPostDate, ' ');
      array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $total));
      array_push($tbAmount, explode("TOTAL", $total)[1]);
      // echo 'Total <br>';
      if ($counterBalance != 0) {
        $amount = intval(explode("SUBTOTAL", $subTotal)[1]);
        $response = convertCurrency($from_currency,$to_currency,$amount);
        array_push($tbAmountConverted, $response);
        // echo 'Amount Converted Total <br>';
      }

      // break;

      // count the balance
      // echo $counterBalance . ' < ' . count($balanceArr) . '<br>';
      if($counterBalance < count($balanceArr)){

        // // add new spacing for new transaction
        // array_push($tbSaleDate, '');
        // array_push($tbPostDate, '');
        // array_push($tbTransactionDetails, '');
        // array_push($tbAmount, '');

        // // for new transaction
        // array_push($tbSaleDate, 'Sales Date');
        // array_push($tbPostDate, 'Post Date');
        // array_push($tbTransactionDetails, 'Transaction Details');
        // array_push($tbAmount, 'Amount');

        // $amount = explode($tbAmount, $balanceArr[$counterBalance]);
        // $response = convertCurrency($from_currency,$to_currency,$amount);
        // array_push($tbAmountConverted, explode("TOTAL", $total)[1]);

        array_push($tbSaleDate, ' ');
        array_push($tbPostDate, ' ');
        array_push($tbTransactionDetails, 'PREVIOUS STATEMENT BALANCE '.$CardNumber);
        array_push($tbAmount, $balanceArr[$counterBalance]);
        // echo $balanceArr[$counterBalance];
          $response = convertCurrency($from_currency,$to_currency, intval($balanceArr[$counterBalance]));
          // echo 'PREVIOUS STATEMENT BALANCE <br>';
          array_push($tbAmountConverted, $response);
      }
    }
  }

  // echo $newTransactionCtr;

  // echo $newTransactionCtr;
  array_shift($tbAmountConverted);
  // print_r($tbAmountConverted);
  // echo "<br>";
  // print_r($tbPostDate);
  // echo "<br>";
  // print_r($tbTransactionDetails);
  // echo "<br>";
  // print_r($tbAmount);
  foreach($tbTransactionDetails as $transaction){

      if(preg_match('/(\s.*)/', $transaction, $matches)){
        // echo preg_replace('/(\n.*)/', '', $transaction);
        // echo $transaction;
      }

      // echo $transaction;
  }
  $counterBalance = 0;
  // echo $newTransactionCtr;
  // header('Location: export.php?newTransactionCtr=' . $newTransactionCtr);
?>

<br>
<div style="margin-left: 15%">
<!-- export button -->
<a href="export.php?tbSaleDate=<?php echo urlencode(serialize($tbSaleDate));?>
                    &tbPostDate=<?php echo urlencode(serialize($tbPostDate)); ?>
                    &tbTransactionDetails=<?php echo urlencode(serialize($tbTransactionDetails)); ?>
                    &tbAmount=<?php echo urlencode(serialize($tbAmount)); ?>
                    &statementDate=<?php echo urlencode(serialize($statementDate))?>
                    &cardNumber=<?php echo urlencode(serialize($CardNumber))?>
                    &outstandingBalance=<?php echo urlencode(serialize($outstandingBalance))?>
                    &tbAmountConverted=<?php echo urlencode(serialize($tbAmountConverted))?>
                    &counterarray=<?php echo $counterarray?>
                    &name=<?php echo $nameOnly?>"
                    class ="export-button">EXPORT</a>
                    </div>

<div>

  <!-- LOCAL transaction -->
  <div  class="column">
    <h2><center>LOCAL CURRENCY(PHP)</center></h2>
    <table style="margin-top: 2%;" id="customers">
      <tr>
        <th>Sales Date</th>
        <th>Post Date</th>
        <th>Transaction Details</th>
        <th>Amount</th>
      </tr>
      <tr>
        <?php

        $counterarray = 0;

        //Sale Date
        echo "<td>";
        array_push($tbSaleDate, ' ');
        echo "</td>";
        //Post Date
        echo "<td>";
        array_push($tbPostDate, ' ');
        echo "</td>";
        //Transaction Details
        echo "<td>";
        array_push($tbTransactionDetails, 'PREVIOUS STATEMENT BALANCE');
        echo "<b> PREVIOUS STATEMENT BALANCE </b>";
        array_push($tbTransactionDetails, $CardNumber);
        echo "<br>", $CardNumber;

        echo "</td>";
        //Amount
        echo "<td style='text-align: right'>";
        array_push($tbAmount, $balanceArr[$counterBalance]);
        echo "<b> $balanceArr[$counterBalance]</b>";
        echo "</td>";
        ?>
      </tr>
      <?php
      // this is for printing the result in the web
      foreach($transactionDetails as $x => $transaction){
        //New Row
        echo "<tr>";

        $date = preg_split('/(\s)/', $dates[$x]);
        //Sale Date
        echo "<td>";
        array_push($tbSaleDate, $date[0]);
        echo $date[0];
        echo "</td>";
        //Post Date
        echo "<td>";
        array_push($tbPostDate, $date[1]);
        echo $date[1];
        echo "</td>";

        $counterarray = $counterarray + 1;

        $referenceCount = preg_split('/(Reference:|500.00 SINGAPORE DOLLARS|SUBTOTAL)\s.*?/', $transaction);
        $referenceCounts = preg_split('/(Reference:|SUBTOTAL)\s.*?/', $transaction);
        $amounts = preg_split('/(\s)/', $referenceCount[0]);
        $amountCount = count($amounts);

        //Transaction Details
        echo "<td>";

        $excludeValue = '500.00';

        // Use preg_replace to remove the amount from the string
        // $pattern = '/\d+(?:,\d+)?\.\d+/';

        // remove the amount and except the 500.00 in SINGAPORE DOLLAR
        $pattern = '/\b(?!500\.00)\d+(?:,\d+)?\.\d+\b/';
        $referenceCounts[0] = preg_replace($pattern, ' ', $referenceCounts[0]);
        array_push($tbTransactionDetails, preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]));
        echo preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]);
        if (preg_match('/\bReference:\s*(\S+)/', $transaction, $matches)) {
            $reference = $matches[0];
            // if($referenceCounts[1] == " "){
            //   echo "500.00";
            // }
            echo $reference;
            $tbTransactionDetails[$x + 1] = preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]) . "\n" . $reference;
        }
        // print_r($referenceCounts[1]);

        echo "</td>";

        //Amount
        echo "<td style='text-align: right'>";
        array_push($tbAmount, $amounts[$amountCount - 4]);
        echo $amounts[$amountCount - 4];
        echo "</td>";

        //If SubTotal found a match on Transaction Details
        if (preg_match('/(SUBTOTAL\s.*)/', $transaction, $matches)) {
            $subTotal = $matches[0];

            //New Row
            echo "<tr>";

              //Sale Date
              echo "<td>";
              array_push($tbSaleDate, ' ');
              echo "</td>";
              //Post Date
              echo "<td>";
              array_push($tbPostDate, ' ');
              echo "</td>";
              //Transaction Details
              echo "<td>";
              array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $subTotal));
              echo preg_replace('/\d+(?:,\d+)?\.\d+/', '', $subTotal);
              echo "</td>";
              //Amount
              echo "<td style='text-align: right'>";
              array_push($tbAmount, explode("SUBTOTAL", $subTotal)[1]);
              echo explode("SUBTOTAL", $subTotal)[1];
              echo "</td>";

            echo "</tr>";
        }
        $cont = $cont + 1;
        //If Total found a match on Transaction Details
        if (preg_match('/(TOTAL\s.*)/', $transaction, $matches)) {
            $total = $matches[0];
            // echo $counterBalance."<br>";
            $counterBalance = $counterBalance + 1;

            //New Row
            echo "<tr>";

              //Sale Date
              echo "<td>";
              array_push($tbSaleDate, ' ');
              echo "</td>";

              //Post Date
              echo "<td>";
              array_push($tbPostDate, ' ');
              echo "</td>";

              //Transaction Details
              echo "<td>";
              array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $total));
              echo preg_replace('/\d+(?:,\d+)?\.\d+/', '', "<b>$total</b>");
              echo "</td>";

              //Amount
              echo "<td style='text-align: right'>";
              array_push($tbAmount, explode("TOTAL", "<b>$total</b>")[1]);
              $tolamount = explode("TOTAL", $total)[1];
              echo "<b> $tolamount</b>";
              echo "</td>";
            echo "</tr>";

            break;

            // echo "<tr>";
            //   echo "<td class='first' colspan=4>";
            //   echo "</td>";
            // echo "</tr>";

            //Showing Previous Statement Balance
            // if($counterBalance < count($balanceArr)){
            //   // echo $counterBalance."<br>";
            //   // echo count($balanceArr);
            //   echo "<tr>";
            //     array_push($tbSaleDate, 'Sales Date');
            //     array_push($tbPostDate, 'Post Date');
            //     array_push($tbTransactionDetails, 'Transaction Details');
            //     array_push($tbAmount, 'Amount');
            //     echo "<th>Sales Date</th>";
            //     echo "<th>Post Date</th>";
            //     echo "<th>Transaction Details</th>";
            //     echo "<th>Amount</th>";
            //   echo "</tr>";

            //     //New Row
            //     echo "<tr>";

            //     //Sale Date
            //     echo "<td>";
            //     array_push($tbSaleDate, ' ');
            //     echo "</td>";

            //     //Post Date
            //     echo "<td>";
            //     array_push($tbPostDate, ' ');
            //     echo "</td>";

            //     //Transaction Details
            //     echo "<td>";
            //     array_push($tbTransactionDetails, 'PREVIOUS STATEMENT BALANCE');
            //     echo "<b> PREVIOUS STATEMENT BALANCE </b>";
            //     echo "<br>", $CardNumber;
            //     echo "</td>";

            //     //Amount
            //     echo "<td style='text-align: right'>";
            //     array_push($tbAmount, $balanceArr[$counterBalance]);
            //     echo "<b>$balanceArr[$counterBalance]</b>";
            //     echo "</td>";

            //     echo "</tr>";

            // }

        }

        echo "</tr>";


      }

      // index count of transaction Details
      // echo "INDEX COUNT: ",$cont;

      ?>
    </table>
  </div>
  <?php
  // echo $counterarray;
  ?>

  <!-- FOREIGN transaction -->
  <div  class="column">
    <h2><center>FOREIGN CURRENCY</center></h2>
    <table style="margin-top: 2%;" id="customers">
      <tr>
        <th>Sales Date</th>
        <th>Post Date</th>
        <th>Transaction Details</th>
        <th>Amount</th>
        <th>Amount <br> in (PHP)</th>
      </tr>
      <tr>
        <?php
          //Sale Date
          echo "<td>";
            array_push($tbSaleDate, ' ');
          echo "</td>";

          //Post Date
          echo "<td>";
            array_push($tbPostDate, ' ');
          echo "</td>";

          //Transaction Details
          echo "<td>";
            array_push($tbTransactionDetails, 'PREVIOUS STATEMENT BALANCE');
            echo "<b> PREVIOUS STATEMENT BALANCE </b>";
            array_push($tbTransactionDetails, $CardNumber);
            echo "<br>", $CardNumber;
          echo "</td>";

          //Amount
          echo "<td style='text-align: right'>";
            array_push($tbAmount, $balanceArr[$counterBalance]);
            echo "<b> $balanceArr[$counterBalance]</b>";
          echo "</td>";

          // Converted Amount
          echo "<td>";
            $from_currency = "SGD";
            $to_currency = "PHP";
            $amount = abs(intval($balanceArr[$counterBalance])); // Getting the absolute amount value

            // $currentDate = date('Y-m-d');
            // $response = convertCurrency($from_currency,$to_currency,$amount, $currentDate); // Getting the amount converted
            echo  $tbAmountConverted[0] ;
            array_push($tbCoverted, ' ');
          echo "</td>";
        ?>
      </tr>

      <?php
      $ctr = 1;
      foreach($transactionDetails as $x => $transaction){

        if($x >= $counterarray){

          // echo $transaction;
          // echo $x."\n";
          //New Row
          echo "<tr>";

            $date = preg_split('/(\s)/', $dates[$x]);
            //Sale Date
            echo "<td>";
              array_push($tbSaleDate, $date[0]);
              echo $date[0];
            echo "</td>";
            //Post Date
            echo "<td>";
              array_push($tbPostDate, $date[1]);
              echo $date[1];
            echo "</td>";

            $referenceCount = preg_split('/(Reference:|500.00 SINGAPORE DOLLARS|SUBTOTAL)\s.*?/', $transaction);
            $referenceCounts = preg_split('/(Reference:|SUBTOTAL)\s.*?/', $transaction);
            $amounts = preg_split('/(\s)/', $referenceCount[0]);
            $amountCount = count($amounts);

            //Transaction Details
            echo "<td>";

            $excludeValue = '500.00';

            // Use preg_replace to remove the amount from the string
            // $pattern = '/\d+(?:,\d+)?\.\d+/';

            // remove the amount and except the 500.00 in SINGAPORE DOLLAR
            $pattern = '/\b(?!500\.00)\d+(?:,\d+)?\.\d+\b/';
            $referenceCounts[0] = preg_replace($pattern, ' ', $referenceCounts[0]);
            array_push($tbTransactionDetails, preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]));
            echo preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]);
            if (preg_match('/\bReference:\s*(\S+)/', $transaction, $matches)) {
                $reference = $matches[0];
                // if($referenceCounts[1] == " "){
                //   echo "500.00";
                // }
                echo $reference;
                $tbTransactionDetails[$x + 1] = preg_replace('/(SUBTOTAL|TOTAL)\s.*?/', '', $referenceCounts[0]) . "\n" . $reference;
            }
            // print_r($referenceCounts[1]);

            echo "</td>";

            //Amount
            echo "<td style='text-align: right'>";
              array_push($tbAmount, $amounts[$amountCount - 4]);
              echo $amounts[$amountCount - 4];
            echo "</td>";

            // Converted Amount
            echo "<td style='text-align: right'>";
            // if (preg_match('/(SINGAPORE DOLLARS)\s.*?/', $transaction))
              $from_currency = "SGD";
              $to_currency = "PHP";
              $amount = abs(intval($amounts[$amountCount - 4])); // Getting the absolute amount
              $isNegative = intval($amounts[$amountCount - 4]) < 0; // Checking if negative
              // $datePost = date_create($date[1]);
              // $datePost = date_format($datePost, 'Y-m-d');
              // $response = convertCurrency($from_currency,$to_currency,$amount,$datePost); // Getting the convertion amount
              // if ($isNegative) // Checking if Negative
              //   $response = '-' . $response; // Add negative sign
              // echo "<b> $response </b>";
              echo $tbAmountConverted[$ctr];
              array_push($tbCoverted, ' ');
            echo "</td>";
          echo "</tr>";
          $ctr += 1;

          // If SubTotal found a match on Transaction Details
          if (preg_match('/(SUBTOTAL\s.*)/', $transaction, $matches)) {
            $subTotal = $matches[0];

            //New Row
            echo "<tr>";

              //Sale Date
              echo "<td>";
                array_push($tbSaleDate, ' ');
              echo "</td>";

              //Post Date
              echo "<td>";
                array_push($tbPostDate, ' ');
              echo "</td>";

              //Transaction Details
              echo "<td>";
                array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $subTotal));
                echo preg_replace('/\d+(?:,\d+)?\.\d+/', '', $subTotal);
              echo "</td>";

              //Amount
              echo "<td style='text-align: right'>";
                array_push($tbAmount, explode("SUBTOTAL", $subTotal)[0]);
                echo explode("SUBTOTAL", $subTotal)[1];
              echo "</td>";

              // Converted
              echo "<td style='text-align: right'>";
                $from_currency = "SGD";
                $to_currency = "PHP";
                $amount = abs(intval(explode("SUBTOTAL", $subTotal)[1])); // Getting the absolute amount value

                // $response = convertCurrency($from_currency,$to_currency,$amount); // Getting the amount converted
                // echo "<b> $response </b>";
                echo $tbAmountConverted[$ctr];
                array_push($tbCoverted, ' ');
              echo "</td>";

            echo "</tr>";
          }

          //If Total found a match on Transaction Details
          if (preg_match('/(TOTAL\s.*)/', $transaction, $matches)) {
            $total = $matches[0];
            $counterBalance = $counterBalance + 1;

            //New Row
            echo "<tr>";
              //Sale Date
              echo "<td>";
                array_push($tbSaleDate, ' ');
              echo "</td>";

              //Post Date
              echo "<td>";
                array_push($tbPostDate, ' ');
              echo "</td>";

              //Transaction Details
              echo "<td>";
                array_push($tbTransactionDetails, preg_replace('/\d+(?:,\d+)?\.\d+/', '', $total));
                echo preg_replace('/\d+(?:,\d+)?\.\d+/', '', "<b>$total</b>");
              echo "</td>";

              //Amount
              echo "<td style='text-align: right'>";
                array_push($tbAmount, explode("TOTAL", "<b>$total</b>")[1]);
                $tolamount = explode("TOTAL", $total)[1];
                echo "<b> $tolamount</b>";
              echo "</td>";

              // Converted
              echo "<td style='text-align: right'>";
                $from_currency = "SGD";
                $to_currency = "PHP";
                $amount = abs(intval($tolamount)); // Getting the absolute value

                // $response = convertCurrency($from_currency,$to_currency,$amount); // Getting the amount converted
                // echo "<b> $response </b>";
                echo $tbAmountConverted[$ctr];
                array_push($tbCoverted, ' ');
              echo "</td>";

            echo "</tr>";

            //Showing Previous Statement Balance
            // if($counterBalance < count($balanceArr)){

            //   //New Row
            //   echo "<tr>";

            //     //Sale Date
            //     echo "<td>";
            //       array_push($tbSaleDate, ' ');
            //     echo "</td>";

            //     //Post Date
            //     echo "<td>";
            //       array_push($tbPostDate, ' ');
            //     echo "</td>";

            //     //Transaction Details
            //     echo "<td>";
            //       array_push($tbTransactionDetails, 'PREVIOUS STATEMENT BALANCE');
            //       echo "<b> PREVIOUS STATEMENT BALANCE </b>";
            //       echo "<br>", $CardNumber;
            //     echo "</td>";

            //     //Amount
            //     echo "<td style='text-align: right'>";
            //       array_push($tbAmount, $balanceArr[$counterBalance]);
            //       echo "<b>$balanceArr[$counterBalance]</b>";
            //     echo "</td>";

            //   echo "</tr>";
            // }
            break;
          }

        }
      }


      ?>
    </table>
  </div>
</div>

<br>
<br>
<br>
<br>
<br>
<br>

<style>
  .first {
    border: 0;
    background-color: white;

  }

  .export-button{
    width: 300px;
    max-width: 100%;
    color: #00B643;
    padding: 15px;
    background: #CDFFDF;
    border-radius: 10px;
    border: 2px solid #00B643;
    cursor: pointer;
    font-weight: bold;
  }

  .export-button:hover{
    background: #00B643;
    font-weight: bold;
    color: #fff;
  }

  * {
    box-sizing: border-box;
  }
  /* Create two equal columns that floats next to each other */
  .column {
    font-family: Arial, Helvetica, sans-serif;
    float: left;
    width: 50%;
    padding: 10px;
    line-height: 1.3;
  }

  /* Clear floats after the columns */
  .row:after {
    content: "";
    display: table;
    clear: both;
  }
  #customers {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
      width: 75%;
      margin: auto;
  }

  #customers td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #customers tr:nth-child(even){background-color: #f2f2f2;}

  #customers tr:hover {background-color: #ddd;}

  #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #04AA6D;
    color: white;
  }
</style>
