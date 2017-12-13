<?php 
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

$descrip = $_GET['descrip'] ;
$item = $_GET['itemid'] ;
$vencode = $_GET['vencode'] ;


$check_qty = "SELECT SUM(QTYREM) AS QTYREM FROM NARCPUR WHERE ITEM = '$item'" ;
$get_qty = mysql_query($check_qty, $tryconnection) or die(mysql_error()) ;
$row_Narc = mysqli_fetch_assoc($get_qty) ;
$qtyrem = $row_Narc['QTYREM'] ;
$_SESSION['available'] = $qtyrem ;


$check_date = "SELECT DATEPURCH, DATE_FORMAT(DATEPURCH,'%m/%d/%Y') AS DATEPUR FROM NARCPUR WHERE ITEM = '$item' ORDER BY DATEPURCH DESC LIMIT 1" ;
$get_date = mysql_query($check_date, $tryconnection) or die(mysql_error()) ;
$row_DNarc = mysqli_fetch_assoc($get_date) ;
$lastpur = $row_DNarc['DATEPURCH'] ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>DV MANAGER MAC</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload(){
	if (sessionStorage.filetype!='0'){
	document.getElementById('inuse').innerText=sessionStorage.fileused;
	}
	else {
	document.getElementById('inuse').innerHTML="&nbsp;";
	}
}
</script>
<style type="text/css">
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><?php // if (empty($_SESSION['fileused'])){echo"&nbsp;"; } else {echo substr($_SESSION['fileused'],0,25);}  ?>
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->

<form action="UPDATE_NARCOTIC_PURCHASES.php" method="GET" class="FormDisplay">
<table width="100%" height="553" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="17%" height="100">&nbsp;</td>
    <td width="66%">&nbsp;</td>
    <td width="17%">&nbsp;</td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td height="40" align="center" valign="middle">
    
    <table width="418" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="border" rules="none">
      <tr>
        <td width="247" align="center" class="Verdana12">Narcotic drug code</td>
        <td width="165" align="center" valign="middle" class="Verdana12BHL"><?php echo $item ; ?></td>
      </tr>
    </table>
    
    </td>
    <td height="40">&nbsp;</td>
  </tr>
  <tr>
  <tr>
    <td height="28">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
    
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="border" rules="none">
  <tr>
    <td width="65%"height="30" colspan="1" align="left" class="Verdana12"><strong>&nbsp;Drug on file as: <?php echo $descrip ;?></strong></td>
    <td width="25%" class="Verdana12Red">Quantity on file:</td>
    <td width="10%" class="Verdana12Red"><?php echo $qtyrem ;?></td>
    </tr>
  <tr>
    <td width="65%"height="30" >&nbsp;</td>
    <td width="25%" class="Verdana12">Last Purchase:</td>
    <td width="10%" class="Verdana12"><?php echo $lastpur ;?></td>
    </tr>
  <tr>
    <td width="65%" height="30" align="right" class="Verdana12">Quantity Purchased (in drawing units)</td>
    <td width="10%" height="30" align="right" class="Verdana12">&nbsp;</td>
    <td width="25%" height="30" align="left"><input name="qty" id="qty" type="text" class="Input" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
  </tr>
  <tr>
    <td height="30" align="right" class="Verdana12">Date Purchased (MM/DD/YYYY)</td>
    <td height="30" align="right" class="Verdana12">&nbsp;</td>
    <td height="30" align="left"><input name="datepur" id="datepur" type="text" class="Input" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"  onclick="ds_sh(this,'<?php echo date('m/d/Y') ?>')"/></td>
  </tr>
  <tr>
    <td height="30" align="right" class="Verdana12">Vendor Code</td>
    <td height="30" align="right" class="Verdana12">&nbsp;</td>
    <td height="30" align="left"><input name="vpartno" id="vpartno" type="text" class="Input" size="7" value="<?php echo $vencode ;?>"onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>     </td>
  </tr>
</table>
    
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="200">&nbsp;</td>
    <td height="200"><table width="90%" border="1" cellspacing="0" cellpadding="0" bordercolor="#446441" frame="border" rules="none">
      <tr>
        <td>Comment:</td>
      </tr>
      <tr>
        <td>
          <textarea name="comment" id="comment"  class="commentarea" cols="80" rows="2"></textarea></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
    <td height="200">&nbsp;</td>
  </tr>
  <tr class="ButtonsTable">
    <td height="35" colspan="3" align="center" valign="middle">
      <input name="ok" type="submit" class="button" value="OK" />
      <input name="cancel" type="reset" class="button" value="CANCEL" onclick="history.back();"/> 
      <input name="item" type="hidden" value="<?php echo $item; ?>"/>  
      <input name="descrip" type="hidden" value="<?php echo $descrip; ?>"/>  
      <input name="qtyrem" type = "hidden" value = "<?php echo $qtyrem ; ?>"/>
     </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
