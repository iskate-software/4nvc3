<?php 
session_start();
require_once('../../tryconnection.php');


// ARSTAT program.
// This program prints out statements for individual or all clients. 
// The statement date is given by NOW().
// It asks for the ending dates for cash receipts and invoices which are to be
// included. This allows for retroactive printing of statements, excluding all
// invoices from $invdate on, and cash receipts from $cashdate onwards.
// As a result, statement can be sent late, but take into account payments which
// were received after the nominal month end. Or not. It depends on the clinic's protocols.
//
// Temporary tables TARCUST (arcusto), TARV (ararecv) and CASH (arcashr + cashdep) are created to
// minimize traffic loads, and prevent live updating of the records during the printout.
//
// First, it is conceivable that the client balance field in ARCUSTO may be incorrect. To account 
// for this, client balances are "Forced" to the totals of the receivables records. After that
// credit amounts are taken into account (it is assumed that the credit field in ARCUSTO is correct,
// so it can just be applied to the balance to give the true balance.)
// To keep subsequent joins minimalized, ARCUSTO is then extracted to a temporary file for 
// non zero balances.

mysqli_select_db($tryconnection, $database_tryconnection);


$query_CRITDATA = "SELECT * FROM CRITDATA LIMIT 1";
$CRITDATA = mysqli_query($tryconnection, $query_CRITDATA) or die(mysqli_error($mysqli_link));
$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);


$invdate=$_POST['invdate'];
$invdate="SELECT STR_TO_DATE('$invdate','%m/%d/%Y')";
$invdate=mysqli_query($tryconnection, $invdate) or die(mysqli_error($mysqli_link));
$invdate=mysqli_fetch_array($invdate);

$get_year = "SELECT YEAR('$invdate[0]')" ;
$query_year = mysqli_query($tryconnection, $get_year) or die(mysqli_error($mysqli_link)) ;
$row_year = mysqli_fetch_assoc($query_year) ;
$year = $row_year[0] ;

$cashdate=$_POST['cashdate'];
$cashdate="SELECT STR_TO_DATE('$cashdate','%m/%d/%Y')";
$cashdate=mysqli_query($tryconnection, $cashdate) or die(mysqli_error($mysqli_link));
$cashdate=mysqli_fetch_array($cashdate);

$balfwddate=$_POST['balfwddate'];
$balfwddate="SELECT STR_TO_DATE('$balfwddate','%m/%d/%Y')";
$balfwddate=mysqli_query($tryconnection, $balfwddate) or die(mysqli_error($mysqli_link));
$balfwddate=mysqli_fetch_array($balfwddate);

$stmtmonth=$_POST['stmtmonth'] ;
$stmtyear = $year ;

setlocale(LC_MONETARY, 'en_US');

$BALANCE1 = "DROP TEMPORARY TABLE IF EXISTS TAR1" ;
$BALANCE2 = "CREATE TEMPORARY TABLE TAR1 (CUSTNO FLOAT(7),COMPANY VARCHAR(50),INVDTE DATE, IBAL FLOAT(8,2)) SELECT CUSTNO, COMPANY, INVDTE, SUM(IBAL) AS IBAL FROM ARARECV WHERE IBAL <> 0 AND INVDTE <= '$invdate[0]' GROUP BY CUSTNO ";
$BALANCE3 = "UPDATE ARCUSTO SET BALANCE = 0 WHERE BALANCE <> 0" ;
$BALANCE4 = "UPDATE ARCUSTO JOIN TAR1 USING (CUSTNO) SET ARCUSTO.BALANCE = TAR1.IBAL" ;
$BALANCE5 = "UPDATE ARCUSTO SET BALANCE = BALANCE - CREDIT" ;
$BALANCE6 = "DROP TEMPORARY TABLE IF EXISTS TARCUST" ;
$BALANCE7 = "CREATE TEMPORARY TABLE TARCUST (CUSTNO FLOAT(7), TITLE VARCHAR(25), COMPANY VARCHAR (50), CONTACT VARCHAR(50), ADDRESS1 VARCHAR(60), ADDRESS2 VARCHAR(60), CITY VARCHAR(50), STATE CHAR(3), ZIP CHAR(12), COUNTRY VARCHAR(30), CREDIT FLOAT(8,2), BALANCE FLOAT(8,2)) SELECT CUSTNO, TITLE, COMPANY, CONTACT, ADDRESS1, ADDRESS2, CITY,STATE, ZIP, COUNTRY, CREDIT, BALANCE FROM ARCUSTO WHERE BALANCE <> 0 " ;
$BALANCE8 = "DELETE FROM TARCUST WHERE CREDIT = -BALANCE" ;
$Q_Balance1 = mysqli_query($tryconnection, $BALANCE1) or die(mysqli_error($mysqli_link));
$Q_Balance2 = mysqli_query($tryconnection, $BALANCE2) or die(mysqli_error($mysqli_link));
$Q_Balance3 = mysqli_query($tryconnection, $BALANCE3) or die(mysqli_error($mysqli_link));
$Q_Balance4 = mysqli_query($tryconnection, $BALANCE4) or die(mysqli_error($mysqli_link));
$Q_Balance5 = mysqli_query($tryconnection, $BALANCE5) or die(mysqli_error($mysqli_link));
$Q_Balance6 = mysqli_query($tryconnection, $BALANCE6) or die(mysqli_error($mysqli_link));
$Q_Balance7 = mysqli_query($tryconnection, $BALANCE7) or die(mysqli_error($mysqli_link));
$Q_Balance8 = mysqli_query($tryconnection, $BALANCE8) or die(mysqli_error($mysqli_link));

