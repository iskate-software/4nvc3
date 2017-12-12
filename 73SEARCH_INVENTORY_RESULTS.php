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
$startdate=mysql_fetch_array($startdate);

echo ' start ' . $startdate[0] ;
// In spite of the name, the following is the search argument in the invoice line item
$vpartno = $_GET['item'] ;
echo ' search '. $vpartno ;
if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;
$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysql_fetch_array($enddate);
echo ' end ' . $enddate[0] . ' ' ;
$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$gethosp="SELECT HOSPNAME FROM CRITDATA" ;
$Query_hosp = mysql_query($gethosp, $tryconnection) or die(mysql_error()) ;
$row_hosp = mysql_fetch_array($Query_hosp) ;
$hospname = $row_hosp['HOSPNAME'] ;

$search_SETUP0 = "DROP TEMPORARY TABLE IF EXISTS SEARCHINV ;" ;
$search_SETUP1 = "CREATE TEMPORARY TABLE SEARCHINV (INVNO CHAR(7), VPARTNO CHAR(7), INVCUST FLOAT(7),INVDATETIME DATETIME,INVUNITS FLOAT(7),INVDESCR VARCHAR(50),INVPRICE FLOAT(8,2),INVTOT FLOAT(10,2),PETNAME VARCHAR(20),INVDECLINE INT(1))" ;
$search_SETUP2 = "INSERT INTO SEARCHINV SELECT INVNO, INVVPC, INVCUST,INVDATETIME,INVUNITS,INVDESCR,INVPRICE,INVTOT,CONCAT(INVLGSM,' ',PETNAME) AS PETNAME, INVDECLINE FROM DVMINV WHERE INVVPC = '$vpartno' AND INVDECLINE = 0" ;
$search_SETUP3 = "INSERT INTO SEARCHINV SELECT INVNO, INVVPC, INVCUST,INVDATETIME,INVUNITS,INVDESCR,INVPRICE,INVTOT,CONCAT(INVLGSM,' ',PETNAME) AS PETNAME, INVDECLINE FROM ARYDVMI WHERE INVVPC = '$vpartno' AND INVDATETIME >= '$startdate[0]'  AND INVDATETIME <= '$enddate[0]'  AND INVDECLINE = 0";
$search_ARINVOI="SELECT DATE_FORMAT(INVDATETIME, '%Y/%m/%d') AS INVDTE,INVNO,INVUNITS,CONCAT(COMPANY, ',' , CONTACT) AS COMPANY,INVDESCR,INVTOT,PETNAME FROM SEARCHINV JOIN ARCUSTO ON SEARCHINV.INVCUST = ARCUSTO.CUSTNO WHERE INVDATETIME >= '$startdate[0]' AND INVDATETIME <= '$enddate[0]' ORDER BY INVDTE DESC ";
$search_TOTAL = "SELECT SUM(INVUNITS*INVPRICE) AS TOTAL FROM SEARCHINV WHERE INVDATETIME >= '$startdate[0]'  AND INVDATETIME <= '$enddate[0]'  AND INVDECLINE = 0 AND INVCUST <> 0 ";

$Query_0 = mysql_query($search_SETUP0, $tryconnection) or die(mysql_error()) ;
$Query_1 = mysql_query($search_SETUP1, $tryconnection) or die(mysql_error()) ;
$Query_2 = mysql_query($search_SETUP2, $tryconnection) or die(mysql_error()) ;
$Query_3 = mysql_query($search_SETUP3, $tryconnection) or die(mysql_error()) ;
$ARINVOI=mysql_query($search_ARINVOI, $tryconnection ) or die(mysql_error()) ;
$Query_TOT = mysql_query($search_TOTAL, $tryconnection)or die(mysql_error()) ;
$row_ARINVOI=mysql_fetch_assoc($ARINVOI);
$row_NET = mysql_fetch_assoc($Query_TOT) ;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY ITEM SEARCH FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate']; ?></title>
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
    <td colspan="7" height="30" align="center" class="Verdana13B"><?php echo $hospname ; ?>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="7" height="15" align="center" class="Verdana13">Search Line Items for <?php echo $vpartno.' ' ; if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;} echo  ' (No Casual Sales)';?><br />&nbsp;</td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="60" align="left">Inv.#</td>
    <td width="101" align="right">Date</td>
    <td width="207"align="center">Client</td>
    <td width="219"align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sp/Patient</td>
    <td width="263" align="center">Item</td>
    <td width="120" align="center">&nbsp;&nbsp;&nbsp;Units</td>
    <td width="92" align="center">Price</td>
  </tr>
  <tr>
    <td colspan="7" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  
  do {
  echo '
  <tr id="'.$row_ARINVOI['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.'ARINVOI'.'&invdte='.$row_ARINVOI['INVDTE'].'&invno='.$row_ARINVOI['INVNO'].'\',\'_blank\')">
    <td width="60" align="left" class="Verdana13">'.$row_ARINVOI['INVNO'].'&nbsp;</td>
    <td width="101" align="center" class="Verdana13">'.$row_ARINVOI['INVDTE'].'</td>
    <td width="207" class="Verdana13">'.'&nbsp;'.substr($row_ARINVOI['COMPANY'],0,40).'</td>
    <td width="219" class="Verdana13">'.'&nbsp;'.substr($row_ARINVOI['PETNAME'],0,40).'</td>
    <td width="263" class="Verdana13">'.substr($row_ARINVOI['INVDESCR'],0,50).'</td>
    <td width="120" align="right" class="Verdana13">'.$row_ARINVOI['INVUNITS'].'</td>
    <td width="92" align="right" class="Verdana13">'.$row_ARINVOI['INVTOT'].'</td>
  </tr>';
  }
  while ($row_ARINVOI=mysql_fetch_assoc($ARINVOI));
  
  ?>
  
</table>
    </div>
    

    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Line Items Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="22" valign="top" class="Verdana13B">Grand Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET['TOTAL']); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.go(-2)" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
