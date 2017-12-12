<?php 
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if ($_GET['ref']=='Lookup'){
$ref='Lookup  ';
$arinvtype='P';
}
else if ($_GET['ref']=='LookupF'){
$ref='Lookup Food';
$arinvtype='F';
}
else if ($_GET['ref']=='LookupO'){
$ref='Lookup Food';
$arinvtype='O';
}

$query_SELECTEDITEM = sprintf("SELECT * FROM VETCAN WHERE TDESCR = '$ref' LIMIT 1");
$SELECTEDITEM = mysql_query($query_SELECTEDITEM, $tryconnection) or die(mysql_error());
$row_SELECTEDITEM = mysql_fetch_assoc($SELECTEDITEM);

$taxname=taxname($database_tryconnection, $tryconnection, date("m/d/Y")); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY PRICE INQUIRIES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="../../ASSETS/calculation.js"></script>

<script type="text/javascript">
function bodyonload()
{
document.reg_invoicing.invunits.focus();
document.getElementById('inuse').innerText=localStorage.xdatabase;
	
<?php if (substr($row_SELECTEDITEM['TDESCR'],0,6)=="Lookup"){
echo "window.open('../../INVOICE/INVOICING/INVENTORY_POPUP_SCREEN.php?arinvtype=$arinvtype','_blank','width=732,height=500');";
} 
?>

}


function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
</script>

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




<form action="" name="reg_invoicing" method="post">

<!--ARCUSTO-->
<input type="hidden" name="invdisc" value="" />
<input type="hidden" name="invpaydisc" value="<?php echo $row_SELECTEDITEM['INVPAYDISC']; ?>" />
<input type="hidden" name="invpercnt" value="<?php if ($row_PATIENT_CLIENT['DISC']!='0' && $row_SELECTEDITEM['TDISCOUNT']=='1') {echo '1';} else {echo '0';}?>" />
<input type="hidden" name="refclin" value="<?php echo $row_PATIENT_CLIENT['REFCLIN'];?>" />
<input type="hidden" name="refvet" value="<?php echo $row_PATIENT_CLIENT['REFVET'];?>" />
<input type="hidden" name="ptax" value="<?php echo $_SESSION['PTAX'];?>" />
<input type="hidden" name="gtax" value="<?php echo $_SESSION['GTAX'];?>" />
<input type="hidden" name="xdisc" value="<?php echo $row_PATIENT_CLIENT['DISC'];?>" />
<!-- VETCAN (TREATMENTFEEFILE) -->
<input type="hidden" name="invmaj" value="<?php echo $row_SELECTEDITEM['TCATGRY']; ?>" />
<input type="hidden" name="invmin" value="<?php echo $row_SELECTEDITEM['TNO']; ?>" />
<input type="hidden" name="invincm" value="<?php echo $row_SELECTEDITEM['TINCMAST']; ?>" />
<input type="hidden" name="invrevcat" value="<?php echo $row_SELECTEDITEM['TREVCAT']; ?>" />
<input type="hidden" name="invflags" value="<?php echo $row_SELECTEDITEM['TFLAGS']; ?>" />
<input type="hidden" name="invdisp" value="<?php echo $row_SELECTEDITEM['TDISP']; ?>" />
<input type="hidden" name="invget" value="<?php echo $row_SELECTEDITEM['TGET']; ?>" />
<input type="hidden" name="invcomm" value="<?php echo $row_SELECTEDITEM['TINVCOMM']; ?>" />
<input type="hidden" name="histcomm" value="<?php echo $row_SELECTEDITEM['THISTCOMM']; ?>" />
<input type="hidden" name="modicode" value="<?php echo $row_SELECTEDITEM['TMODICODE']; ?>" />
<input type="hidden" name="iradlog" value="<?php echo $row_SELECTEDITEM['TRADLOG']; ?>" />
<input type="hidden" name="isurlog" value="<?php echo $row_SELECTEDITEM['TSURLOG']; ?>" />
<input type="hidden" name="inarclog" value="<?php echo $row_SELECTEDITEM['TNARCLOG']; ?>" />
<input type="hidden" name="iuac" value="<?php echo $row_SELECTEDITEM['TUAC']; ?>" />
<input type="hidden" name="invserum" value="<?php echo $row_SELECTEDITEM['TSERUM']; ?>" />
<input type="hidden" name="autocomm" value="<?php echo $row_SELECTEDITEM['TAUTOCOMM']; ?>" /><!--at the end of each patient on the invoice - it is the code from TFF, by which the system subsequently attaches appropriate comment from ARSYSCOM-->
<input type="hidden" name="commtext" value="<?php echo $row_TAUTOCOMM['COMMENT']; ?>" />
<input type="hidden" name="invupdte" value="<?php echo $row_SELECTEDITEM['TUPDATE']; ?>" /><!--needed at the end of invoice to say if there is anything to update in the patient file-->
<input type="hidden" name="mtaxrate" value="<?php echo $row_SELECTEDITEM['TTAX']; ?>" />
<input type="hidden" name="tunits" value="<?php echo $row_SELECTEDITEM['TUNITS']; ?>" /><!--if invunits is editable-->  
<input type="hidden" name="tfloat" value="<?php echo $row_SELECTEDITEM['TFLOAT']; ?>" /><!--if invunits is a float or integer-->   
<input type="hidden" name="tenter" value="<?php echo $row_SELECTEDITEM['TENTER']; ?>" /><!--if the description is editable-->
        <!-- PETMAST	-->                
