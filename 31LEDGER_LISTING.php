<?php 
session_start();
require_once('../../tryconnection.php');

$client=$_SESSION['client'];

// Ledger listing program

//This program pulls out all the invoices and cash receipts for a client, and organises them by
// invoice number and date. A running balance is calculated.

// A Client name and number, and starting dates and ending dates are required.

mysqli_select_db($tryconnection, $database_tryconnection);

$LEDG1 = "DROP TEMPORARY TABLE IF EXISTS LEDGER" ;
$LEDG1P = mysqli_query($tryconnection, $LEDG1) or die(mysqli_error($mysqli_link)) ;

$LEDG2 = "CREATE TEMPORARY TABLE LEDGER LIKE ARARECV" ;
$LEDG2P = mysqli_query($tryconnection, $LEDG2) or die(mysqli_error($mysqli_link)) ;

$LEDG2b = "ALTER TABLE LEDGER ADD COLUMN PAYYEAR INT(4) " ;
$LEDG2bP = mysqli_query($tryconnection, $LEDG2b) or die(mysqli_error($mysqli_link)) ;

$LEDG2c = "ALTER TABLE LEDGER DROP COLUMN UNIQUE1 " ;
$LEDG2cP = mysqli_query($tryconnection, $LEDG2c) or die(mysqli_error($mysqli_link)) ;

$LEDG2A = "ALTER TABLE LEDGER ADD COLUMN A1 INT(5) PRIMARY KEY AUTO_INCREMENT" ;
$LEDG2AP = mysqli_query($tryconnection, $LEDG2A) or die(mysqli_error($mysqli_link)) ;

$LEDG3 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, 0, PONUM, REFNO, 0 FROM ARYINVO WHERE CUSTNO = '$client'" ;
$LEDG3P = mysqli_query($tryconnection, $LEDG3) or die(mysqli_error($mysqli_link)) ;

$LEDG4 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, DTEPAID, PAYYEAR, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, 0, DTEPAID,YEAR(INVDTE) AS PAYYEAR, AMTPAID, PONUM,REFNO, 0 FROM ARYCASH WHERE CUSTNO = '$client'" ;
$LEDG4P = mysqli_query($tryconnection, $LEDG4) or die(mysqli_error($mysqli_link)) ;

$LEDG5 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, 0, PONUM, REFNO, 0 FROM INVLAST WHERE CUSTNO = '$client'" ;
$LEDG5P = mysqli_query($tryconnection, $LEDG5) or die(mysqli_error($mysqli_link)) ;

$LEDG6 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, DTEPAID, PAYYEAR, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, 0, DTEPAID,YEAR(INVDTE) AS PAYYEAR, AMTPAID, PONUM, REFNO, 0 FROM LASTCASH WHERE CUSTNO = '$client'" ;
$LEDG6P = mysqli_query($tryconnection, $LEDG6) or die(mysqli_error($mysqli_link)) ;

$LEDG7 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, 0, PONUM, REFNO, 0 FROM ARINVOI WHERE CUSTNO = '$client'" ;
$LEDG7P = mysqli_query($tryconnection, $LEDG7) or die(mysqli_error($mysqli_link)) ;

$LEDG8 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, DTEPAID, PAYYEAR, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, 0, DTEPAID,YEAR(INVDTE) AS PAYYEAR, AMTPAID, PONUM, REFNO, 0 FROM CASHDEP WHERE CUSTNO = '$client'" ;
$LEDG8P = mysqli_query($tryconnection, $LEDG8) or die(mysqli_error($mysqli_link)) ;

$LEDG9 = "INSERT INTO LEDGER (CUSTNO, COMPANY, INVNO, INVDTE, ITOTAL, DTEPAID, PAYYEAR, AMTPAID, PONUM, REFNO, IBAL) SELECT CUSTNO, COMPANY, INVNO, INVDTE, 0, DTEPAID,YEAR(INVDTE) AS PAYYEAR, AMTPAID, PONUM, REFNO, 0 FROM ARCASHR WHERE CUSTNO = '$client'" ;
$LEDG9P = mysqli_query($tryconnection, $LEDG9) or die(mysqli_error($mysqli_link)) ;

$LEDG9A = "DELETE FROM LEDGER WHERE REFNO = 'DEP.AP.'  " ;
$LEDG9AP = mysqli_query($tryconnection, $LEDG9A) or die(mysqli_error($mysqli_link)) ;

$LEDG10 = "DROP  TABLE IF EXISTS LEDGLIST" ;
$LEDG10P = mysqli_query($tryconnection, $LEDG10) or die(mysqli_error($mysqli_link)) ;

$LEDG11 = "CREATE  TABLE LEDGLIST SELECT * FROM LEDGER ORDER BY INVDTE, LPAD(TRIM(INVNO),7,' '), A1" ;
$LEDG11P = mysqli_query($tryconnection, $LEDG11) or die(mysqli_error($mysqli_link)) ;

$HEADING = "SELECT COMPANY FROM LEDGER ORDER BY INVDTE DESC LIMIT 1" ;
$query_head = mysqli_query($tryconnection, $HEADING) or die(mysqli_error($mysqli_link)) ;
$row_head = mysqli_fetch_assoc($query_head) ;

