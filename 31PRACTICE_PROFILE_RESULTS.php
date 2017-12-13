<?php 
echo ' begun ' ;
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;
mysql_select_db($database_tryconnection, $tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysqli_fetch_array($startdate);

echo ' start ' . $startdate[0] ;
// In spite of the name, the following is the search argument in the invoice line item
if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;
$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysqli_fetch_array($enddate);
echo ' end ' . $enddate[0] ;
$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$gethosp="SELECT HOSPNAME FROM CRITDATA LIMIT 1" ;
$Query_hosp = mysql_query($gethosp, $tryconnection) or die(mysql_error()) ;
$row_hosp = mysqli_fetch_array($Query_hosp) ;
$hospname = $row_hosp['HOSPNAME'] ;

$search_SETUP0 = "SELECT DATE_FORMAT(LASTCLOSE, '%m/%d/%Y') AS DATE,CLIENTS,CLIENTS2 AS CLIENTS1YR,CLIENTS3 AS CLIENTS2YR,PATIENTS AS TOTAL_PNT,ACTIVEPAT AS PNT_2YR, ACTPAT3 AS PNT_1YR,INVSALES AS SALES, INVOICE, ROUND(INVSALES/INVOICE,2) AS AVG, CASHREC AS INCOME,GST+PST AS TAX FROM PRACTICE WHERE LASTCLOSE >= '$startdate[0]' AND LASTCLOSE <= '$enddate[0]' ORDER BY LASTCLOSE" ;
$search_TOTAL = "SELECT SUM(INVSALES) AS TOTAL, SUM(CASHREC) AS TOTALCASH, SUM(GST + PST) AS TAX FROM PRACTICE WHERE LASTCLOSE >= '$startdate[0]' AND LASTCLOSE <= '$enddate[0]' ";

$Query_0 = mysql_query($search_SETUP0, $tryconnection) or die(mysql_error()) ;
$Query_1 = mysql_query($search_TOTAL, $tryconnection) or die(mysql_error()) ;
$row_Q0 = mysqli_fetch_assoc($Query_0) ;
$row_Q1 = mysqli_fetch_assoc($Query_1) ;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PRACTICE PROFILE <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){

resizeTo(790,710) ;
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
<!--
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
-->
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
                <li><a href="#" onclick="searchpatient()">tattoo Numbers</a></li>
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
    <td colspan="12" height="25" align="center" class="Verdana13B"><?php echo $hospname ; ?>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="12" height="15" align="center" class="Verdana13B">Practice Profile for <?php  if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;}?><br />&nbsp;</td>
    </tr>
  <tr height="20" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="32" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Closeing</td>
    <td width="50" align="center">Total  Clients</td>
    <td width="45"align="left">Active Clients</td>
    <td width="45"align="left">Client  1yr</td>
    <td width="45" align="left">All Patnts</td>
    <td width="45" align="left">Active Pnts</td>
    <td width="40" align="left">Pnts 1yr&nbsp;</td>
    <td width="55" align="left">Sales</td>
    <td width="50" align="left">Inv.</td>
    <td width="45" align="left">Avg.</td>
    <td width="55" align="left">Receipts</td>
    <td width="50" align="left">Tax</td>
  </tr>
  <tr>
    <td colspan="12" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  $itotinv = 0 ;
  $lastcl = 0 ;
  $newclients = $row_Q0['CLIENTS'] ;
  do {
  echo '<tr id="'.$row_Q0['DATE'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" >
    <td width="30" align="left" class="Verdana12">'.$row_Q0['DATE'].'</td>
    <td width="50" align="center" class="Verdana12">'.$row_Q0['CLIENTS'].'</td>
    <td width="50" align="center" class="Verdana12">'.$row_Q0['CLIENTS2YR'].'</td>
    <td width="50" align="center" class="Verdana12">'.$row_Q0['CLIENTS1YR'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;'.$row_Q0['TOTAL_PNT'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;'.$row_Q0['PNT_2YR'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;&nbsp;'.$row_Q0['PNT_1YR'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;'.$row_Q0['SALES'].'</td>
    <td width="50" align="center" class="Verdana12">'.$row_Q0['INVOICE'].'</td>
    <td width="50" align="center" class="Verdana12">&nbsp;&nbsp;&nbsp;'.$row_Q0['AVG'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;&nbsp;'.$row_Q0['INCOME'].'</td>
    <td width="50" align="right" class="Verdana12">&nbsp;&nbsp;'.$row_Q0['TAX'].'</td>
  </tr>';
  $itotinv = $itotinv + $row_Q0['INVOICE'] ;
  $lastcl = $row_Q0['CLIENTS'] ;
  }
  while ($row_Q0 = mysqli_fetch_assoc($Query_0)) ;
  $newclients = $lastcl - $newclients ;
  
  ?>
  
</table>
    </div>
    

    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Practice Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  
  <tr>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="20" valign="top" class="Verdana13B">Total Sales</td>
    <td height="20" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_Q1['TOTAL']); ?></td>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="20" valign="top" class="Verdana13B">Total Receipts</td>
    <td height="20" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_Q1['TOTALCASH']); ?></td>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="20" valign="top" class="Verdana13B">Total Taxes</td>
    <td height="20" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_Q1['TAX']); ?></td>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="20" valign="top" class="Verdana13B">Total Invoices</td>
    <td height="20" align="right" valign="top" class="Verdana13B"><?php  echo $itotinv; ?></td>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="20" valign="top" class="Verdana13B">New Clients</td>
    <td height="20" align="right" valign="top" class="Verdana13B"><?php  echo $newclients; ?></td>
    <td height="20" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="12  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='REPORTS_DIRECTORY.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.go(-2)"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
