<?php

require_once('../../tryconnection.php'); 

mysqli_select_db($tryconnection, $database_tryconnection);
$GET_LAST = "SELECT DATE_FORMAT(MEDATE,'%m/%d/%Y') AS MEDATE FROM CRITDATA LIMIT 1" ;
$QUERY_D  = mysqli_query($mysqli_link, $GET_LAST) or die(mysqli_error($mysqli_link)) ;
$ME_Date = mysqli_fetch_assoc($QUERY_D) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>DEFINE PARAMETERS FOR MONTH END CLOSING</title>
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
	document.getElementById('inuse').innerHTML="<?php echo date('m/d/Y'); ?>";
	}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function confparam(){
var closedate=document.forms[0].closedate.value;
	if (document.forms[0].prtall.checked){
	var prtall=1;
	} else {var prtall=0;}
	if (document.forms[0].yearend.checked){
	var yearend=1;
	} else {var yearend=0;}
window.open('CONFIRM_PARAMETERS.php?closedate='+closedate+'&prtall='+prtall+'&yearend='+yearend,'_self');
}
</script>

<style type="text/css">
<!--
.table1 {	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
-->
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
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="<?php echo "nav0();"; //if ($row_CLIENT['LOCKED']=='1'){ echo "regnotallowed();";} else {echo "nav0();";} ?>">Regular Invoicing</a><!-- InstanceEndEditable --></li>
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
<form method="post" action="" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="78" align="center" valign="bottom" class="Verdana13B">DEFINE MONTH END CLOSING PARAMETERS</td>
  </tr>
  <tr>
    <td height="442" align="center" valign="top">
    <br  />
	<table width="80%" border="1" cellspacing="0" cellpadding="0" class="table1">
      <tr>
        <td align="center">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="15" colspan="3"></td>
            </tr>
            <tr>
              <td width="4%" class="Verdana12">&nbsp;</td>
              <td width="50%" height="25" class="Verdana12">Enter the closing date for month end closing<br /></td>
              <td width="46%" height="25" class="Verdana12"><input name="closedate" id="closedate" type="text" class="Input" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this);" value="<?php echo date('m/d/Y'); ?>" title="MM/DD/YYYY"/></td>
            </tr>
            <tr>
              <td class="Verdana12">&nbsp;</td>
              <td height="25" valign="top" class="Verdana11">&nbsp;&nbsp;*Last month end was done on <?php echo $ME_Date['MEDATE']; ?></td>
              <td height="25" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td class="Verdana12">&nbsp;</td>
              <td height="21" class="Verdana12">&nbsp;</td>
              <td height="21" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td class="Verdana12">&nbsp;</td>
              <td height="25" colspan="2" class="Verdana12B"><label>
                <input name="prtall" type="hidden"  value="" />
                <input name="prtall" type="checkbox" class="Verdana12B" id="prtall" value="0" />
                &nbsp;
                Print ALL the Sales Invoices and Cash Receipts for the month.
              </label></td>
              </tr>
            <tr>
              <td class="Verdana12">&nbsp;</td>
              <td height="27" colspan="2" valign="top" class="Verdana11">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*This will not be needed if this is done on a daily basis.</td>
              </tr>
            <tr class="hidden">
              <td class="Verdana12">&nbsp;</td>
              <td height="25" colspan="2" class="Verdana12B"><label>
                <input type="checkbox" name="yearend" id="yearend" value="1" />
                &nbsp;Check this box if this is your year end.</label></td>
            </tr>
            <tr>
              <td height="15" colspan="3"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="CONTINUE" style="width:100px;" onclick="confparam();"/>
    <input name="button2" type="button" class="button" id="button2" value="CANCEL" onclick="history.back();" style="width:100px;"/></td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
