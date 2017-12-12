<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}

mysql_select_db($database_tryconnection, $tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysql_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysql_fetch_array($enddate);

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

// first, make a temporary copy of the client file as is now, and all the invoices and cash receipts which have come in since the cut-off date.

$search_ARCUSTO1 = "DROP TEMPORARY TABLE  IF EXISTS CUSTBAL" ;
$search_ARCUSTO2 = "CREATE TEMPORARY TABLE CUSTBAL (CUSTNO INTEGER(7), TITLE VARCHAR(25), COMPANY VARCHAR(50), CONTACT VARCHAR(50), CAREA CHAR(4), PHONE CHAR(8), CITY VARCHAR(50), CREDIT FLOAT(8,2), BALANCE FLOAT(8,2))" ;
$search_ARCUSTO3 = "INSERT INTO CUSTBAL (CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE) SELECT  CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE FROM ARCUSTO ORDER BY CUSTNO" ;
$search_ARINVOI1 = "DROP TEMPORARY TABLE IF EXISTS INVOICES" ;
$search_ARINVOI2 = "CREATE TEMPORARY TABLE INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2))" ;
$search_ARINVOI3 = "INSERT INTO INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2)) SELECT  CUSTNO,DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE,ITOTAL FROM ARINVOI WHERE INVDTE >= '$startdate[0]'  ORDER BY CUSTNO ASC";
$search_ARINVOI4 = "INSERT INTO INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2)) SELECT  CUSTNO,DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE,ITOTAL FROM INVLAST WHERE INVDTE >= '$startdate[0]'  ORDER BY CUSTNO ASC";
$search_ARINVOI5 = "SELECT CUSTNO,ITOTAL FROM INVOICES" ;
$search_ARCASH1 = "DROP TEMPORARY TABLE IF EXISTS EXTRACASH" ;
$search_ARCASH2 = "CREATE TEMPORARY TABLE EXTRACASH (CUSTNO INTEGER(7),DTEPAID DATE, AMTPAID FLOAT(8,2))" ;
$search_ARCASH3 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARCASHR, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_ARCASH4 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM CASHDEP, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_ARCASH5 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM LASTCASH, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_AR CASH6 = "SELECT CUSTNO,AMTPAID FROM EXTRACASH" ;

$ARCUSTO1 = mysql_query($search_ARCUSTO1, $tryconnection) or die(mysql_error()) ;
$ARCUSTO2 = mysql_query($search_ARCUSTO2, $tryconnection) or die(mysql_error()) ;
$ARCUSTO3 = mysql_query($search_ARCUSTO3, $tryconnection) or die(mysql_error()) ;
$ARINVOI1=mysql_query($search_ARINVOI1, $tryconnection) or die(mysql_error());
$ARINVOI2=mysql_query($search_ARINVOI2, $tryconnection) or die(mysql_error());
$ARINVOI3=mysql_query($search_ARINVOI3, $tryconnection) or die(mysql_error());
$ARINVOI4=mysql_query($search_ARINVOI4, $tryconnection) or die(mysql_error());
$ARINVOI5=mysql_query($search_ARINVOI5, $tryconnection) or die(mysql_error());
$CASH1=mysql_query($search_ARCASH1, $tryconnection) or die(mysql_error());
$CASH2=mysql_query($search_ARCASH2, $tryconnection) or die(mysql_error());
$CASH3=mysql_query($search_ARCASH3, $tryconnection) or die(mysql_error());
$CASH4=mysql_query($search_ARCASH4, $tryconnection) or die(mysql_error());
$CASH5=mysql_query($search_ARCASH5, $tryconnection) or die(mysql_error());
$CASH6=mysql_query($search_ARCASH6, $tryconnection) or die(mysql_error());

// now take out from the client file balances all the invoices which are beyond the selection date, and add the cash in to get the retroactive data.
$select_CLIENT = "SELECT DISTINCT CUSTNO FROM INVOICES" ;
$TAKEAWAY = mysql_query($select_CLIENT, $tryconnection) or die(mysql_error()) ;
$row_CLIENT = mysql_fetch_assoc($TAKEAWAY) ;
$invcust = array() ;
do {
$invcust[] = $row_CLIENT['CUSTNO'] ;
} while ($row_CLIENT=mysql_fetch_assoc($TAKEAWAY)) 
foreach ($invcust as $invcust2 {
       $update_CUSTBAL = "UPDATE CUSTBAL SET BALANCE = BALANCE - $row_ARINVOI['ITOTAL'] WHERE CUSTNO = '$invcust2' ";
       $C_U = mysql_query($update_CUSTBAL, $tryconnection) or die(mysql_error()) ;
       }

while ($row_CASH=mysql_fetch_assoc($CASH6)) {
       $update_CUSTBAL = "UPDATE CUSTBAL SET BALANCE = BALANCE + $row_CASH['AMTPAID'] WHERE CUSTNO = $row_CASH['CUSTNO'] ";
       $C_U2 = mysql_query($update_CUSTBAL, $tryconnection) or die(mysql_error()) ;
       }

// and finally

$CLIENT = "SELECT COMPANY,CONTACT,CAREA,PHONE,BALANCE,CREDIT FROM CUSTBAL WHERE BALANCE <> 0 OR CREDIT <> 0 ORDER BY COMPANY,CONTACT ASC" ;
$get_CLIENT = mysql_query($CLIENT, $tryconnection) or die(mysql_error()) ;
$row_CLIENT = mysql_fetch_assoc($get_CLIENT) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CLIENT BALANCE REPORT AS OF <?php echo $_GET['startdate'] ; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;

var irresults=document.getElementById('irresults');
irresults.scrollTop = irresults.scrollHeight;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function highliteline(x,y){
document.getElementById(x).style.cursor='default';
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="8" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="140" align="left">Client.#&nbsp;</td>
    <td width="60" align="center">Location</td>
    <td width="85" align="center">Phone</td>
    <td width="65" align="center">Owing</td>
    <td width="65" align="center">Credit</td>
    <td width="65" align="center">Balance</td>
  </tr>
  <tr>
    <td colspan="8" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  // id removed from tr below 
  // id="'.$row_CLIENTI['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.$_GET['file2search'].'&invdte='.$row_ARINVOI['INVDTE'].'&invno='.$row_ARINVOI['INVNO'].'\',\'_blank\')">
  do {
  echo '
  <tr >
    <td width="140" align="left" class="Verdana13">'.$row_Client['COMPANY'].', '.$row_CLIENT['CONTACT']'&nbsp;</td>
    <td width="60" align="center" class="Verdana13">'.substr$row_CLIENT['CITY'],0,10).'</td>
    <td width="85" class="Verdana13">'.$row_CLIENT['CAREA'].'-'.$row_CLIENT['PHONE'].'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_CLIENT['BALANCE']+$row_CLIENT['CREDIT']),2).'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_CLIENT['CREDIT']),2).'</td>
    <td width="65" align="right" class="Verdana13">'..number_format(($row_CLIENT['BALANCE']),2).'</td>
  </tr>';
  }
  while ($row_CLIENT=mysql_fetch_assoc($get_CLIENT));
  
  ?>
  
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">Client Balance Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18" class="Verdana12">&nbsp;</td>
    <td width="28%" class="Verdana12">Receivables</td>
    <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0]); ?></td>
    <td width="24%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">Credits</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_PST[0]); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="22" valign="top" class="Verdana13B">Net Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0] + $row_TAX[0] + $row_PST[0]); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='INV_REPORTS_DIR.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
