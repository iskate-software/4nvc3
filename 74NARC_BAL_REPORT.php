<?php

session_start() ;
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

// get the appropriate dates 

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;

mysql_select_db($database_tryconnection, $tryconnection);
$startdate1="SELECT STR_TO_DATE('$startdate','%m/%d/%Y') AS STARTDATE";
$startdate2=mysql_query($startdate1, $tryconnection) or die(mysql_error());
$startdate3=mysql_fetch_array($startdate2);
$startdate = $startdate3['STARTDATE'] ;

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;

$enddate1="SELECT STR_TO_DATE('$enddate','%m/%d/%Y') AS ENDDATE";
$enddate2=mysql_query($enddate1, $tryconnection) or die(mysql_error());
$enddate3=mysql_fetch_array($enddate2);
$enddate = $enddate3['ENDDATE'] ;

// and figure the number of days between them.

$num_days = ceil(abs(strtotime($enddate) - strtotime($startdate))/86400 ) + 1;
//echo ' days = ' . $num_days ;
// Make a temporary table to tabulate all the transactions.

$Step_1 = "DROP TABLE IF EXISTS NARC_BAL" ;
$QStep1 = mysql_query($Step_1, $tryconnection) or die(mysql_error()) ;

$Step_2 = "CREATE TABLE NARC_BAL (ITEM CHAR(8),INVDTE DATE,  BLOCK INT(3),QTYREM_IN FLOAT(8,2), INBOUND FLOAT(8,2), QTY_DRAWN FLOAT (6,2), PATIENT VARCHAR(15), QTYREM FLOAT(8,2))";
$QStep2 = mysql_query($Step_2, $tryconnection) or die(mysql_error()) ;

// Identify the relevant drugs.
$prep0 = "SELECT COUNT(DISTINCT(ITEM)) AS NUMITEM FROM NARCPUR WHERE ACTIVE <> 9" ;
$Q_prep0 = mysql_query($prep0, $tryconnection) or die(mysql_error()) ;
$row_prep0 = mysql_fetch_assoc($Q_prep0) ;
$num_drugs = $row_prep0['NUMITEM'];
// echo ' No of drugs = ' . $num_drugs ;
$block_num = intval($num_drugs / 3) ;
//echo ' blocks = ' . $block_num ;
$over = ($num_drugs % 3);
//echo ' modulus = ' . $over ;
$Select_em = "SELECT DISTINCT ITEM FROM NARCPUR WHERE ACTIVE <> 9 ORDER BY ITEM" ;
$Query_Select = mysql_query($Select_em, $tryconnection) or die(mysql_error()) ;


$master_block = 1 ;
$item = 1 ;
$block_units = $num_days * 3 ; // The number of cells to fill out the week.
$cell_count = 0 ; // To track the number of cells done so far.

