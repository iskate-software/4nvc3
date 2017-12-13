<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

mysqli_select_db($tryconnection, $database_tryconnection);
if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
// if the names are empty, force them to the beginning and end of the ASCII printable character table.

if (!empty($_GET['startname'])) {
$startname = $_GET['startname'] ;
}
else {
$startname = "0" ;
}
if (!empty($_GET['endname'])) {
$endname = $_GET['endname'] ;
}
else {
$endname ="Zz" ;
}

$startdate1="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate2=mysqli_query($tryconnection, $startdate1) or die(mysqli_error($mysqli_link));
$startdate3=mysqli_fetch_array($startdate2);

$Round_about_midnight = "SELECT DATE_ADD('$startdate3[0]', INTERVAL '23:55' HOUR_MINUTE) AS LATER" ;
$Bump_it = mysqli_query($tryconnection, $Round_about_midnight) or die(mysqli_error($mysqli_link)) ;
$Get_Bump = mysqli_fetch_assoc($Bump_it) ;
$startdate3 = $Get_Bump['LATER'] ;

echo $startdate3 ;

$closemonth ="SELECT DATE_FORMAT('$startdate3', '%D %M %Y') " ;
$clm = mysqli_query($tryconnection, $closemonth) or die(mysqli_error($mysqli_link)) ;
$clm1 = mysqli_fetch_array($clm) ;
$clm2 = $clm1[0] ;

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

// First, force all the client balances to match the receivables.

$BALANCE1 = "DROP TEMPORARY TABLE IF EXISTS TAR1" ;
$BALANCE2 = "CREATE TEMPORARY TABLE TAR1 (CUSTNO FLOAT(7),COMPANY VARCHAR(50),INVDTE DATE, IBAL FLOAT(8,2)) 
            SELECT CUSTNO,COMPANY,INVDTE,SUM(IBAL) AS IBAL FROM ARARECV WHERE IBAL <> 0  GROUP BY CUSTNO ";
            
// Now, make a temporary copy of the client file as is now, and all the invoices and cash receipts which have come in since the cut-off date.

$search_ARCUSTO1 = "DROP TEMPORARY TABLE IF EXISTS CUSTBAL" ;
$search_ARCUSTO2 = "CREATE TEMPORARY TABLE CUSTBAL (CUSTNO FLOAT(7), TITLE VARCHAR(25), COMPANY VARCHAR(50), CONTACT VARCHAR(50), CAREA CHAR(4), PHONE CHAR(8), CITY VARCHAR(50), CREDIT FLOAT(8,2), BALANCE FLOAT(8,2), OWING FLOAT(8,2))" ;
$search_ARCUSTO3 = "INSERT INTO CUSTBAL (CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE) SELECT  CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE FROM ARCUSTO  ORDER BY CUSTNO" ;
$ARCUSTO1 = mysqli_query($tryconnection, $search_ARCUSTO1) or die(mysqli_error($mysqli_link)) ;
$ARCUSTO2 = mysqli_query($tryconnection, $search_ARCUSTO2) or die(mysqli_error($mysqli_link)) ;
$ARCUSTO3 = mysqli_query($tryconnection, $search_ARCUSTO3) or die(mysqli_error($mysqli_link)) ;

$BALANCE3 = "UPDATE CUSTBAL SET BALANCE = 0 WHERE BALANCE <> 0" ;
$BALANCE4 = "UPDATE CUSTBAL JOIN TAR1 USING (CUSTNO) SET CUSTBAL.BALANCE = TAR1.IBAL" ;
$BALANCE5 = "UPDATE CUSTBAL SET BALANCE = BALANCE - CREDIT" ;
$Q_Balance1 = mysqli_query($tryconnection, $BALANCE1) or die(mysqli_error($mysqli_link));
$Q_Balance2 = mysqli_query($tryconnection, $BALANCE2) or die(mysqli_error($mysqli_link));
$Q_Balance3 = mysqli_query($tryconnection, $BALANCE3) or die(mysqli_error($mysqli_link));
$Q_Balance4 = mysqli_query($tryconnection, $BALANCE4) or die(mysqli_error($mysqli_link));
$Q_Balance5 = mysqli_query($tryconnection, $BALANCE5) or die(mysqli_error($mysqli_link));


