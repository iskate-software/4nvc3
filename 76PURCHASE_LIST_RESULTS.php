<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);

$reqsupplier = $_GET['supplier'] ;

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;
mysqli_select_db($tryconnection, $database_tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysqli_query($tryconnection, $startdate) or die(mysqli_error($mysqli_link));
$startdate=mysqli_fetch_array($startdate);

echo ' start ' . $startdate[0] ;
if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;
$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysqli_query($tryconnection, $enddate) or die(mysqli_error($mysqli_link));
$enddate=mysqli_fetch_array($enddate);

$select_INVTHIST = "SELECT ITEM,ARINVT.DESCRIP,VPCCODE,INVTHIST.SUPPLIER, DRUGCOST,ARINVTYPE,INVTHIST.ORDERED,BACKORDER,RECEIVED,RECTDATE, SUM(`UNITS`) AS `UNITS` 
FROM INVTHIST JOIN ARINVT ON INVTHIST.VPCCODE = ARINVT.VPARTNO WHERE ARINVTYPE = 'P' AND RECEIVED = 1 AND UNITS <> 0 AND INVTHIST.SUPPLIER = '$reqsupplier' AND BACKORDER <> 1 AND INVTHIST.ORDERED >= '$startdate[0]' AND INVTHIST.ORDERED <= '$enddate[0]' GROUP BY INVTHIST.ORDERED,ARINVT.DESCRIP" ;
$INVTHIST = mysqli_query($mysqli_link, $select_INVTHIST) or die(mysqli_error($mysqli_link));
$row_INVTHIST = mysqli_fetch_assoc($INVTHIST);

$select_address = "SELECT VENDOR,ADDRESS FROM VENDORS WHERE SHORTVEN = '$reqsupplier' LIMIT 1" ;
$query_address = mysqli_query($tryconnection, $select_address) or die(mysqli_error($mysqli_link)) ;
$row_address=mysqli_fetch_assoc($query_address) ;


 if (isset($_POST['finish'])){
	foreach ($_POST['deleted'] as $deleted){
	$delete_INVSOLD = "DELETE FROM INVTHIST WHERE VPCCODE=$deleted AND RECEIVED = 0";
	$delete_INVSOLD = mysqli_query($mysqli_link, $delete_INVSOLD) or die(mysqli_error($mysqli_link));
	}
$closewin="window.open('../COMMON/INVENTORY_DIRECTORY.php','_self');";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PURCHASES BY SUPPLIER</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/JavaScript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function highliteline(x){
document.getElementById(x).style.cursor="default";
document.getElementById(x).style.backgroundColor="#DCF6DD";
document.getElementById(document.cash_receipt.invno.value).style.backgroundColor="#00E684";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
document.getElementById(document.cash_receipt.invno.value).style.backgroundColor="#00E684";
}


</script>

<style type="text/css">
#irresults2 {
height:400px; 
overflow:auto;
}
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
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
<form method="get" action="" name="soldlist" id="soldlist" ">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#B1B4FF">
    <td height="35" colspan="8" align="left" valign="middle" bgcolor="#B1B4FF" class="Verdana13B">&nbsp;&nbsp;<span style="background-color:#FFFF00"> Inventory Shipments Received&nbsp;&nbsp;&nbsp;<?php echo $startdate[0] . ' - ' . $enddate[0] ;?></span></td>
    </tr>
    <tr>
    <td height="35" colspan="8" align="left" valign="middle" bgcolor="#B1B4FF" class="Verdana13B">&nbsp;&nbsp;<?php echo $row_address['VENDOR'] ; ?></td>
    </tr>
    <tr>
    <td height="35" colspan="8" align="left" valign="middle" bgcolor="#B1B4FF" class="Verdana13B">&nbsp;&nbsp;<?php echo $row_address['ADDRESS'] ; ?></td>
    </tr>
  <tr>
    <td width="55" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;Vendor#</td>
    <td width="55" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;Supplier</td>
    <td width="50" align="center" class="Verdana11Bwhite" bgcolor="#000000">Units</td>
    <td width="50" align="center" class="Verdana11Bwhite" bgcolor="#000000">Pckge</td>
    <td width="130" align="left" class="Verdana11Bwhite" bgcolor="#000000">Description</td>
    <td width="80" align="left" class="Verdana11Bwhite" bgcolor="#000000">&nbsp;Rcv.Date</td>
    <td width="70"class="Verdana11Bwhite" bgcolor="#000000">Unit Cost</td>
    <td width="40" class="Verdana11Bwhite" bgcolor="#000000">Total $</td>
  </tr>
  <tr>
    <td colspan="8"><div id="irresults2">
      <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
        <?php do { ?>
        <tr class="Verdana11" id="<?php echo $row_INVTHIST['VPCCODE']; ?>" onmouseover="highliteline(this.id)" onmouseout="whiteoutline(this.id)">
          <td height="15" width="55">&nbsp;<?php echo $row_INVTHIST['VPCCODE']; ?></td>
          <td height="15" width="55">&nbsp;<?php echo $row_INVTHIST['SUPPLIER']; ?></td>
          <td width="52" align="right"><?php 
	if (number_format($row_INVTHIST['UNITS'],0)==$row_INVTHIST['UNITS']){
		echo  number_format($row_INVTHIST['UNITS'],0)."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		else {
		echo $row_INVTHIST['UNITS'];
		}
	 ?>          </td>
          <td width="50" align="right"><?php echo $row_INVTHIST['PKGQTY']; ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td width="125"><?php echo $row_INVTHIST['DESCRIP']; ?></td>
          <td width="85"><?php echo $row_INVTHIST['RECTDATE']; ?></td>
          <td width="44" align="right"><?php echo $row_INVTHIST['DRUGCOST']; ?></td>
          <td width="65" align="right"><?php echo number_format($row_INVTHIST['DRUGCOST']*$row_INVTHIST['UNITS'],2); ?></td>
        </tr>
        <?php 
$cogs = $cogs + ($row_INVTHIST['DRUGCOST']*$row_INVTHIST['UNITS']);
} while ($row_INVTHIST = mysqli_fetch_assoc($INVTHIST)); ?>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="70" colspan="11" align="center" bgcolor="#B1B4FF">
    <div class="Verdana14B" style="background-color: #FFFF00; width:250px; line-height:22px; border: ridge white medium;">
    Total Cost.&nbsp;&nbsp;&nbsp;<?php echo number_format($cogs, 2); ?>    </div>    </td>
  </tr>
  <tr id="buttons">
    <td colspan="8" align="center" class="ButtonsTable">
    	<input name="button" type="button" class="button" id="button" value="PRINT" onclick="window.print();" /><input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back();" /></td>
  </tr>
</table>
<input type="hidden" name="whichbutton" value="" />

</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