while ($row_drug = mysql_fetch_assoc($Query_Select)) {

 $drug = $row_drug['ITEM'] ; 
 $Open_bal = "SELECT SUM(QTYREM)  AS OPENING FROM NARCPUR WHERE ITEM = '$drug' AND DATEPURCH < '$startdate'";
 $Query_open = mysql_query($Open_bal, $tryconnection) or die(mysql_error()) ;
 $row_bal = mysql_fetch_assoc($Query_open) ;
 $open = $row_bal['OPENING'] ;
 
 // Then add back in everything which has been taken out after the last date..
 $put_back = "SELECT SUM(DRAWN) AS RETURNED FROM NARCLOG WHERE ITEM = '$drug' AND INVDTE >='$startdate'  AND INVDTE <= '$enddate'" ;
 $Q_putback = mysql_query($put_back, $tryconnection) or die(mysql_error()) ;
 $row_balPB = mysql_fetch_assoc($Q_putback) ;
 $return = $row_balPB['RETURNED'] ;
 $open = $open + $return ;
 
 $shipped = "SELECT ITEM,QTY, DATEPURCH FROM NARCPUR WHERE ITEM = '$drug' AND DATEPURCH >= '$startdate' AND DATEPURCH <= '$enddate'  AND QTY <> 0 ORDER BY DATEPURCH " ;
 $Qship = mysql_query($shipped, $tryconnection) or die(mysql_error()) ;
 

// set up one array for the purchases, and one for the dates of purchase

 // Initialize the purchase arrays, and the "Block" no, which determines where the item appears in the report .
unset($purch) ;
unset($date_purch) ;
$purch = array() ;
$date_purch = array() ;
while ($row_Qship = mysql_fetch_assoc($Qship)) {
  $drug1 = $row_Qship['ITEM']  ; 
  $purch[] = $row_Qship['QTY']  ;
  $date_purch[] = $row_Qship['DATEPURCH'] ;
  
 }  // pack the purchase arrays

 // now do the usage 
  unset($drawn) ;
  unset($date_drawn) ;
  unset($used_on) ;
  $drawn = array() ;
  $date_drawn = array() ;
  $used_on = array() ;
  $use = "SELECT INVDTE, DRAWN, PETID FROM NARCLOG WHERE ITEM = '$drug' AND INVDTE >= '$startdate' AND INVDTE <= '$enddate' ORDER BY INVDTE" ;
  $Quse = mysql_query($use, $tryconnection) or die(mysql_error()) ;
  
  while ($row_Qdrawn = mysql_fetch_assoc($Quse)) {
   $drawn[] = $row_Qdrawn['DRAWN'] ;
   $date_drawn[] = $row_Qdrawn['INVDTE'] ;
   $used_on[] = $row_Qdrawn['PETID'] ;
  } // pack the drawn arrays.

 //  build the daily totals. Walk from day the first to day the end, even if no usage. (But drop Sundays).
 $this_day = $startdate ;
 $ship = 0 ;
 $used = 0 ;

while ($this_day <= $enddate) {

        while ( array_search($this_day,$date_purch) !== FALSE)  {
         
             $key = array_search($this_day,$date_purch); 
             $ship = $ship + $purch[$key] ;
             $date_purch[$key] = ' ' ;
             $purch[$key] = 0 ; 
         } // found a hit this day

    // Then check the usage. Blank date as per shipments, for the same reason.
       while ( array_search($this_day,$date_drawn) !== FALSE) {
             $key =array_search($this_day,$date_drawn) ;
             $used = $used + $drawn[$key] ;
             $date_drawn[$key] = ' ' ; 
             $drawn[$key] = 0 ;
             
       } // search the drawn array.
    $close =  $open + $ship - $used ;
    // So we can create the table row
    $Make_a_row = "INSERT INTO NARC_BAL (ITEM,INVDTE,BLOCK,QTYREM_IN,INBOUND, QTY_DRAWN,QTYREM) 
                   VALUES ('$drug','$this_day', '$item', '$open', '$ship', '$used', '$close')" ;
                   $Put_it_out = mysql_query($Make_a_row, $tryconnection) or die(mysql_error()) ;
                   $open = $close ;
                 // reset the daily totals
                   $ship = 0 ;
                   $used = 0 ;
                   $item = $item + 3 ;
                   
    // now bump the date, check it is not Sunday, and keep on going.
    $this_day = date('Y-m-d', strtotime($this_day.'+ 1 days')) ;
    // Check for Sundays...
    if (date('w')== 0) {
       $this_day = date('Y-m-d', strtotime($this_day.'+ 1 days')) ;
    } // is it Sunday?
 
 } // check this drug within the required dates.
   if (intval($master_block/3)*3 != $master_block) {
      $item = $master_block + 1 ;  
      $master_block++ ;
    }
    else {
      $master_block = $master_block + 16 ;
      $item = $master_block ;  
    }
    
} // end of each drug check.

$balance = "SELECT * FROM NARC_BAL ORDER BY BLOCK,INVDTE" ;
$Q_balance = mysql_query($balance, $tryconnection) or die(mysql_error()) ;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.5" />
<title>Narcotic Balancing</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<style type="text/css">
body {
background-color:#FFFFFF;
overflow:auto;
}
#prtclosebuttons{
display:block;
}

</style>
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body>

<?php  
$blocks = 4 ;
$items = 0 ;
$page = 1 ;
$first = 1 ;
$linecount = 99 ;
unset($drug1) ;
unset($drug2) ;
unset($drug3) ;