$search_ARINVOI1 = "DROP TEMPORARY TABLE IF EXISTS INVOICES" ;
$search_ARINVOI2 = "CREATE TEMPORARY TABLE INVOICES (CUSTNO FLOAT(7),INVDTE DATE,ITOTAL FLOAT(8,2))" ;
$search_ARINVOI3 = "INSERT INTO INVOICES (CUSTNO,INVDTE,ITOTAL) SELECT  CUSTNO, INVDTE,SUM(ITOTAL) AS ITOTAL FROM ARINVOI WHERE INVDTE > '$startdate3'  AND CUSTNO <> 0  GROUP BY CUSTNO ASC";


$search_CASH1 = "DROP TEMPORARY TABLE IF EXISTS EXTRACASH" ;
$search_CASH12 = "DROP TEMPORARY TABLE IF EXISTS EXTRACASH2" ;
$search_CASH2 = "CREATE TEMPORARY TABLE EXTRACASH (CUSTNO FLOAT(7),DTEPAID DATE, INVNO CHAR(7),AMTPAID FLOAT(8,2))" ;
$search_CASH22 = "CREATE TEMPORARY TABLE EXTRACASH2 (CUSTNO FLOAT(7),DTEPAID DATE, INVNO CHAR(7),AMTPAID FLOAT(8,2))" ;
$search_CASH3 = "INSERT INTO EXTRACASH SELECT CUSTNO, DTEPAID, INVNO,SUM(AMTPAID) AS AMTPAID FROM CASHDEP WHERE DTEPAID > '$startdate3' AND CUSTNO <> 0 GROUP BY CUSTNO ASC";
$search_CASH32 = "INSERT INTO EXTRACASH2 SELECT CUSTNO, DTEPAID, INVNO,SUM(AMTPAID) AS AMTPAID FROM ARCASHR WHERE DTEPAID > '$startdate3' AND CUSTNO <> 0 GROUP BY CUSTNO ASC ";


$ARINVOI1 = mysqli_query($tryconnection, $search_ARINVOI1) or die(mysqli_error($mysqli_link));
$ARINVOI2 = mysqli_query($tryconnection, $search_ARINVOI2) or die(mysqli_error($mysqli_link));
$ARINVOI3 = mysqli_query($tryconnection, $search_ARINVOI3) or die(mysqli_error($mysqli_link));
 
$CASH1 = mysqli_query($tryconnection, $search_CASH1) or die(mysqli_error($mysqli_link));
$CASH12 = mysqli_query($tryconnection, $search_CASH12) or die(mysqli_error($mysqli_link));
$CASH2 = mysqli_query($tryconnection, $search_CASH2) or die(mysqli_error($mysqli_link));
$CASH22 = mysqli_query($tryconnection, $search_CASH22) or die(mysqli_error($mysqli_link));
$CASH3 = mysqli_query($tryconnection, $search_CASH3) or die(mysqli_error($mysqli_link));
$CASH32 = mysqli_query($tryconnection, $search_CASH32) or die(mysqli_error($mysqli_link));
 
// now take out from the client file balances all the invoices which are beyond the selection date, and add the cash in to get the retroactive data.

$takeinvout = "UPDATE CUSTBAL JOIN INVOICES ON (CUSTBAL.CUSTNO = INVOICES.CUSTNO) SET CUSTBAL.BALANCE = CUSTBAL.BALANCE - INVOICES.ITOTAL " ;
$takeoutcash = "UPDATE CUSTBAL JOIN EXTRACASH ON (CUSTBAL.CUSTNO = EXTRACASH.CUSTNO) SET CUSTBAL.BALANCE = CUSTBAL.BALANCE + EXTRACASH.AMTPAID  " ;
$takeoutcash2 = "UPDATE CUSTBAL JOIN EXTRACASH2 ON (CUSTBAL.CUSTNO = EXTRACASH2.CUSTNO) SET CUSTBAL.BALANCE = CUSTBAL.BALANCE + EXTRACASH2.AMTPAID " ;

