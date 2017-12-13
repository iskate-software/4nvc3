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
$startdate=mysqli_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysqli_fetch_array($enddate);

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$search_ARARECV="SELECT ARARECV.CUSTNO,INVDTE AS DSEQ, DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE,INVNO,ITOTAL,IBAL,DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID,
TRIM(ARCUSTO.COMPANY),TRIM(CONTACT),CREDIT,BALANCE, CAREA,PHONE FROM ARARECV JOIN ARCUSTO ON ARARECV.CUSTNO = ARCUSTO.CUSTNO WHERE 
INVDTE  <= '$enddate[0]'AND ARARECV.IBAL <> 0 ORDER BY ARCUSTO.COMPANY,DSEQ ASC";

$search_CREDIT = "SELECT SUM(CREDIT) AS Total_CREDIT FROM ARCUSTO ";
$search_CURRENT = "SELECT SUM(IBAL) AS Total_CURRENT FROM ARARECV WHERE  INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]'";
$search_PST = "SELECT SUM(PTAX) AS Total_PST FROM ARARECV WHERE  INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]'";
$ARARECV=mysql_query($search_ARARECV, $tryconnection ) or die(mysql_error());
$row_ARARECV=mysqli_fetch_assoc($ARARECV);
$CREDIT = mysql_query($search_CREDIT, $tryconnection ) or die(mysql_error()) ;
$TAX = mysql_query($search_TAX, $tryconnection ) or die(mysql_error()) ;
$PST = mysql_query($search_PST, $tryconnection ) or die(mysql_error()) ;
$row_CREDIT = mysqli_fetch_array($CREDIT) ;
$row_TAX = mysqli_fetch_array($TAX) ;
$row_PST = mysqli_fetch_array($PST) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>AGED TRIAL BALANCE FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
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
    <td colspan="8" height="20" align="center" class="Verdana12B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
  <tr height="10" bgcolor="#00FF0F" class="Verdana11Bwhite">
    <td width="35" align="center">Date</td>
    <td width="27" align="center">Inv.#</td>
    <td width="20" align="center">Reason</td>
    <td width="25" align="right">Amount</td>
    <td width="25" align="right">Current</td>
    <td width="25" align="right">Over 30</td>
    <td width="25" align="right">Over 60</td>
    <td width="25" align="right">Over 90</td>
    <td width="25"align="right">Over 120</td>
    <td width="25" align="right">Balance</td>
    <td width="20" align="right">Lpay</td>
  </tr>
  <tr>
    <td colspan="8" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  $thisclient = 0  ;
  do {
   if ($row_ARARECV['ARARECV.CUSTNO']!= $thisclient) {
     $thisclient = $row_ARARECV['ARARECV.CUSTNO'] ;
     echo '
     <tr >
     <td width= "100" align = "left" class = "Verdana12B">'.$row_ARARECV['ARCUSTO.COMPANY'].', '.$row_ARARECV['CONTACT'] .'</td>
     </tr>
     <tr>
     <td width= "100" align = "left" class = "Verdana12B">'.'('.$row_ARARECV['CAREA'].') '.$row_ARARECV['PHONE'] .'</td>
     </tr>' ;
   }
  echo '
  <tr id="'.$row_ARARECV['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search=ARINVOI&invdte='.$row_ARARECV['INVDTE'].'&invno='.$row_ARARECV['INVNO'].'\',\'_blank\')">
    <td width="30" align="left" class="Verdana12">'.$row_ARARECV['INVDTE'].'&nbsp;</td>
    <td width="30" align="right" class="Verdana12">'.$row_ARARECV['INVNO'].'</td>
    <td width="30" class="Verdana13">'.substr($row_ARARECV['PONUM'],0,10).'</td>
    <td width="40" align="right" class="Verdana12">'.number_format(($row_ARARECV['ITOTAL']-$row_ARARECV['TAX']),2).'</td>
    <td width="40" align="right" class="Verdana12">'.$row_ARARECV['TAX'].'</td>
    <td width="40" align="right" class="Verdana12">'.$row_ARARECV['ITOTAL'].'</td>
    <td width="40" align="right" class="Verdana12">'.$row_ARARECV['IBAL'].'</td>
    <td width="40" align="right" class="Verdana12">'.$row_ARARECV['AMTPAID'].'</td>
  </tr>';
  }
  while ($row_ARARECV=mysqli_fetch_assoc($ARARECV);
  
  ?>
  
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">Aged Trial Balance Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18" class="Verdana12">&nbsp;</td>
    <td width="28%" class="Verdana12">Total Credits</td>
    <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CREDIT[0]); ?></td>
    <td width="24%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">Tax</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_TAX[0]); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">PST</td>
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
    <td height="22" valign="top" class="Verdana13B">Grand Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0] + $row_TAX[0] + $row_PST[0]); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='RECEIVABLES_DIRECTORY.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