<input  type="hidden"name="petname" value="<?php echo $row_PATIENT_CLIENT['PETNAME'];?>" />			
<!-- OTHER FOR SESSION[invline] -->
<input type="hidden" name="inlinenote" value=""  />
<input type="hidden" name="invhype" value="" />
<input type="hidden" name="invest" value="<?php if ($_SESSION['refID']=='EST'){echo "1";} else {echo "0";} ?>" />
<input type="hidden" name="invdecline" value="0" />
<!-- OTHER FOR REJECTED INVOICE -->
<input type="hidden" name="rejdate" value="<?php echo date("Y/m/d"); ?>"/>
<input type="hidden" name="company" value="<?php echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY']; ?>"/>

<!-- OTHER FOR CALCULATIONS -->
<input type="hidden" name="invuprice" value="" />
<input type="hidden" name="pkgprice" value="0" />
<input type="hidden" name="pkgqty" value="" />
<input type="hidden" name="markup" value="" />
<input type="hidden" name="xlabel" value="" />
<input type="hidden" name="dfyes" value="" />
<input type="hidden" name="result" value="" />
<input type="hidden" name="bulk" value="" />
<input type="hidden" name="dispfee" value="" />
<input type="hidden" name="bdispfee" value="" />
<input type="hidden" name="expdate" value="" />
<input type="hidden" name="xtype" value="" />
                             
<input type="hidden" name="salmon" value="1" />
<input type="hidden" name="quantity" value=""  />





<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="57" align="center" valign="bottom" class="Verdana12B">
    
    <label><input type="radio" name="what2look4" value="Lookup" onchange="document.location='PRICE_INQUIRIES.php?ref=Lookup'" <?php if ($_GET['ref']=='Lookup'){echo 'checked';} ?>/>&nbsp;&nbsp;Pharmacy</label>&nbsp;&nbsp;
    <label><input type="radio" name="what2look4" value="Lookup F" onchange="document.location='PRICE_INQUIRIES.php?ref=LookupF'" <?php if ($_GET['ref']=='LookupF'){echo 'checked';} ?>/>&nbsp;&nbsp;Food</label>&nbsp;&nbsp;
    <label><input type="radio" name="what2look4" value="Lookup O" onchange="document.location='PRICE_INQUIRIES.php?ref=LookupO'" <?php if ($_GET['ref']=='LookupO'){echo 'checked';} ?>/>&nbsp;&nbsp;Other</label>   </td>
  </tr>
  <tr>
    <td height="33" align="center" valign="bottom" class="Verdana12B"><input name="button2" type="button" id="button2" value="LOOK UP" onclick="window.open('../../INVOICE/INVOICING/INVENTORY_POPUP_SCREEN.php','blank','width=732,height=500')" style="width:80px;"/>&nbsp;&nbsp;
      <input name="button4" type="button" id="button4" value="CLEAR" onclick="document.location='PRICE_INQUIRIES.php?ref=Lookup'" style="width:80px;"/></td>
  </tr>
  <tr>
    <td height="160" align="center" valign="middle">
    
    <table width="367" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000" frame="box" rules="none">
      <tr>
        <td width="144" height="40" align="right" valign="middle" class="Verdana12">Inventory Code: </td>
        <td width="217" height="40" valign="middle" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="invnarc" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
      <tr>
        <td height="30" align="right" class="Verdana12">Vendor Code: </td>
        <td height="30" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="invvpc" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
      <tr>
        <td height="40" align="right" valign="middle" class="Verdana12">Shelf Location: </td>
        <td height="40" valign="middle" class="Verdana12">&nbsp;&nbsp;
          <input type="text" name="xseq" value="" style="font-size:12px; border:none;" readonly="readonly" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="240" align="center" valign="top">
    
    <table width="80%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" frame="box" rules="none">

<tr>
<td height="150" align="center" valign="top">

        <table width="70%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="20" colspan="3" valign="bottom" class="Verdana11B">&nbsp;</td>
          </tr>
        <tr>
        <td width="50%" height="10" valign="bottom" class="Verdana11B">
        &nbsp;
    <!--PRODUCT SERVICE-->
        <span id="ps">Product/Service</span>
    <!--DRUG-->
        <span id="drug" style="display:none">Drug</span>        </td>
        <td width="35%" height="10" align="right" valign="bottom" class="Verdana11B">
    <!--QTY-->
    	UPrice&nbsp;&nbsp;
        <span id="spkgs" style="display:">Pkgs&nbsp;</span>
