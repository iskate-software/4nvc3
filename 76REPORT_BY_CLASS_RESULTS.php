<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
if ($_GET[seq]!= '99') {
$query_INVENTORY = "SELECT ITEMID,ITEM,DESCRIP,DISPFEE,ONHAND, SEQ,CLASS,VPARTNO,PKGQTY,COST,UPRICE,PRICE FROM ARINVT WHERE INSTR('$_GET[seq]',CLASS) <> 0 ORDER BY CLASS, DESCRIP"; 
$query_tot = "SELECT SUM(ONHAND/PKGQTY*COST) AS TOTAL FROM ARINVT WHERE INSTR('$_GET[seq]',CLASS) <> 0" ;
 }
else {
$query_INVENTORY = "SELECT ITEMID,ITEM,DESCRIP,DISPFEE, ONHAND, SEQ,CLASS,VPARTNO,PKGQTY,COST,UPRICE,PRICE FROM ARINVT ORDER BY ITEM, DESCRIP";
$query_tot = "SELECT SUM(ONHAND/PKGQTY*COST) AS TOTAL FROM ARINVT " ;
}
$INVENTORY = mysqli_query($tryconnection, $query_INVENTORY) or die(mysqli_error($mysqli_link));
$row_INVENTORY = mysqli_fetch_assoc($INVENTORY);


$grand_tot = mysqli_query($tryconnection, $query_tot) or die(mysqli_error($mysqli_link)) ;
$row_tot = mysqli_fetch_assoc($grand_tot) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>REPORT BY CLASS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript">

function bodyonload(){
resizeTo(795,750) ;
document.getElementById('inuse').innerText=localStorage.xdatabase;

var irresults=document.getElementById('irresults');
irresults.scrollTop = irresults.scrollHeight;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function highliteline(x){
document.getElementById(x).style.cursor='default';
document.getElementById(x).style.backgroundColor="#DCF6DD";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}


</script>


<style type="text/css">

#WindowBody {
	position:absolute;
	top:33px;
	width:785px;
	min-height:553px;
	z-index:1;
	font-family: "Andale Mono";
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #FFFFFF;
	left: 10px;
	color: #000000;
	text-align: left;
}

</style>
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="12" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="8" align="left">&nbsp;&nbsp;Code</td>
    <td width="30" align="center">Description</td>
    <td width="9" align="center">Qty</td>
    <td width="8" align="left">Value</td>
    <td width="6" align="left">DF</td>
    <td width="8" align="left">&nbsp;Loc&nbsp;&nbsp;Cl</td>
    <td width="5" align="left">&nbsp;</td>
    <td width="8" align="left">VPC Code&nbsp;</td>
    <td width="9" align="left">PkQty</td>
    <td width="8" align="left">Cost</td>
    <td width="10" align="left">Uprice</td>
    <td width="8" align="left">Pprice</td>
  </tr>
  <tr>
    <td colspan="11" class="Verdana12" align="center">
    
    <div id="reportbyloc">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#FFFFFF" frame="below" rules="all">

  <tr height="0">
    <td width="8" align="left"></td>
    <td width="30" align="left"></td>
    <td width="10" aligh="left"</td>
    <td width="8" aligh="left"</td>
    <td width="8" aligh="center"</td>
    <td width="7" align="right"></td>
    <td width="3" align="right"></td>
    <td width="8" align="left"></td>
    <td width="11" align="right"></td>
    <td width="11" align="right"></td>
    <td width="11" align="right"></td>
    <td width="11" ></td>
  </tr>

  <?php 
  
  do {
  echo '
  <tr id="'.$row_INVENTORY['ITEMID'].'" onmouseover="highliteline(this.id)" onmouseout="whiteoutline(this.id)">
    <td class="Verdana12">'.$row_INVENTORY['ITEM'].'</td>
    <td class="Verdana12">'.$row_INVENTORY['DESCRIP'].'</td>
    <td class="Verdana12">'.$row_INVENTORY['ONHAND'].'</td>
    <td class="Verdana12" align="left">'.round($row_INVENTORY['ONHAND']/$row_INVENTORY['PKGQTY']*$row_INVENTORY['COST'],0).'</td>
    <td class="Verdana12" align="left">'.$row_INVENTORY['DISPFEE'].'</td>
    <td class="Verdana12" align="left">&nbsp;'.$row_INVENTORY['SEQ'].'</td>
    <td class="Verdana12B" align="right">'.$row_INVENTORY['CLASS'].'</td>
    <td class="Verdana12" align="right">'.$row_INVENTORY['VPARTNO'].'</td>
    <td class="Verdana12" align="right">'.$row_INVENTORY['PKGQTY'].'</td>
    <td class="Verdana12" align="right">'.$row_INVENTORY['COST'].'</td>
    <td class="Verdana12" align="right">'.$row_INVENTORY['UPRICE'].'</td>
    <td class="Verdana12" align="right">'.$row_INVENTORY['PRICE'].'</td>
  </tr>';
	} while ($row_INVENTORY = mysqli_fetch_assoc($INVENTORY));  
  ?>
  
</table>
    </div>
    
     <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br /><?php if ($can == 1) {echo 'Cancelled ';}  if ($sc == 1) {echo ' Service Charges Only ' ; } if ($negs == 1) {echo 'Negative Invoices Only ';} ?>Year End Inventory Summary </td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18" class="Verdana12">&nbsp;</td>
    <td width="28%" class="Verdana12">TOTAL</td>
    <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_tot['TOTAL']); ?></td>
    <td width="24%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="12  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.back()" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>




<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
