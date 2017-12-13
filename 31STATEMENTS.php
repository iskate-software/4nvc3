<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);

$month_end = date('m')-1 . '/'. date('t',strtotime('last month')).'/'.date('Y') ;
if (substr($month_end,1,1) == '/') {$month_end = '0'.$month_end ;}
if (substr($month_end,0,2) == '00') {$month_end = '12/31/' . date('Y') - 1 ;}
$in_sql = substr($month_end,6,4) . '-' . substr($month_end,0,2) . '-' . substr($month_end,3,2) ;
$prev_month = $in_sql ;
$pre_sql = "SELECT DATE_SUB('$prev_month', interval 1 month) AS AA" ;
$q_pm = mysqli_query($tryconnection, $pre_sql) or die(mysqli_error($mysqli_link)) ;
$row_pm = mysqli_fetch_assoc($q_pm) ;
$pre_php = $row_pm['AA'] ;
$prev_month = substr($pre_php,5,2) . '/' . substr($pre_php,8,2) . '/' . substr($pre_php,0,4) ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CLIENT STATEMENTS</title>
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

<form action="STATEMENTS_SEARCH_CONFIRMATION.php" name="client_statements" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr align="center">
    <td height="185" bgcolor="#B1B4FF">
    <table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td height="37" colspan="2" align="center" class="Verdana13B">DEFINE THE SEARCH GUIDELINE</td>
        </tr>
      <tr>
        <td height="55" align="center" class="Verdana12">Enter the last date of the statement accounting period:<br />
          <span class="Verdana11Grey">(Invoices/Payments after this date will be ignored.)</span></td>
        <td width="180" class="Verdana12"><input name="invdate" type="text" class="Input" id="invdate" size="10" maxlength="10" onclick="ds_sh(this);" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $month_end ; ?>" title="MM/DD/YYYY"/></td>
      </tr>
      <tr>
        <td height="55" align="center" class="Verdana12">Enter the last date of cash receipt to be included:</td>
        <td class="Verdana12"><input name="cashdate" type="text" class="Input" id="cashdate" size="10" maxlength="10" onclick="ds_sh(this);" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $month_end; ?>" title="MM/DD/YYYY"/></td>
      </tr>
      <tr>
        <td height="55" align="center" class="Verdana12">Enter the ending date to accumulate balance foward:<br />
          <span class="Verdana11Grey">(Invoices on and before this date will not be detailed.)</span></td>
        <td class="Verdana12"><input name="balfwddate" type="text" class="Input" id="balfwddate" size="10" maxlength="10" onclick="ds_sh(this);" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $prev_month; ?>" title="MM/DD/YYYY"/></td>
      </tr>
    </table></td>
  </tr>
  <tr align="center">
    <td bgcolor="#B1B4FF">
    <table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td width="90" align="center" class="Verdana12">&nbsp;</td>
        <td width="268" height="40" align="left" class="Verdana12">
        <label>Statement Month
          <select name="stmtmonth" id="stmtmonth">
            <option value="1" <?php if (date('n') == 2) {echo 'selected="selected"';}?>>January</option>
            <option value="2" <?php if (date('n') == 3) {echo 'selected="selected"';}?>>February</option>
            <option value="3" <?php if (date('n') == 4) {echo 'selected="selected"';}?>>March</option>
            <option value="4" <?php if (date('n') == 5) {echo 'selected="selected"';}?>>April</option>
            <option value="5" <?php if (date('n') == 6) {echo 'selected="selected"';}?>>May</option>
            <option value="6" <?php if (date('n') == 7) {echo 'selected="selected"';}?>>June</option>
            <option value="7" <?php if (date('n') == 8) {echo 'selected="selected"';}?>>July</option>
            <option value="8" <?php if (date('n') == 9) {echo 'selected="selected"';}?>>August</option>
            <option value="9" <?php if (date('n') == 10) {echo 'selected="selected"';}?>>September</option>
            <option value="10" <?php if (date('n') == 11) {echo 'selected="selected"';}?>>October</option>
            <option value="11" <?php if (date('n') == 12) {echo 'selected="selected"';}?>>November</option>
            <option value="12" <?php if (date('n') == 1) {echo 'selected="selected"';}?>>December</option>
          </select>
          </label></td>
        <td width="294" height="40" align="left" class="Verdana12"><label>
          <input type="checkbox" name="printhst" id="printhst"  CHECKED value="1"/>
          Print Current HST Invoiced</label></td>
      </tr>
      <tr>
        <td align="center" class="Verdana12">&nbsp;</td>
        <td height="40" align="left" class="Verdana12"><label>
          <input type="checkbox" name="allclients" id="allclients" CHECKED value="1"/>
          All Clients</label></td>
      </tr>
    </table></td>
  </tr>
  <tr align="center">
		<td height="192" bgcolor="#B1B4FF">
        <table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
          <tr>
            <td height="37" colspan="5" align="center" class="Verdana13B">DEFINE CLIENT SEARCH</td>
          </tr>
          <tr>
            <td class="Verdana12">&nbsp;</td>
            <td width="136" height="30" class="Verdana12">Starting Name</td>
            <td width="153" class="Verdana12"><input name="startname" type="text" class="Input" id="startname" size="15" maxlength="15" value="<?php echo "A              " ;?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" /></td>
            <td colspan="2" class="Verdana11Grey">Blank will start at the beginning</td>
          </tr>
          <tr>
            <td class="Verdana12">&nbsp;</td>
            <td height="30" class="Verdana12">Ending Name</td>
            <td class="Verdana12"><input name="endname" type="text" class="Input" id="endname" size="15"  value="<?php echo "Zzzzzzzzzzzzzzz" ;?>" maxlength="15" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" /></td>
            <td colspan="2" class="Verdana11Grey">Zzzzz's will go to the end</td>
          </tr>
          
          <tr>
            <td width="87" class="Verdana12">&nbsp;</td>
            <td height="30" colspan="3" class="Verdana12"><label>
              <input type="checkbox" name="credbal" id="credbal"  />
              Print statements for client with credit balances</label></td>
            <td width="180" class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td class="Verdana12">&nbsp;</td>
            <td height="30" colspan="3" class="Verdana12">Minimum balance needed to print statements 
              <input name="minbal" type="text" class="Inputright" id="minbal" value="5.00" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" /></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
        </table></td>
  </tr>
  <!--
  <tr>
    <td height="60" align="center" valign="top" bgcolor="#B1B4FF"><table width="660" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td width="328" height="40" align="right" class="Verdana12BRed"><span style="background-color:#FFFF00">&nbsp;Run service charges:&nbsp;</span></td>
        <td width="326" height="40" align="left" class="Verdana12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
          <input type="radio" name="svc" id="svc" value="radio" />
          No
          </label>&nbsp;&nbsp;&nbsp;
          <label>
          <input type="radio" name="svc" id="svc2" value="radio2" />
          Yes</label></td>
      </tr>
    </table></td>
  </tr>
-->
  <tr>
  <td align="center" class="ButtonsTable">
  	<input name="save" type="submit" class="button" id="save" value="DISPLAY" />
    <input name="save2" type="submit" class="button" id="save2" value="PRINT" />
    <input name="cancel" type="button" class="button" id="cancel" value="CANCEL" onclick="history.back();"/>
  </td>
  </tr>
</table>

</form>	
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>