&nbsp;                
        <span id="qty">Qty</span>
        <span id="dose" style="display:none">Dose</span>
        <!--UNITS-->
        <span id="units" style="display:none" >Units</span>		</td>
        <!--PRICE-->
        <td height="10" align="right" valign="bottom" class="Verdana11B">Price&nbsp;</td>
        </tr>
        
        <tr>
        <td height="10" valign="top" class="Labels2">
        <input name="invdescr" type="text"  id="item" class="Input" size="25" value="" readonly="readonly"/>        </td>
        <td height="10" align="right" valign="top"><input type="text" name="invprice" id="invprice" value="<?php  echo number_format($row_SELECTEDITEM['TFEE'], 2);?>" class="Inputright" size="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" <?php if ($row_SELECTEDITEM['TGET']!='1'){echo "readonly='readonly'";} ?>/><input name="pkgs" id="pkgs" type="text" class="Inputright" value="0" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" title="number of packages" style="display:"/><input name="invunits" id="invunits" type="text" class="Inputright" value="1" size="4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" title="number of units" <?php if ($row_SELECTEDITEM['TUNITS']!='1'){echo "readonly='readonly'";} ?>/>        </td>
        <td width="15%" height="10" align="right" valign="top" class="Labels2">
        <input name="invtot" id="invtot" type="text" class="Inputright" size="7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php $tfee=number_format($row_SELECTEDITEM['TFEE'], 2); echo $tfee;?>"/>         </td>
         </tr>
         
         <tr id="pharmacy1" style="display:none">
         <td height="10" valign="bottom" class="Verdana11B">&nbsp;Dosage</td>
         <td height="10" valign="bottom" class="Labels">&nbsp;</td>
         <td height="10" align="center" valign="bottom" class="Verdana11B">Days</td>
         </tr>
         
         <tr id="pharmacy2" style="display:none">
         <td height="10" colspan="2" valign="middle" class="Labels2">
          <label><input type="radio" name="dosage" id="sid" value="1" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>SID</label>
          <label><input type="radio" name="dosage" id="bid" value="2" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>BID</label>
          <label><input type="radio" name="dosage" id="tid" value="3" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>TID</label>
          <label><input type="radio" name="dosage" id="qid" value="4" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>QID</label>
          <label><input type="radio" name="dosage" id="other" value="5" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>Other</label>         </td>
         <td height="10" align="center" valign="middle" class="Labels2">
         <input name="days" id="days" type="text" class="Inputright" value="7" size="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')"/>         </td>
         </tr>		  
         <tr>
         <td height="10" class="Labels">
         <label id="fullpkg" style="display:none"><input type="checkbox" name="full" id="full" onchange="calculateprice(localStorage.ovma,localStorage.disp,'<?php taxvalue($database_tryconnection, $tryconnection, date("m/d/Y")); ?>')" />Full package </label><span id="pkgcount"></span><input id="labelbutton" name="label" type="button" value="LABEL"  style="display:none" onclick="labelx();"/>         </td>
         <td height="10" colspan="2" align="right" class="Labels">
         <span style="display:none;"><input name="lookupitem" id="lookupitem" type="button" value="LOOK UP" style="display:none" onclick="window.open('INVENTORY_POPUP_SCREEN.php','blank','width=732,height=500')" />
         <input name="inline" type="button" value="IN-LINE" onclick="window.open('IN_LINE.php','_blank','width=500, height=215');"/></span>         
         <input name="ok" type="button" value="OK" style="display:none"/>         </td>
         </tr>
         <tr>
           <td height="40" colspan="3" align="right" class="Verdana12B">
           <?php echo substr($taxname,0,3); ?>: <input name="invgst" type="text" id="invgst" style="font-size:12px; border:none; text-align:right;" value="" size="10" readonly="readonly" /><br  />
           PST: <input name="invtax" type="text" style="font-size:12px; border:none; text-align:right;" value="" size="10" readonly="readonly" /><br  /><hr  />
           Total invoice cost for items: <input name="abctotal" type="text" style="font-size:12px; border:none; text-align:right; font-weight:bold" value="" size="10" readonly="readonly" />           </td>
           </tr>
         <tr>
           <td height="10" colspan="3" class="Labels"></td>
         </tr>
         <tr>
           <td height="10" colspan="3" class="Labels">&nbsp;</td>
         </tr>
         </table>    </td>
</tr>
</table></td>
  </tr>
  <tr>
    <td height="30" align="right" valign="bottom" class="Verdana11">
    <input type="hidden" name="petia" id="petia" value=""  />
    <input type="text" name="cost" value="" style="font-size:12px; border:none; text-align:right;" readonly="readonly"  size="10"/>
   	<input type="text" name="abccost" value="" style="font-size:12px; border:none; text-align:right;" readonly="readonly"  size="10"/>
</td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="button" type="button" class="button" id="button" value="FINISHED" onclick="document.location='../COMMON/INVENTORY_DIRECTORY.php'" />
    <input name="button3" type="reset" class="button" id="button3" value="CANCEL" onclick="history.back();" /></td>
  </tr>
</table>
<div id="invpreview"></div>
</form>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
