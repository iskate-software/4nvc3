<?php 
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$_SESSION['minbal'] = $_POST['minbal'] ;
$_POST['startname'] = trim($_POST['startname']) ;
$_POST['endname'] = trim($_POST['endname']) ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>DISPLAY/PRINT ALL CLIENT STATEMENTS FOR </title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
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

<form action="../../IMAGES/CUSTOM_DOCUMENTS/STATEMENT.php" name="search_confirm" method="post" target="_blank">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr align="center">
    <td height="316" bgcolor="#B1B4FF">
    <table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td height="47" colspan="2" align="center" class="Verdana13B">CLIENT STATEMENT SEARCH DEFINITION</td>
        </tr>
      <tr>
        <td width="47" align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Last date of statement accounting period: <span class="Verdana12BBlue"><?php echo $_POST['invdate']; ?></span></td>
        </tr>
      <tr>
        <td width="47" align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Last date for cash receipts: <span class="Verdana12BBlue"><?php echo $_POST['cashdate']; ?></span></td>
        </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Balance forward date: <span class="Verdana12BBlue"><?php echo $_POST['balfwddate']; ?></span></td>
        </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Statement month: <span class="Verdana12BBlue"><?php echo date('F',mktime(0,0,0,$_POST['stmtmonth'],1,0));	?></span></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Minimum balance needed to print statement: <span class="Verdana12BBlue"><?php echo $_POST['minbal']; ?></span></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Species: <span class="Verdana12BBlue"><?php if ($_POST['allclients'] == 1 ) {echo 'All species' ;} ?></span></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Client Scan: <span class="Verdana12BBlue"><?php if ($_POST['startname']> 'A') {echo $_POST['startname'];} else {echo 'A ' ;} ?><?php if( $_POST['endname'] != '     ') {echo ' - ' .$_POST['endname']; }?></span></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">Print HST invoiced on statement: <span class="Verdana12BBlue"><?php echo ($_POST['printhst']==1) ? "Yes":"No"; ?></span></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="30" align="left" class="Verdana12">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr align="center">
    <td bgcolor="#B1B4FF">
    <table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td width="46" align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td width="185" align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="genmess" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=gen', '_blank', 'width=600, height=325');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/>&nbsp;General Message</span></div>        </td>
        <td width="182" height="40" align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="mess1" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=1', '_blank', 'width=600, height=412');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/> Message</span> #1</div>        </td>
        <td width="237" height="40" align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="mess2" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=2', '_blank', 'width=600, height=412');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/>&nbsp;Message</span> #2</div>        </td>
      </tr>
      <tr>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="mess3" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=3', '_blank', 'width=600, height=412');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/>&nbsp;Message</span> #3</div>        </td>
        <td height="40" align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="mess4" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=4', '_blank', 'width=600, height=412');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/>&nbsp;Message</span> #4</div>        </td>
        <td height="40" align="left" valign="middle" class="Verdana12">&nbsp;&nbsp;&nbsp;
        <div id="mess5" onmouseover="CursorToPointer(this.id)" onclick="window.open('STMT_MESSAGE.php?msg=5', '_blank', 'width=600, height=412');"><img src="../../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" id="koule1" style="margin-bottom:-5px;"/>&nbsp;Message</span> #5</div>        </td>
      </tr>
      <tr>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td height="10" align="left" valign="middle" class="Verdana12">&nbsp;</td>
        <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="108" align="center" valign="top" bgcolor="#B1B4FF">&nbsp;</td>
  </tr>
  <tr>
  <td align="center" class="ButtonsTable">
  	<input name="save" type="submit" class="button" id="save" value="START" style="width:130px;"/>
    <input name="cancel" type="button" class="button" id="cancel" value="CANCEL" style="width:130px;" onclick="history.back();"/>
  </td>
 </tr>
</table>
<input name="invdate" type="hidden" value="<?php echo $_POST['invdate']; ?>" />
<input name="cashdate" type="hidden" value="<?php echo $_POST['cashdate']; ?>" />
<input name="balfwddate" type="hidden" value="<?php echo $_POST['balfwddate']; ?>" />
<input name="stmtmonth" type="hidden" value="<?php echo $_POST['stmtmonth']; ?>" />
<input name="printhst" type="hidden" value="<?php echo $_POST['printhst']; ?>" />
<input name="indivclient" type="hidden" value="<?php echo $_POST['indivclient']; ?>" />
<input name="allclients" type="hidden" value="<?php echo $_POST['allclients']; ?>" />
<input name="startname" type="hidden" value="<?php echo $_POST['startname']; ?>" />
<input name="endname" type="hidden" value="<?php echo $_POST['endname']; ?>" />
<input name="credbal" type="hidden" value="<?php echo $_POST['credbal']; ?>" />
<input name="minbal" type="hidden" value="<?php echo $_POST['minbal']; ?>" />
<input name="svc" type="hidden" value="<?php echo $_POST['svc']; ?>" />
</form>	
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>

