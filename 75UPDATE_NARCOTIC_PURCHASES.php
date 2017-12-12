<?php
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

$descrip = $_GET['descrip'] ;
$qty = $_GET['qty'] ;
$item = $_GET['item'] ;
$vpartno = $_GET['vpartno'] ;
$comment = $_GET['comment'] ;
$datepur = $_GET['datepur'] ;
$b4 = $_GET['qtyrem'] ;
//echo $datepur ;

$unixdate = date("Y-m-d",mktime(0,0,0,substr($datepur,0,2),substr($datepur,3,2),substr($datepur,6,4))) ;
echo $unixdate ;

$update = "INSERT INTO NARCPUR (ITEM,DESCRIP,B4,QTY,QTYREM,DATEPURCH,VENDOR,SEQ,COMMENT) 
VALUES ('$item', '$descrip', '$b4', '$qty', '$qty', '$unixdate','$vpartno','1','$comment' )" ;
$do_it = mysql_query($update, $tryconnection) or die(mysql_error()) ;

header("Location:NARCOTIC_DRUG_SEARCH.php") ;

?>