// Then, all the cash records are gathered from ARCASHR and CASHDEP and 
// summarised for each client. This allows both for removing payments on receivables
// if the clinic chooses to backdate the statements, and for showing the total payment
// received that month on each statement.


$CASH1 = "DROP TEMPORARY TABLE IF EXISTS CASH";
$CASH2 = "CREATE TEMPORARY TABLE CASH SELECT * FROM ARCASHR ORDER BY CUSTNO, INVNO, INVDTE ASC" ;
$CASH3 = "INSERT INTO CASH SELECT * FROM CASHDEP "; 
$Q_Cash1 = mysqli_query($tryconnection, $CASH1) or die(mysqli_error($mysqli_link));
$Q_Cash2 = mysqli_query($tryconnection, $CASH2) or die(mysqli_error($mysqli_link));
$Q_Cash3 = mysqli_query($tryconnection, $CASH3) or die(mysqli_error($mysqli_link));

// The receivables are then selected using the $invdate variable to exclude any late records.

$INVOICE1 = "DROP TEMPORARY TABLE IF EXISTS TARV" ;
$Q_Invoice1 = mysqli_query($tryconnection, $INVOICE1) or die(mysqli_error($mysqli_link));
$INVOICE2 = "CREATE TEMPORARY TABLE TARV SELECT * FROM ARARECV WHERE INVDTE <= '$invdate[0]' ORDER BY CUSTNO,INVDTE,INVNO" ;
$Q_Invoice2 = mysqli_query($tryconnection, $INVOICE2) or die(mysqli_error($mysqli_link));

// If the run is being backdated to the last month end, the above selection looks after everything 
// but the overdated payments in both the receivables file (TARV) and the cash file (CASH). 
// They have to be removed.
// First, all payments made on old invoices after the cash cut-off date have to be removed from TARV, 
// then all payments for invoices after the invoice cut-off date have to be trashed from CASH.
if ($cashdate[0] > $invdate[0] || $invdate[0] < date('Y-m-d')) {
  $TARV1 = "UPDATE TARV JOIN CASH USING (CUSTNO,INVDTE,INVNO) SET IBAL = IBAL + CASH.AMTPAID,TARV.AMTPAID = TARV.AMTPAID-CASH.AMTPAID WHERE CASH.DTEPAID > '$cashdate[0]'";
  $Q_Tarv1 = mysqli_query($tryconnection, $TARV1) or die(mysqli_error($mysqli_link));
  $CASH4 = "DELETE FROM CASH WHERE INVDTE > '$invdate[0]' ";
  $Q_Cash4 = mysqli_query($tryconnection, $CASH4) or die(mysqli_error($mysqli_link));
}
// Finally, we have clean data. So, work through the temporary client file, extracting the appropriate
// data.
// Start by preparing statement summary totals
  $GCurrent = 0 ;
  $GOver_30 = 0 ;
  $GOver_60 = 0 ;
  $GOver_90 = 0 ;
  $GOver_120 = 0 ;
  
  $what = getdate(strtotime($invdate[0])) ;
  $year = $what['year'] ;
  $month = $what['mon'] ;
  $Curdate = $year * 12 + $month ;
  
  $query_CLIENT = "SELECT CUSTNO, TITLE, COMPANY, CONTACT, ADDRESS1, ADDRESS2, CITY, STATE, ZIP, COUNTRY, CREDIT FROM TARCUST ORDER BY COMPANY ASC" ;
  $CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link)) ;
  $row_CLIENT = mysqli_fetch_assoc($CLIENT);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>STATEMENT OF ACCOUNT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