$query_LEDGLIST = "SELECT *, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID, DATE_FORMAT(INVDTE, '%Y/%m/%d') AS DATESEQ FROM LEDGLIST ORDER BY DATESEQ ASC, LPAD(TRIM(INVNO),7,' '),  A1" ;
$LEDGLIST = mysqli_query($tryconnection, $query_LEDGLIST) or die(mysqli_error($mysqli_link)) ;
$row_LEDGLIST = mysqli_fetch_assoc($LEDGLIST);

// now go through this file, building the balance forward into the IBAL field.
$balfwd = 0.00 ;

// Then the array will be the ledger list.


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>LEDGER LISTING FOR <?php echo strtoupper($row_LEDGLIST['COMPANY']); ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}


function highliteline(x,y){
document.getElementById(x).style.backgroundColor=y;
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}



</script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="">About DV Manager</span></a></li>
                <li><a onclick="utilities();">Utilities</a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self'/'+localStorage.xdatabase+'/INVOICE/CASUAL_SALE_INVOICING/STAFF.php?refID=SCI)"><span class="">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="nav0();">Regular Invoicing</a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Cancel Invoices</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/INVOICE/COMMENTS/COMMENTS_LIST.php?path=DIRECTORY','_blank','width=733,height=553,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')">Comments</a></li>
                <li><a href="#" onclick="tffdirectory()">Treatment and Fee File</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Worksheet File</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick="invreports();"><span class="">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')"><span class="">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick="reception();">Patient Registration</a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/RECEPTION/USING_REG_FILE.php','_blank','width=550,height=535')">Using Reception File</a></li>
                <li><a href="#" onclick="nav2();"><span class="hidden"></span>Examination Sheets</a></li>
                <li><a href="#" onclick="gexamsheets()"><span class="">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick="nav3();">Duty Log</a></li>
                <li><a href="#" onclick="staffsiso()">Staff Sign In &amp; Out</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="nav4();">Processing Menu</a> </li>
                <li><a href="#" onclick="nav5();">Review Patient Medical History</a></li>
                <li><a href="#" onclick="nav6();">Enter New Medical History</a></li>
                <li><a href="#" onclick="nav7();">Enter Patient Lab Results</a></li>
                <li><a href="#" onclick=""window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=ENTER SURG. TEMPLATES','_self')><span class="">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=CREATE NEW CLIENT','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');">Create New Client</a></li>
                <li><a href="#" onclick="movepatient();">Move Patient to a New Client</a></li>
                <li><a href="#" onclick="searchpatient()">Rabies Tags</a></li>
                <li><a href="#" onclick="searchpatient()">Tattoo Numbers</a></li>
                <li><a href="#" onclick="nav8();"><span class="">Certificates</span></a></li>
                <li><a href="#" onclick="nav9();"><span class="">Clinical Logs</span></a></li>
                <li><a href="#" onclick="nav10();"><span class="">Patient Categorization</span></a></li>
                <li><a href="#" onclick="">Laboratory Templates</a></li>
                <li><a href="#" onclick="nav1();"><span class="">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""accreports()>Accounting Reports</a></li>
                <li><a href="#" onclick="inventorydir();" id="inventory" name="inventory">Inventory</a></li>
                <li><a href="#" onclick="" id="busstatreport" name="busstatreport"><span class="">Business Status Report</span></a></li>
                <li><a href="#" onclick="" id="hospstatistics" name="hospstatistics"><span class="">Hospital Statistics</span></a></li>
                <li><a href="#" onclick="" id="monthend" name="monthend"><span class="">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')" ><span class="">Recalls and Searches</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Handouts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')MAILING/MAILING_LOG/MAILING_LOG.php?refID=">Mailing Log</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?refID=1','_blank','width=567,height=473')">Referring Clinics and Doctors</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Referral Adjustments</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<table border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="7" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>    </td>
    </tr>
    <tr id="prtclientname">
    <td colspan="7" height="25" align="center" class="Verdana13"><?php echo $row_head['COMPANY'] ?> </td>
    </tr>
  <tr bgcolor="#000000" class="Verdana11Bwhite">
    <td width="111" height="10" align="left">Invoice #</td>
    <td width="77" align="left">Date </td>
    <td width="198" align="left">Invoice Reason&nbsp;&nbsp;&nbsp;&nbsp;Pay Meth.</td>
    <td width="79" align="left">Amount</td>
    <td width="95" align="left">Payment</td>
    <td width="88" align="left">Date paid</td>
    <td width="85" align="center">Bal Fwd</td>
  </tr>
  <tr>
    <td colspan="7" class="Verdana11" align="center">
    
    <div id="irresults3">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="cols">
  <tr>
    <td width="80" height="0" align="left"></td>
    <td width="100" align="left"></td>
    <td width="180" align="left"></td>
    <td width="" align="left"></td>
    <td width="" align="right"></td>
    <td width="100" align="right"></td>
    <td width="80" align="right"></td>
  </tr>
  <?php 
  $invdte = $row_LEDGLIST['INVDTE'];
  $firstdate = $row_LEDGLIST['INVDTE'];
  
  do {
 
 
  echo '
  <tr id="'.$row_LEDGLIST['A1'].'" onmouseover="highliteline(this.id,\'#DCF6DD\');" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search=ARINVOI&invdte='.$row_LEDGLIST['INVDTE'].'&invno='.$row_LEDGLIST['INVNO'].'\',\'_blank\')">
    <td width="" align="center" class="';
	if ($row_LEDGLIST['INVNO'] == 'DEP.'){
 	echo "Verdana13Pink";
  	}
	else {
	echo "Verdana13";
	}
	
  echo '">'.$row_LEDGLIST['INVNO'].'</td>';
  echo '&nbsp;</td>
    <td width="" align="center" class="Verdana13">';
	
	//just for the first date:
	if (isset($firstdate)){
	echo $firstdate;
	unset($firstdate);
	}
	//then filter the repeated dates
	else if ($row_LEDGLIST['INVDTE'] != $invdte){
	//echo $invdte;
	echo $row_LEDGLIST['INVDTE'] ;
	}
	
  echo '</td>
    <td width=""';

	if ($row_LEDGLIST['INVDTE'] != $invdte || $row_LEDGLIST['ITOTAL'] != 0 ){
	echo 'class="Verdana13" align="left"';
	}
	else {
	echo ' class="Verdana11Grey" align="right"';
	}
	
   $isitcan = strpos($row_LEDGLIST['REFNO'],"CANC.") ;
  if ($isitcan === false) {
  // was PONUM below
	if ($row_LEDGLIST['INVDTE'] != $invdte  || $row_LEDGLIST['ITOTAL'] != 0){
  echo '>&nbsp;'.substr($row_LEDGLIST['PONUM'],0,29).'</td>
    <td width="" align="left" class="Verdana13">'; }
    else {
  echo '>&nbsp;'.substr($row_LEDGLIST['REFNO'],0,29).'</td>
    <td width="" align="right" class="Verdana13">'; }
    }
  else {
  
  echo '>&nbsp;'.substr($row_LEDGLIST['PONUM'],0,20)."CANCELLED".'</td>
    <td width="" align="right" class="Verdana13">'; 
  }
  echo $row_LEDGLIST['ITOTAL']==0.00 ? "" : $row_LEDGLIST['ITOTAL'];
  echo '&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td width="" align="right" class="';
	if ($row_LEDGLIST['INVNO'] == 'DEP.'){
 	echo "Verdana13Pink";
  	}
	else {
	echo "Verdana13";
	}
	
  echo '">';
  echo $row_LEDGLIST['AMTPAID']==0.00 ? "" : $row_LEDGLIST['AMTPAID'];
  echo '&nbsp;&nbsp;&nbsp;&nbsp;</td> ';
    if ($row_LEDGLIST['PAYYEAR'] >'1980') {
       echo '<td width="" align="center" class="Verdana13">'.$row_LEDGLIST['DTEPAID'].'</td>
        <td width="" align="right" class="';
	    if ($row_LEDGLIST['INVNO'] == 'DEP.'){
 	     echo "Verdana13Pink";
       	}
	    else {
	    echo "Verdana13";
	    }
	    }
	  else {
	  echo '<td width = "" align = "center" class = "Verdana13">'.'   '.'</td>
        <td width="" align="right" class="';
	    if ($row_LEDGLIST['INVNO'] == 'DEP.'){
 	     echo "Verdana13Pink";
       	}
	    else {
	    echo "Verdana13";
	    }
	}
  echo '">';
 if ($isitcan === false) {
//	 $balfwd = number_format($balfwd,2) + number_format($row_LEDGLIST['ITOTAL'],2) - number_format($row_LEDGLIST['AMTPAID'],2) ;
     if ($row_LEDGLIST['AMTPAID'] > 999.99 ) {
     $balfwd = $balfwd + $row_LEDGLIST['ITOTAL'] - ($row_LEDGLIST['AMTPAID'] - 999.99)  ;
     $balfwd = $balfwd - 999.99 ;
     }
     else {
	 $balfwd = $balfwd + $row_LEDGLIST['ITOTAL'] - $row_LEDGLIST['AMTPAID'] ;
	 }
	 $key = $row_LEDGLIST['A1'] ;
	 $UPDLEGE = "UPDATE LEDGLIST SET IBAL = '$balfwd' WHERE A1 = '$key' " ;
	 $doit = mysqli_query($tryconnection, $UPDLEGE) or die(mysqli_error($mysqli_link)) ;
	}
	
  echo number_format($balfwd,2).'&nbsp;&nbsp;</td>
  </tr>';
  
  
  if (number_format($balfwd,2) == 0.00 || number_format($balfwd,2) == -0.00 ){
  echo '<tr><td colspan="7"><hr size="1" style="margin:0px;" color="#00FF00"/></td></tr>';
  
  }
  
  $invdte = $row_LEDGLIST['INVDTE'];
  }
  while ($row_LEDGLIST = mysqli_fetch_assoc($LEDGLIST));
  ?>
 
</table>
    </div>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="7">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.back();" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