$drugs = 0 ;
$col1 = 'a' ;
$col2 = 'a' ;
$col3 = 'a' ;

$break = 0 ;
while ($row_Qbal = mysql_fetch_assoc($Q_balance)) {
// fill out the arrays, padding appropriate columns in the last block with blanks if the total 
// number of drugs is not exactly divisible by 3
  if ($items == 0){
  $i = 0 ;
// echo $i. ' - ' . $items .'  ' ;
  $i++ ;
    $drug1 = array() ;
    $drug2 = array_fill(0,6,' ') ;
    $drug3 = array_fill(0,6,' ') ;
     if ($col1 != $row_Qbal['ITEM'] ) {$col1 = $row_Qbal['ITEM'] ; $drugs++ ;}
    //     if ($drugs >= $num_drugs) {echo ' col 1 ' . $col1 . ' ' .$row_Qbal['ITEM']. ' drug '.$drugs;}
    $drug1[0] = $row_Qbal['INVDTE'] ;
    $drug1[1] = $row_Qbal['ITEM'] ;
    $drug1[2] = $row_Qbal['QTYREM_IN'] ;
    $drug1[3] = $row_Qbal['INBOUND'] ;
    $drug1[4] = $row_Qbal['QTY_DRAWN'] ;
    $drug1[5] = $row_Qbal['QTYREM'] ;
    $items++ ;
    
  } //$items = 1
  else if ($items ==1){
    if ($drugs >= $num_drugs) { //echo ' over ' . $drugs ;
      $items = 3 ;
//     continue ;
    }
    
     else {
      if ($col2 != $row_Qbal['ITEM'] ) {$col2 = $row_Qbal['ITEM'] ; $drugs++ ;}
         if ($drugs >= $num_drugs) {//echo ' col 1 ' . $col1 . ' ' .$row_Qbal['ITEM']. ' drug '.$drugs . ' ->';
          $items = 3;}
      $drug2[0] = $row_Qbal['INVDTE'] ;
      $drug2[1] = $row_Qbal['ITEM'] ;
      $drug2[2] = $row_Qbal['QTYREM_IN'] ;
      $drug2[3] = $row_Qbal['INBOUND'] ;
      $drug2[4] = $row_Qbal['QTY_DRAWN'] ;
      $drug2[5] = $row_Qbal['QTYREM'] ;
      $items++ ;
    }
   
    
  } //$items = 2
  else {
    if ($drugs >= $num_drugs) {// echo ' over2 ' . $drugs ;
      $items = 3 ;
//      continue ;
    }
    else
    {
     if ($col3 != $row_Qbal['ITEM'] ) {$col3 = $row_Qbal['ITEM'] ; $drugs++ ;
     
     // echo ' 3-> ' . $col3 .  $drugs ;
     }
     $drug3[0] = $row_Qbal['INVDTE'] ;
     $drug3[1] = $row_Qbal['ITEM'] ;
     $drug3[2] = $row_Qbal['QTYREM_IN'] ;
     $drug3[3] = $row_Qbal['INBOUND'] ;
     $drug3[4] = $row_Qbal['QTY_DRAWN'] ;
     $drug3[5] = $row_Qbal['QTYREM'] ;
     }
    $items++ ;
    
  } // $items = 3
//  $linecount++ ;
//echo ' count ' . $drugs ;
// Start of the top of the page
  
if ($blocks == 4  && $items >= 3) {
 $blocks = 0 ;
 $linecount = -1 ;
 echo
  '<div style="width:955px; height:2100px; overflow:auto;" id="realpreview">
  <table width="954" border="1" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF">
  <caption align="top">'.date("Y-m-d").'&nbsp;&nbsp;&nbsp;'.'NARCOTIC BALANCING'
  .'&nbsp;&nbsp;&nbsp;Page '. $page .'</caption> ' ; $page++ ; 
  echo  
//  '<div style="width":955px; height=20px; id="prtclosebuttons">
//      <td width="287">&nbsp;</td>
//      <td class="Verdana12B">&nbsp;</td>
//      <td width="212" align="right">
//      <input type="button" value="PRINT" onclick="window.print();"/>
//      <input type="button" value="CLOSE" onclick="history.back()"/>
//      </td>
//      </div><tr>
    '<td class="Verdana10" width="8%">DATE</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
  </tr>' ;
  } 

 if (($linecount == -1 || $linecount >= $num_days) && $items >= 3) {
 
// $items = 0 ;

      
    if ($break == 30) {
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> ';
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> ';  
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana10" colspan="13">&nbsp;</td></tr> '; 
      echo '<tr> <td class="Verdana15" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date("Y-m-d").'&nbsp;&nbsp;&nbsp;NARCOTIC BALANCING&nbsp;&nbsp;&nbsp;Page 2</td></tr> '; 
      echo '<tr> 
    <td class="Verdana10" width="8%">DATE</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
    <td class="Verdana10">Open</td>
    <td class="Verdana10">In</td>
    <td class="Verdana10">Out</td>
    <td class="Verdana10">Rem</td>
  </tr>';  
      
    }
    echo
      '<tr>
    <td class="Verdana10" width="8%">&nbsp;</td>
    <td class="Verdana9Blue" width="7%">'.$drug1[1].'</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana9Blue" width="7%">'.$drug2[1].'</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana9Blue" width="7%">'.$drug3[1].'</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
    <td class="Verdana10" width="7%">&nbsp;</td>
  </tr>' ;
  $linecount = 0 ;
  
  } 
  if ( $items >= 3) {
  echo '<tr>
    <td class="Verdana10">'. $drug1[0] . '</td>
    <td class="Verdana10">'. $drug1[2] . '</td>
    <td '; if ($drug1[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug1[3] . '</td>
    <td '; if ($drug1[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug1[4] . '</td>
    <td class="Verdana10">'. $drug1[5] . '</td>
    <td class="Verdana10">'. $drug2[2] . '</td>
    <td '; if ($drug2[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug2[3] . '</td>
    <td '; if ($drug2[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug2[4] . '</td>
    <td class="Verdana10">'. $drug2[5] . '</td>
    <td class="Verdana10">'. $drug3[2] . '</td>
    <td '; if ($drug3[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug3[3] . '</td>
    <td '; if ($drug3[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug3[4] . '</td>
    <td class="Verdana10">'. $drug3[5] . '</td>
  </tr> ' ;
  $linecount++ ; 
  $items = 0 ;
  unset($drug1) ;
  unset($drug2) ;
  unset($drug3) ;
  $break++ ;
  }
//  $blocks++ ;
  
  
  if ($blocks == 4){
     echo '</table>
     </div>';
    }
 } //while ($row_Qbal = mysql_fetch_assoc($Q_balance))

 // dump out the outstanding data, putting in blanks if nothing there.
 if ($items == 1) {
   unset($drug2) ;
   $drug2 = array_fill(0,6,' ') ;
   
    $items++;
 }
 if ($items == 2) {
   unset($drugs3) ;
   $drug3 = array_fill(0,6,' ') ;
 }
 
  echo '<tr>
    <td class="Verdana10">'. $drug1[0] . '</td>
    <td class="Verdana10">'. $drug1[2] . '</td>
    <td '; if ($drug1[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug1[3] . '</td>
    <td '; if ($drug1[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug1[4] . '</td>
    <td class="Verdana10">'. $drug1[5] . '</td>
    <td class="Verdana10">'. $drug2[2] . '</td>
    <td '; if ($drug2[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug2[3] . '</td>
    <td '; if ($drug2[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug2[4] . '</td>
    <td class="Verdana10">'. $drug2[5] . '</td><td class="Verdana10">'. $drug3[2] . '</td>
    <td '; if ($drug3[3] != 0){echo 'class="Verdana10Green"';} else {echo 'class="Verdana10"';} echo '>'. $drug3[3] . '</td>
    <td '; if ($drug3[4] != 0){echo 'class="Verdana10Red"';} else {echo 'class="Verdana10"';} echo '>'. $drug3[4] . '</td>
    <td class="Verdana10">'. $drug3[5] . '</td>
    </tr> ' ;
     echo '</table>
     </div>';
  
 ?>
</body>
</html>