$doinv = mysqli_query($tryconnection, $takeinvout) or die(mysqli_error($mysqli_link)) ;
$docash = mysqli_query($tryconnection, $takeoutcash) or die(mysqli_error($mysqli_link)) ;
$docash2 = mysqli_query($tryconnection, $takeoutcash2) or die(mysqli_error($mysqli_link)) ;


//penultimately, the net and gross figures for the summary.

$BALANCE = "SELECT SUM(BALANCE + CREDIT) AS IBAL FROM CUSTBAL WHERE COMPANY >= TRIM('$startname') AND COMPANY <= TRIM('$endname') " ;
$CREDIT = "SELECT SUM(CREDIT) AS CREDIT FROM CUSTBAL  WHERE COMPANY >= TRIM('$startname') AND COMPANY <= TRIM('$endname')" ;

$NET = mysqli_query($tryconnection, $BALANCE) or die(mysqli_error($mysqli_link)) ;
$CREDIT1 = mysqli_query($tryconnection, $CREDIT) or die(mysqli_error($mysqli_link)) ;

$row_NET = mysqli_fetch_assoc($NET) ;
$row_CREDIT = mysqli_fetch_assoc($CREDIT1) ;

// FINALLY, the alpha and numeric extract. 

$CLIENT = "SELECT COMPANY,CONTACT,CITY,CAREA,PHONE,BALANCE,CREDIT, BALANCE-CREDIT AS OWING FROM CUSTBAL WHERE COMPANY >= TRIM('$startname') AND COMPANY <= TRIM('$endname') AND (BALANCE <> 0 OR CREDIT <> 0) ORDER BY COMPANY,CONTACT ASC" ;
$get_CLIENT = mysqli_query($tryconnection, $CLIENT) or die(mysqli_error($mysqli_link)) ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CLIENT BALANCE REPORT AS OF <?php echo $startdate[0] ; ?></title>
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
    <td colspan="6" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>    </td>
  </td>
  <tr id="reporttitle">
    <td colspan="6" height="20" align ="center" class="Verdana13"><?php echo 'Client Balance Report as of '.$clm2 ; ?> </td>
    </tr>
  </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="100" align="left">Client&nbsp;</td>
    <td width="80" align="center">Location</td>
    <td width="75" align="center">Phone</td>
    <td width="70" align="right">Invoices</td>
    <td width="60" align="right">&nbsp;&nbsp;&nbsp;Credit</td>
    <td width="75" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Balance</td>
  </tr>
  <tr>
    <td colspan="6" class="Verdana12" align="center">
    
    <div id="irresults2">
      <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
        <?php 
  while ($row_CLIENT=mysqli_fetch_assoc($get_CLIENT)) {
   
  echo ' 
 <tr>
    <td width="100" align="left" class="Verdana13">'.$row_CLIENT['COMPANY'].', '.$row_CLIENT['CONTACT']. '&nbsp;</td>
    <td width="20" align="left" class="Verdana13">'.$row_CLIENT['CITY'].'</td>
    <td width="85" class="Verdana13">'.$row_CLIENT['CAREA'].'-'.$row_CLIENT['PHONE'].'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_CLIENT['BALANCE']+$row_CLIENT['CREDIT']),2).'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_CLIENT['CREDIT']),2).'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_CLIENT['BALANCE']),2).'</td>
      </tr>';
  }
  
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
            <td width="28%" class="Verdana12">Invoices</td>
            <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET['IBAL']); ?></td>
            <td width="24%" class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">Credits</td>
            <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CREDIT['CREDIT']); ?></td>
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
            <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET['IBAL'] - $row_CREDIT['CREDIT'] ); ?></td>
            <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
          </tr>
      </table></td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  "><input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='MONTH_END_DIRECTORY.php'" />
        <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
        <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table></td>
    </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