//window.print();
}

function OnClose()
{
self.close();
}

function bodyonunload()
{}

</script>

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

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->

<form method="post" action="" name="inv_preview" id="inv_preview" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<?php



//FOR EACH CLIENT DO:
 do { 

  // Check for cash
  $Is_Cash = "SELECT SUM(AMTPAID) AS AMTPAID FROM CASH WHERE CUSTNO = $row_CLIENT[CUSTNO]" ;
  $Q_Cash5 = mysqli_query($tryconnection, $Is_Cash) or die(mysqli_error($mysqli_link)) ;
  $row_CASH5 = mysqli_fetch_assoc($Q_Cash5) ;
  
//get the balance forward

  $query_BALFWD = "SELECT SUM(IBAL) AS BALFWD FROM TARV WHERE INVDTE <= '$balfwddate[0]' AND CUSTNO = $row_CLIENT[CUSTNO]";
  $BALFWD = mysqli_query($tryconnection, $query_BALFWD) or die(mysqli_error($mysqli_link));
  $row_BALFWD = mysqli_fetch_assoc($BALFWD);
//
  // Prepare the aging data
  // First, age all the receivables, figure the Balance Forward, then get the current for printing.
  $AGING = "SELECT INVNO, YEAR(INVDTE) AS INVYEAR, MONTH(INVDTE) AS INVMONTH, IBAL FROM TARV WHERE CUSTNO = '$row_CLIENT[CUSTNO]' ";
  $Q_Aged = mysqli_query($tryconnection, $AGING) or die(mysqli_error($mysqli_link)) ;
  $TOTRECV = 0 ;
  $Current = 0 ;
  $Over_30 = 0 ;
  $Over_60 = 0 ;
  $Over_90 = 0 ;
  $Over_120 = 0 ;
  //  
   while ($row1 = mysqli_fetch_assoc($Q_Aged) ) {
      $thisI = $row1['IBAL'] ;
      $TOTRECV = $TOTRECV + $thisI ;
      $AgeFactor =  $Curdate - (12 * $row1['INVYEAR'] + $row1['INVMONTH']) ;
	  
	  if ($AgeFactor <= 0 ) {
		$Current = $Current + $thisI ;
		$GCurrent = $GCurrent + $thisI ;
		}
	  elseif ($AgeFactor == 1 ) {
		$Over_30 = $Over_30 + $thisI ;
		$GOver_30 = $GOver_30 + $thisI ;
		 }
	  elseif ($AgeFactor == 2 ) {
		$Over_60 = $Over_60 + $thisI ;
		$GOver_60 = $GOver_60 + $thisI ;
		 }
	  elseif ($AgeFactor == 3 ) {
		$Over_90 = $Over_90 + $thisI ;
		$GOver_90 = $GOver_90 + $thisI ;
		}
	  elseif ($AgeFactor > 3 ) {
		$Over_120 = $Over_120 + $thisI ;
		$GOver_120 = $GOver_120 + $thisI ;
		}
  }
  // now the current.
  $RECEIVABLES = "SELECT INVNO, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, YEAR(INVDTE) AS INVYEAR, MONTH(INVDTE) AS INVMONTH, PONUM, ITOTAL, AMTPAID, IBAL, TAX FROM TARV WHERE CUSTNO = '$row_CLIENT[CUSTNO]' AND INVDTE > '$balfwddate[0]' ORDER BY INVDTE,INVNO ";
  $Q_Recv = mysqli_query($tryconnection, $RECEIVABLES) or die(mysqli_error($mysqli_link)) ;
  $row1 = mysqli_fetch_assoc($Q_Recv) ;
  
  $tax = 0 ;
  
  $ibal = $row_BALFWD['BALFWD'] ;

?>
<div style="width:855px; height:1050px; overflow:auto;" id="realpreview">
  <table width="854" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
    <tr>
      <td align="center" class="Verdana11B">&nbsp;</td>
      <td width="82" height="96" align="center" class="Verdana11B">&nbsp;</td>
      <td height="96" colspan="3" align="left" valign="top" class="Verdana10"><span style="font-family:Helvetica, sans-serif; font-size:35px;"><?php echo $row_CRITDATA['HOSPNAME']; ?></span><br />
        <span style="font-family:Helvetica, sans-serif; font-size:20px;"><?php echo $row_CRITDATA['HOSPPNAME']; ?></span><br />
        <span style="line-height:15px;"><?php echo $row_CRITDATA['HSTREET']; ?></span><br />
        <span style="line-height:15px;"><?php echo $row_CRITDATA['HCITY'].", ".$row_CRITDATA['HPROV']." ".$row_CRITDATA['HCODE']; ?></span> <br />
        <span style="line-height:15px;"><?php echo "(".$row_CRITDATA['HPACD'].") ".$row_CRITDATA['HPPHONE']; ?> Fax</span></td>
      <td width="42" height="96" align="left" valign="top" class="Verdana11B"><img src="../../IMAGES/BGROUNDS/HOSPICT copy.jpg" alt="hospital picture" name="hospict" width="158" height="90" class="hidden" id="hospict" /> </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td width="129" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    

    <tr>
      <td colspan="2" align="center"><?php echo date('m/d/Y'); ?></td>
      <td align="center">&nbsp;</td>
      <td align="center"><?php echo "(".$row_CRITDATA['HPACD'].") ".$row_CRITDATA['HPPHONE']; ?></td>
      <td colspan="2" align="right"><?php echo date("H:i:s"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    
    <tr>
      <td height="2" colspan="6"> <hr noshade="noshade" size="1" color="#000000"  /></td>
    </tr>
    <tr>
      <td width="87">&nbsp;</td>
      <td colspan="2" class="Verdana12B">&nbsp;</td>
      <td width="512" rowspan="2" align="left">
      <span class="Verdana14B">STATEMENT OF ACCOUNT FOR <?php echo date('F',mktime(0,0,0,$_POST['stmtmonth'],1,0)) .' '. $year;	?> </span><br />
      <div id="prtclosebuttons">
      <input type="button" value="PRINT" onclick="window.print();"/>
      <input type="button" value="CLOSE" onclick="self.close();"/>
      </div>      </td>
      <td height="20" colspan="2" align="right" valign="bottom" class=""><!--Admitting Doctor-->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td rowspan="3"></td>
      <td colspan="2" rowspan="3" class="Verdana12"><?php 		
		echo $row_CLIENT['TITLE'].' '.$row_CLIENT['CONTACT'].' '.$row_CLIENT['COMPANY'];
		echo "<br />";
		echo $row_CLIENT['ADDRESS1'];
		echo "<br />";
		echo $row_CLIENT['CITY'].", ".$row_CLIENT['STATE']."<br />".$row_CLIENT['ZIP'];
	  ?>      <br />      </td>
      <td height="20" colspan="2" align="right" class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="Verdana14B">&nbsp;</td>
      <td height="20" colspan="2" align="right" valign="bottom" class=""><!--Discharge Staff-->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="Verdana12">&nbsp;</td>
      <td height="20" colspan="2" align="right" class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td height="2" colspan="6"> <hr noshade="noshade" size="1" color="#000000"  /></td>
    </tr>
    <tr>
      <td class="Verdana12">&nbsp;</td>
      <td colspan="5" class="Verdana12">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="6" align="center" class="Verdana12">
      <table width="811" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100" height="25" align="center" valign="top" class="Verdana13B">Date</td>
          <td width="100" align="center" valign="top" class="Verdana13B">Invoice#</td>
          <td align="left" valign="top" class="Verdana13B">Details</td>
          <td width="120" align="right" valign="top" class="Verdana13"><strong>Amount</strong><!--<br />
            <span class="Verdana11">(Invoice Amount)</span>-->          </td>
          <td width="120" align="right" valign="top" class="Verdana13"><strong>Paid</strong><!--<br />
            <span class="Verdana11">(Since Charge)</span>-->          </td>
          <td width="120" align="right" valign="top" class="Verdana13"><strong>Balance</strong><!--<br />
            <span class="Verdana11">(Current Balance)</span>-->          </td>
        </tr>
        <tr <?php if ($row_BALFWD['BALFWD']==0) {echo "class='hidden'";} ?>>
          <td height="20" align="center" class="Verdana13">&nbsp;</td>
          <td align="center" class="Verdana13">&nbsp;</td>
          <td align="left" class="Verdana13">Balance Forward</td>
          <td align="right" class="Verdana13">&nbsp;</td>
          <td align="right" class="Verdana13">&nbsp;</td>
          <td align="right" class="Verdana13"><?php echo money_format('%(#1n',$row_BALFWD['BALFWD']); ?></td>
        </tr>
      
      <?php 
	  //FOR EACH INVOICE DO:
	  do { 
      $ibal = $ibal + $row1['IBAL'];


	  ?>
      
        <tr>
          <td height="20" align="center" class="Verdana13"><?php echo $row1['INVDTE']; ?></td>
          <td align="center" class="Verdana13"><?php echo $row1['INVNO']; ?></td>
          <td align="left" class="Verdana13"><?php echo $row1['PONUM']; ?></td>
          <td align="right" class="Verdana13"><?php echo $row1['ITOTAL']; ?></td>
          <td align="right" class="Verdana13"><?php echo $row1['AMTPAID']; ?></td>
          <td align="right" class="Verdana13"><?php if ($ibal != $row_BALFWD['BALFWD']) { echo money_format('%(#1n', $ibal); }?></td>
        </tr>
       <?php 
	   		$tax = $tax + $row1['TAX']; 
	   		} while ($row1 = mysqli_fetch_assoc($Q_Recv)); ?>
       
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="20">&nbsp;</td>
          <td>&nbsp;</td>
          <td align="left" class="Verdana13">Total payment received this month:</td>
          <td align="left" class="Verdana13">&nbsp;</td>
          <td align="right"><span class="Verdana13"><?php echo (empty($row_CASH5['AMTPAID']) ? "0.00" : $row_CASH5['AMTPAID']); ?></span></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="20">&nbsp;</td>
          <td>&nbsp;</td>
          <td class="Verdana13">Total HST invoiced this month:</td>
          <td align="right" class="Verdana13"><?php echo $tax; ?></td>
          <td align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32">&nbsp;</td>
          <td>&nbsp;</td>
          <td class="Verdana13">&nbsp;</td>
          <td align="right" class="Verdana13">&nbsp;</td>
          <td align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>      </td>
    </tr>
    <tr>
      <td colspan="6" align="center" class="Verdana12">
      <table width="814" border="0" cellspacing="0" cellpadding="0">
        <tr <?php if ($Current == 0 && $Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "class='hidden'";} ?>>
          <td width="119" height="31" align="right" valign="bottom" class="Verdana13B">Current</td>
          <td width="119" align="right" valign="bottom" class="<?php if ($Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13B";} ?>">Over 30</td>
          <td width="119" align="right" valign="bottom" class="<?php if ($Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13B";} ?>">Over 60</td>
          <td width="120" align="right" valign="bottom" class="<?php if ($Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13B";} ?>">Over 90</td>
          <td align="right" valign="bottom" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13B";} ?>">Over 120</td>
          <td align="right" valign="bottom" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13B";} ?>">&nbsp;</td>
        </tr>
        <tr <?php if ($Current == 0 && $Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "class='hidden'";} ?>>
          <td height="1" align="right" valign="top" class="Verdana13"><hr size="1" width="55" color="#000000" style="margin:0px;"/></td>
          <td height="1" align="right" valign="top" class="<?php if ($Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><hr size="1" width="60" color="#000000" style="margin:0px;"/></td>
          <td height="1" align="right" valign="top" class="<?php if ($Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><hr size="1" width="60" color="#000000" style="margin:0px;"/></td>
          <td height="1" align="right" valign="top" class="<?php if ($Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><hr size="1" width="60" color="#000000" style="margin:0px;"/></td>
          <td width="118" height="1" align="right" valign="top" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><hr size="1" width="60" color="#000000" style="margin:0px;"/></td>
          <td width="219" align="right" valign="top" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>">&nbsp;</td>
        </tr>
        <tr <?php if ($Current == 0 && $Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "class='hidden'";} ?>>
          <td height="20" align="right" class="Verdana13"><?php echo money_format('%(#1n', $Current); ?></td>
          <td align="right" class="<?php if ($Over_30 == 0 && $Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><?php echo money_format('%(#1n', $Over_30); ?></td>
          <td align="right" class="<?php if ($Over_60 == 0 && $Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><?php echo money_format('%(#1n', $Over_60); ?></td>
          <td align="right" class="<?php if ($Over_90 == 0 && $Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><?php echo money_format('%(#1n', $Over_90); ?></td>
          <td align="right" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>"><?php echo money_format('%(#1n', $Over_120); ?></td>
          <td align="right" class="<?php if ($Over_120 == 0) {echo "hidden";} else {echo "Verdana13";} ?>">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr  class="Verdana14B">
          <td height="20" colspan="4" align="right"><?php if ($TOTRECV > 0) {echo 'Please pay this amount:';} else {echo 'Your Credit Balance:';}?></td>
          <td height="20" align="right">&nbsp;</td>
          <td align="right"><?php echo money_format('%(#1n', $TOTRECV); ?></td>
        </tr>
      </table></td>
    </tr>
  </table>

</div>
<?php } while ($row_CLIENT = mysqli_fetch_assoc($CLIENT)); ?>
</form>


<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
