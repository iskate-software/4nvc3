<?php
session_start();

// This function takes the rounding unit from the markup formula and returns the result

function invtround($x,$y)
{
 if ($x == 0.01) {

  $result = $y ;
 }

 if ($x == .05) {

  $floor = round($y,1) ;
  if ($floor > $y) {$floor = $floor - $x ;}
  $diff  = $y - $floor ;
  if ($diff < .03) {$result = $floor ;}  
  else if ($diff < .08) {$result = $floor + .05;} 
  else {$result = $floor + .10;} 
 }

 if ($x == .10) {

  $floor = round(floor($y*10)/10,1) ;
  if ($floor > $y) {$floor = $floor - $x ;}
  $diff  = $y - $floor ;
  if ($diff < .05) {$result = $floor ;}  
  else {$result = $floor + .10;}
 }
   
 if ($x == .25) {
  $floor = floor($y*10)/10 ;
  $diff  = $y - $floor ;
  if ($diff < .13) {$result = $floor ;}
  else if ($diff < .38) {$result = $floor +.25;}  
  else if ($diff < .68) {$result = $floor + .50;}  
  else if ($diff < .88) {$result = $floor + .75;}  
  else {$result = $floor + 1.0;}
 }
  
 if ($x == .50) {
  $floor = floor($y*10)/10 ;
  $diff  = $y - $floor ;
  if ($diff < .25) {$result = $floor ;}
  else if ($diff < .75) {$result = $floor + .50;}  
  else {$result = $floor + 1.0;}
 }
 if ($x == 1.00){$result = round($y,0) ;}
 $result = number_format($result,2);
 return $result ;
}

require_once('../../tryconnection.php');

$itemid=$_GET['itemid'];
mysql_select_db($database_tryconnection, $tryconnection);
$query_INVENTORY = "SELECT *, DATE_FORMAT(EXPDATE, '%m/%d/%Y') AS EXPDATE, DATE_FORMAT(LDATE, '%m/%d/%Y') AS LDATE, DATE_FORMAT(LASTSALE, '%m/%d/%Y') AS LASTSALE FROM ARINVT WHERE ITEMID = '$itemid'";
$INVENTORY = mysql_query($query_INVENTORY, $tryconnection) or die(mysql_error());
$row_INVENTORY = mysqli_fetch_assoc($INVENTORY);


/////////////////////////////PAGING WITHIN INVENTORY FILES/////////////////////////
$query_VIEW="CREATE OR REPLACE VIEW INVENTORY2 AS SELECT ITEMID, ITEM FROM ARINVT ORDER BY ITEM ASC";
$VIEW= mysql_query($query_VIEW, $tryconnection) or die(mysql_error());

$query_INVENTORY2="SELECT * FROM INVENTORY2";
$INVENTORY2= mysql_query($query_INVENTORY2, $tryconnection) or die(mysql_error());
$row_INVENTORY2 = mysqli_fetch_assoc($INVENTORY2);

$ids= array();
do {
$ids[]=$row_INVENTORY2['ITEMID'];
}
while ($row_INVENTORY2 = mysqli_fetch_assoc($INVENTORY2));

$key=array_search($_GET['itemid'],$ids);

/////////////////////////////PAGING WITHIN INVENTORY FILES/////////////////////////


if (isset($_POST['check']) && $_POST['xdelete']=="1") {
$deleteSQL="DELETE FROM ARINVT WHERE ITEMID='$itemid'";
$Result1 = mysql_query($deleteSQL, $tryconnection) or die(mysql_error());
header("Location: ADD_EDIT_INVENTORY.php?itemid=$_POST[invpointer]&check=");
}

else if (isset($_POST['check']) && $itemid=="0") {
$expquery="SELECT STR_TO_DATE('$_POST[expdate]','%m/%d/%Y');";
$expdate1= mysql_query($expquery, $tryconnection) or die(mysql_error());
$expdate=mysqli_fetch_array($expdate1);
$ldatequery="SELECT STR_TO_DATE('$_POST[ldate]','%m/%d/%Y');";
$ldate1= mysql_query($ldatequery, $tryconnection) or die(mysql_error());
$ldate=mysqli_fetch_array($ldate1);
// do the class check and update. If not there, default to a markup of 1.
$CLASS = "SELECT REGITM3,REGITM6,ROUNDER1,MINPRICE1,ROUNDER4,MINPRICE4 FROM FORMULA1 WHERE CLASS = '$_POST[class]' LIMIT 1" ;
$get_CLASS = mysql_query($CLASS) or die(mysql_error()) ;
$row_CLASS = mysqli_fetch_array($get_CLASS) ;
if (empty($_POST['manual']) || $_POST['manual'] == 0) {
 $_POST['price']  = ROUND($_POST['cost'] * $row_CLASS['REGITM3'] / $_POST['pkgqty'],2) ;
 $_POST['uprice'] = ROUND($_POST['cost'] * $row_CLASS['REGITM6'] / $_POST['pkgqty'],2) ;

  if ($_POST['price'] < $row_CLASS['MINPRICE1'])  {
      $_POST['price'] = $row_CLASS['MINPRICE1'] ;
  }
  if ($_POST['uprice'] < $row_CLASS['MINPRICE4'])  {
      $_POST['uprice'] = $row_CLASS['MINPRICE4'] ;
  }
  // and apply the round to a particular unit formula (nickels, dimes, quarters, half looneys, looneys).
  $_POST['price'] = invtround($row_CLASS['ROUNDER1'],$_POST['price']) ;
  $_POST['uprice'] = invtround($row_CLASS['ROUNDER4'],$_POST['uprice']) ;
  
  if ($_POST['pkgqty'] == 1) {
  $_POST['uprice'] = $_POST['price'] ;
 }
}
$insertSQL = sprintf("INSERT INTO ARINVT (ITEM, `CLASS`, DESCRIP, COST, PRICE, UPRICE, ONHAND, ONORDER, ORDERPT, ORDERQTY, ORDERED, SUPPLIER, SEQ, VPARTNO, DISPFEE, BDISPFEE, GOVNARC, TAXRATE, PKGQTY, SELLUNIT, MEMO, LABEL, TYPE, DFYES, BULK, MARKUP, EXPDATE, LDATE, MONITOR, AUTOSET, SAFETY, SPECORDER, BARCODE, `COMMENT`, MANUAL, ARINVTYPE) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '$expdate[0]', '$ldate[0]', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                       mysql_real_escape_string($_POST['item']),
                       $_POST['class'],
                       mysql_real_escape_string($_POST['descrip']),
                       $_POST['cost'],
                       $_POST['price'],
                       $_POST['uprice'],
                       $_POST['onhand'],
                       $_POST['onorder'],
                       $_POST['orderpt'],
                       $_POST['orderqty'],
                       $_POST['ordered'],
                       mysql_real_escape_string($_POST['supplier']),
                       $_POST['seq'],
                       $_POST['vpartno'],
                       $_POST['dispfee'],
                       $_POST['bdispfee'],
					   !empty($_POST['govnarc']) ? "1" : "0",
                       $_POST['taxrate'],
                       $_POST['pkgqty'],
                       $_POST['sellunit'],
                       mysql_real_escape_string($_POST['memo']),
					   !empty($_POST['label']) ? "1" : "0",
                       $_POST['type'],
                       $_POST['dfyes'],
					   !empty($_POST['bulk']) ? "1" : "0",
                       $_POST['markup'],
					   !empty($_POST['monitor']) ? "1" : "0",
					   !empty($_POST['autoset']) ? "1" : "0",
                       $_POST['safety'],
					   !empty($_POST['specorder']) ? "1" : "0",
                       $_POST['barcode'],
                       $_POST['comment'],
					   !empty($_POST['manual']) ? "1" : "0",
					   $_POST['arinvtype']
                       );

mysql_select_db($database_tryconnection, $tryconnection);
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
if (isset($_POST['save'])){
header("Location: INVENTORY_SEARCH_SCREEN.php");}
else {
header("Location: ADD_EDIT_INVENTORY.php?itemid=$_POST[invpointer]&check=");}
}

else if (isset($_POST['check']) && $itemid!="0") {
$expquery="SELECT STR_TO_DATE('$_POST[expdate]','%m/%d/%Y');";
$expdate1= mysql_query($expquery, $tryconnection) or die(mysql_error());
$expdate=mysqli_fetch_array($expdate1);
$ldatequery="SELECT STR_TO_DATE('$_POST[ldate]','%m/%d/%Y');";
$ldate1= mysql_query($ldatequery, $tryconnection) or die(mysql_error());
$ldate=mysqli_fetch_array($ldate1);
// do the class check and update. If not there, default to a markup of 1.
$CLASS = "SELECT REGITM3,REGITM6,ROUNDER1,MINPRICE1,ROUNDER4,MINPRICE4 FROM FORMULA1 WHERE CLASS = '$_POST[class]' LIMIT 1" ;
$get_CLASS = mysql_query($CLASS) or die(mysql_error()) ;
$row_CLASS = mysqli_fetch_array($get_CLASS) ;
if (empty($_POST['manual']) || $_POST['manual'] == 0) {
 $_POST['price'] = ROUND($_POST['cost'] * $row_CLASS['REGITM3'] / $_POST['pkgqty'],2) ;
 
 $_POST['uprice'] = ROUND($_POST['cost'] * $row_CLASS['REGITM6'] / $_POST['pkgqty'],2) ;

  if ($_POST['price'] < $row_CLASS['MINPRICE1'])  {
      $_POST['price'] = $row_CLASS['MINPRICE1'] ;
  }
  if ($_POST['uprice'] < $row_CLASS['MINPRICE4'])  {
      $_POST['uprice'] = $row_CLASS['MINPRICE4'] ;
  }
  // and apply the round to a particular unit formula (nickels, dimes, quarters, half looneys, looneys).
  $_POST['price'] = invtround($row_CLASS['ROUNDER1'],$_POST['price']) ;
  $_POST['uprice'] = invtround($row_CLASS['ROUNDER4'],$_POST['uprice']) ;
  
  if ($_POST['pkgqty'] == 1) {
  $_POST['uprice'] = $_POST['price'] ;
 }
}
$updateSQL = sprintf("UPDATE ARINVT SET ITEM='%s', `CLASS`='%s', DESCRIP='%s', COST='%s', PRICE='%s', UPRICE='%s', ONHAND='%s', ONORDER='%s', ORDERPT='%s', ORDERQTY='%s', ORDERED='%s', SUPPLIER='%s', SEQ='%s', VPARTNO='%s', DISPFEE='%s', BDISPFEE='%s', GOVNARC='%s', TAXRATE='%s', PKGQTY='%s', SELLUNIT='%s', MEMO='%s', LABEL='%s', TYPE='%s', DFYES='%s', BULK='%s', MARKUP='%s', EXPDATE = '$expdate[0]', LDATE = '$ldate[0]', MONITOR='%s', AUTOSET='%s', SAFETY='%s', SPECORDER='%s', BARCODE='%s', `COMMENT`='%s', MANUAL='%s', ARINVTYPE='%s' WHERE ITEMID='%s'",
                       mysql_real_escape_string($_POST['item']),
					   $_POST['class'],
                       mysql_real_escape_string($_POST['descrip']),
                       $_POST['cost'],
                       $_POST['price'],
                       $_POST['uprice'],
                       $_POST['onhand'],
                       $_POST['onorder'],
                       $_POST['orderpt'],
                       $_POST['orderqty'],
                       $_POST['ordered'],
                       mysql_real_escape_string($_POST['supplier']),
                       $_POST['seq'],
                       $_POST['vpartno'],
                       $_POST['dispfee'],
                       $_POST['bdispfee'],
					   !empty($_POST['govnarc']) ? "1" : "0",
                       $_POST['taxrate'],
                       $_POST['pkgqty'],
                       $_POST['sellunit'],
                       mysql_real_escape_string($_POST['memo']),
					   !empty($_POST['label']) ? "1" : "0",
                       $_POST['type'],
                       $_POST['dfyes'],
					   !empty($_POST['bulk']) ? "1" : "0",
                       $_POST['markup'],
					   !empty($_POST['monitor']) ? "1" : "0",
					   !empty($_POST['autoset']) ? "1" : "0",
                       $_POST['safety'],
					   !empty($_POST['specorder']) ? "1" : "0",
                       $_POST['barcode'],
                       $_POST['comment'],
					   !empty($_POST['manual']) ? "1" : "0",
					   $_POST['arinvtype'],
                       $itemid
					   );

mysql_select_db($database_tryconnection, $tryconnection);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
if (isset($_POST['save'])){
header("Location: INVENTORY_SEARCH_SCREEN.php");}
else {
header("Location: ADD_EDIT_INVENTORY.php?itemid=$_POST[invpointer]&check=");}
}


?>?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.5" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>INVENTORY MAINTENANCE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.style2 {color: #990033}
.CustomizedButton1 {
	font-family: Verdana;
	font-size: 12px;
	width: 80px;
	height: 27px;
	margin-left: 4px;
	margin-right: 4px;
}

.CustomizedButton2 {
	font-family: Verdana;
	font-size: 20px;
	width: ;
	height: 27px;
	margin-left: 4px;
	margin-right: 4px;
}

-->
</style>
<script type="text/JavaScript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
document.forms[0].descrip.focus();
}


function nextinv(x){
document.inventory.invpointer.value=x;
document.inventory.submit();
}

function previnv(x){
document.inventory.invpointer.value=x;
document.inventory.submit();
}

function deleting(x, prodname){
if (confirm("Are you sure you would like to delete "+prodname)){
	document.inventory.xdelete.value='1';
	previnv(x);
	}
}

<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}


//-->
</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../../IMAGES/left_arrow_dark.JPG','../../IMAGES/right_arrow_dark.JPG')" onunload="bodyonunload()">
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
<form name="inventory" action="" method="POST">
<input type="hidden" name="invpointer" value=""  />
<input type="hidden" name="xdelete" value=""  />
<table width="100%" height="219" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
  <tr>
    <td height="62" colspan="2"><table width="736" height="60" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="85" rowspan="2" align="left" valign="middle" title="Product description as it appears on the invoice." class="RequiredItems">Description</td>
        <td width="250" rowspan="2" align="left" valign="middle" class="Labels">
		
		<input name="descrip" type="text" class="Input" id="descrip" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['DESCRIP']; ?>" size="30" /></td>
       
	   
	   
	    <td rowspan="2" align="left" valign="middle" class="Labels"><label><input type="checkbox" name="specorder" <?php if ($row_INVENTORY['SPECORDER']=='1'){echo "CHECKED";}; ?> />Special Order</label> </td>
        <td width="96" height="35" align="left" valign="middle" class="RequiredItems">Abreviation</td>
        <td width="146" align="right" class="Labels"><input name="item" type="text" class="Input" id="item" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ITEM']; ?>" size="10"/></td>
      </tr>
      <tr>
        <td height="35" align="left" valign="middle" class="RequiredItems">BarCode</td>
        <td align="right" class="Labels"><input name="barcode" type="text" class="Input" id="barcode" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['BARCODE']; ?>" size="15" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2">
    
    
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="55" height="35" align="left" valign="middle"  title="This code categorizes this item, and determines the markup formula. See the Class button at the bottom of the screen." class="RequiredItems">Class</td>
        <td width="57" height="35" align="left" valign="middle" class="Labels"><input name="class" type="text" class="Input" id="class" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['CLASS']; ?>" size="1"/></td>
        <td width="73" align="left" valign="middle" class="Labels">&nbsp;</td>
        <td height="35" colspan="3" align="left" valign="middle" class="Labels"><label>Disp.Fee&nbsp;
            <input name="dispfee" type="text"  title="The dispensing fee if the source is the inventory" class="Inputright" id="dispfee" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['DISPFEE']; ?>" size="6"/>
        </label>&nbsp;          
          <label>B.Disp.Fee&nbsp;<input name="bdispfee" type="text" title="The dispensing fee if the product is dispensed sealed in its original container" class="Inputright" id="bdispfee" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['BDISPFEE']; ?>" size="6"/></label>&nbsp;<label>Disp.Code&nbsp;<input name="type" type="text" class="Input"  title="T for Tablets, C for Capsules M for mls, D for Drops" id="type" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['TYPE']; ?>" size="6"/>
          </label></td>
        <td width="169" height="35" align="right" valign="middle" class="Labels"><label>Vendor<input name="supplier" type="text" class="Input" id="supplier" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['SUPPLIER']; ?>" size="6"/></label></td>
      </tr>
      
      <tr>
        <td height="35" align="left" valign="middle" class="Labels">Narcotic</td>
        <td height="35" align="left" valign="middle" class="Labels">
        <input type="checkbox" name="govnarc" id="govnarc" value="1" <?php if ($row_INVENTORY['GOVNARC']=='1'){echo "checked='checked'";} ?> />
        </td>
        <td align="left" valign="middle" class="Labels"><label><input type="checkbox"  title="Tick this if a pharmacy label is required." name="label" <?php if ($row_INVENTORY['LABEL']=='1'){echo "CHECKED";}; ?> />&nbsp;Label</label></td>
        <td height="35" colspan="3" align="left" valign="middle"  title="The source of the dispensing fee. These three items are the choices." class="Labels2"><span class="RequiredItems">DF Source</span>
          &nbsp;&nbsp;<label><input name="dfyes" type="radio" value="1"  <?php if ($row_INVENTORY['DFYES']=='1'){echo "CHECKED";}; ?>/>&nbsp;T&amp;Fee</label>          
          <label><input name="dfyes" type="radio" value="2" <?php if ($row_INVENTORY['DFYES']=='2'){echo "CHECKED";}; ?> />&nbsp;Table</label>          
          <label><input name="dfyes" type="radio" value="3" <?php if ($row_INVENTORY['DFYES']=='3'){echo "CHECKED";}; ?>/>&nbsp;Invent.</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="checkbox" name="bulk" <?php if ($row_INVENTORY['BULK']=='1'){echo "CHECKED";}; ?> />&nbsp;Bulk</label></td>
        <td height="35" align="right" valign="middle" class="Labels"><label>Vendor Code <input name="vpartno" type="text" class="Input" id="vpartno" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['VPARTNO']; ?>" size="10"/></label></td>
      </tr>
      
      <tr>
        <td height="30" align="left" valign="middle" class="Labels">Location</td>
        <td height="30" align="left" valign="middle" class="Labels"><input name="seq" type="text" class="Input" id="seq" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['SEQ']; ?>" size="6" /></td>
        <td height="30" colspan="3" align="center" valign="middle" class="Labels">          
        
        <label>
          <input type="radio" name="arinvtype" id="radio2" value="P" <?php if ($row_INVENTORY['ARINVTYPE']=="P"){echo "checked";} ?>/>
          Pharm  </label>
          
          <label>
          <input type="radio" name="arinvtype" id="radio" value="F" <?php if ($row_INVENTORY['ARINVTYPE']=="F"){echo "checked";} ?> />
          Food</label>
                  
          <label><input type="radio" name="arinvtype" id="radio4" value="R" <?php if ($row_INVENTORY['ARINVTYPE']=="R"){echo "checked";} ?>/>
          Retail</label><label>
          <input type="radio" name="arinvtype" id="radio3" value="O" <?php if ($row_INVENTORY['ARINVTYPE']=="O" || (!isset($row_INVENTORY['ARINVTYPE']))){echo "checked";} ?>/>
          Other
		  </label></td>
        <td height="30" align="right" class="Labels2" colspan="2"><label>Memo<input name="memo" type="text" class="Input" id="memo" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['MEMO']; ?>" size="40"/>
        </label></td>
      </tr>
      <tr>
        <td height="0"></td>
        <td height="0"></td>
        <td height="0"></td>
        <td width="278" height="0"></td>
        <td width="17" height="0"></td>
        <td width="175" height="0"></td>
        <td height="0"></td>
      </tr>
    </table>
    
    
    </td>
  </tr>
  <tr>
    <td width="41%">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="48" height="30" class="Verdana11B">&nbsp;STOCK:</td>
        <td height="30" colspan="4" align="center" valign="middle" class="Labels">Order</td>
        </tr>
      <tr>
        <td height="32" class="Labels">&nbsp;</td>
        <td width="55" height="32" align="center" valign="middle" class="Labels">Min,<br />
          (units)</td>
        <td height="32" align="center" valign="middle" class="Labels">Qty.<br />
          (pkgs)</td>
        <td width="69" height="32" align="center" valign="middle" class="Labels">Max.<br />
          (units)</td>
        <td height="32" align="center" valign="middle" class="Labels">Safety<br />
          (%)</td>
      </tr>
      <tr>
        <td height="32" align="center" class="Labels">&nbsp;</td>
        <td height="32" align="center" class="Labels"><input name="orderpt" type="text" class="Inputright" id="orderpt" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ORDERPT']; ?>" size="5"/></td>
        <td height="32" align="center" class="Labels"><input name="orderqty" type="text" class="Inputright" id="orderqty" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ORDERQTY']; ?>" size="7"/></td>
        <td height="32" align="center" class="Labels"><input name="onorder" type="text" class="Inputright" id="onorder" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ONORDER']; ?>" size="7"/></td>
        <td height="32" align="center" class="Labels"><input name="safety" type="text" class="Inputright" id="safety" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['SAFETY']; ?>" size="3"/></td>
      </tr>
      <tr>
        <td height="32" colspan="2" align="left" valign="middle" class="Labels">Qty On Hand </td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="onhand" type="text" class="Inputright" id="onhand" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ONHAND']; ?>" size="7"/></td>
        <td height="32" align="right" valign="middle" class="Labels">Ordered</td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="ordered" type="text" class="Inputright" id="ordered" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['ORDERED']; ?>" size="7"/></td>
      </tr>
      <tr>
        <td height="32" colspan="3" align="left" valign="middle" class="Labels">Reserved Sales </td>
        <td height="32" align="left" valign="middle" class="Labels2"><?php echo $row_INVENTORY['RESERVEQTY']; ?></td>
        <td height="32" class="Labels">&nbsp;</td>
      </tr>
      <tr>
        <td height="32" colspan="2" align="left" valign="middle" class="Labels">Monitor Sales </td>
        <td height="32" align="left" valign="middle" class="Verdana11"><label><input type="checkbox" name="monitor" <?php if ($row_INVENTORY['MONITOR']=='1'){echo "CHECKED";}; ?> />  Yes</label></td>
        <td height="32" align="right" valign="middle" class="Labels"><!--Automatic--></td>
        <td height="32" class="Verdana11"><label class="hidden"><input type="checkbox" name="autoset" <?php if ($row_INVENTORY['AUTOSET']=='1'){echo "CHECKED";}; ?> />  Yes</label></td>
        </tr>
    </table></td>
    <td width="59%">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr class="RequiredItems">
        <td height="30" class="Labels">&nbsp;</td>
        <td height="30" class="Labels">&nbsp;</td>
        <td height="30" class="Labels">&nbsp;</td>
        <td height="30" class="Labels">&nbsp;</td>
        <td height="30" class="Labels">&nbsp;</td>
        <td height="18" class="Labels">&nbsp;</td>
      </tr>
      <tr>
        <td height="32" align="left" valign="middle" class="Labels">Selling Units </td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="sellunit" type="text" class="Inputright" id="sellunit" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['SELLUNIT']; ?>" size="8"/></td>
        <td height="32" colspan="2" class="Labels">Buying Units </td>
        <td height="32" colspan="2" align="right" class="Labels"><input name="uofm" id="uofm" type="text" class="Inputright" size="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['UOFM']; ?>"/></td>
        </tr>
      <tr>
        <td height="32" rowspan="2" align="left" valign="middle"  title="The number of units as bought in a sealed package" class="RequiredItems">Package Quantity </td>
        <td height="32" rowspan="2" align="left" valign="middle" class="Labels"><input name="pkgqty" type="text" class="Inputright" id="pkgqty" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['PKGQTY']; ?>" size="5"/></td>
        <td height="32" colspan="2" rowspan="2"  title="Use Class unless OVMA cost markup table is the reference." class="Labels">Markup Rules </td>
        <td height="14" colspan="2" class="Verdana11"><label><input name="markup" type="radio" value="1" <?php if ($row_INVENTORY['MARKUP']=='1'){echo "CHECKED";}; ?> />Class Based </label></td>
        </tr>
      <tr>
        <td height="14" colspan="2" class="Verdana11"><label><input name="markup" type="radio" value="2" <?php if ($row_INVENTORY['MARKUP']=='2'){echo "CHECKED";}; ?>/>Cost Based </label></td>
        </tr>
      <tr>
        <td height="32" align="left" valign="middle"  title="The price paid to the supplier for a complete package." class="RequiredItems">Package Cost </td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="cost" type="text" class="Inputright" id="cost" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['COST']; ?>" size="8"/></td>
        <td height="32" colspan="2" class="Verdana11"><label><input type="checkbox" name="manual"  title="Allows overide of computed Package and Unit prices with your price." value = "1" <?php if ($row_INVENTORY['MANUAL']=='1'){echo "CHECKED";}; ?> /> Man.Price</label></td>
        <td height="32" class="Labels">&nbsp;</td>
        <td height="32" class="Labels">&nbsp;</td>
      </tr>
      <tr>
        <td height="32" align="left" valign="middle"  title="The price of each item in the package if multiple units are sold as a complete package." class="Labels">Package Price</td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="price" type="text" class="Inputright" id="price" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['PRICE']; ?>" size="8" /></td>
        <td height="32" colspan="2"  title="The individual price if sold on a break-bulk basis." class="Labels">Unit Price </td>
        <td height="32" colspan="2" align="right" class="Labels"><input name="uprice" type="text" class="Inputright" id="uprice" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['UPRICE']; ?>" size="8"/></td>
        </tr>
      <tr>
        <td height="32" align="left" valign="middle"  title="The provincial tax rate if no harmonized Tax" class="Labels">Tax Rate </td>
        <td height="32" align="left" valign="middle" class="Labels"><input name="taxrate" type="text" class="Inputright" id="taxrate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['TAXRATE']; ?>" size="5"/></td>
        <td height="32" colspan="3" class="Labels">Value On Hand</td>
        <td height="32" align="right" valign="middle" class="Labels"><?php echo "$&nbsp;".number_format($row_INVENTORY['ONHAND']*$row_INVENTORY['COST'],2); ?>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="11%" height="35" align="left" class="Labels">Last Sale </td>
        <td width="19%" height="35" align="left" valign="middle" class="Labels"><?php if ($row_INVENTORY['LASTSALE']=='00/00/0000'){echo "";} else {echo $row_INVENTORY['LASTSALE'];} ?></td>
        <td width="34%" height="35" align="center" valign="middle" class="Labels">Margin: <?php echo number_format(($row_INVENTORY['PKGQTY']*$row_INVENTORY['PRICE']-$row_INVENTORY['COST']),2); ?></td>
        <td width="36%" height="35" align="left" valign="middle" class="Labels">Margin Pct. </td>
        </tr>
      <tr>
        <td height="35" align="left" class="Labels">Last Order </td>
        <td height="35" align="left" valign="middle" class="Labels"><input name="ldate" type="text" class="Input" id="ldate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['LDATE']; ?>" size="10" onclick="ds_sh(this<?php if ($itemid!="0"){echo ",'".substr($row_INVENTORY['LDATE'],0,2)."','".substr($row_INVENTORY['LDATE'],3,2)."','".substr($row_INVENTORY['LDATE'],6,4)."'";} ?>);"/> </td>
        <td height="35" class="Labels">&nbsp;</td>
        <td height="35" align="right" valign="middle" class="Labels"><label>Auto.Comment&nbsp;&nbsp;<input name="comment" type="text" class="Input" id="comment" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['COMMENT']; ?>" size="5"/>
        </label></td>
        </tr>
      <tr>
        <td height="35" align="left" class="Labels">Expiry Date </td>
        <td height="35" align="left" valign="middle" class="Labels"><input name="expdate" type="text" class="Input" id="expdate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_INVENTORY['EXPDATE']; ?>" size="10" onclick="ds_sh(this<?php if ($itemid!="0"){echo ",'".substr($row_INVENTORY['EXPDATE'],0,2)."','".substr($row_INVENTORY['EXPDATE'],3,2)."','".substr($row_INVENTORY['EXPDATE'],6,4)."'";} ?>);"/> </td>
        <td height="35" align="center" valign="bottom" class="RequiredItems">Items in blue must be completed </td>
        <td height="35" align="right" class="Labels"><input name="updateprice" type="button" class="hidden" value="UPDATE PRICE" /></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ButtonsTable">
      <tr>
        <td width="11%" height="44" align="center" valign="middle">&nbsp;</td>
        <td width="38%" align="right" valign="middle" id="xxprev" onmouseover="CursorToPointer(this.id);"><img src="../../IMAGES/left_arrow_light.JPG" alt="lal" width="28" height="28" id="Image1" onmouseover="MM_swapImage('Image1','','../../IMAGES/left_arrow_dark.JPG',1)" onmouseout="MM_swapImgRestore()"  onclick="previnv('<?php echo $ids[$key-1]; ?>');" title="Save changes and go to Previous Item"/></td>
        <td width="2%" align="right" valign="middle">&nbsp;</td>
        <td width="38%" height="44" align="left" valign="middle" id="xxnext" onmouseover="CursorToPointer(this.id);"><img src="../../IMAGES/right_arrow_light.JPG" alt="ral" width="28" height="28" id="Image2" onmouseover="MM_swapImage('Image2','','../../IMAGES/right_arrow_dark.JPG',1)" onmouseout="MM_swapImgRestore()"  onclick="nextinv('<?php echo $ids[$key+1]; ?>');" title="Save changes and go to Next Item"/></td>
        <td width="11%" align="center" valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td height="34" colspan="5" align="center" valign="middle">
		  <input name="save" type="submit" class="CustomizedButton1" value="SAVE" title="Save changes and go back to Search Screen"/>
		  <input name="class" type="button" class="CustomizedButton1" value="CLASS" onclick="window.open('../CONSTANTS/CLASS_REPORT.php','_blank', 'width=500, height=553, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no')" title="Look at Inventory Classes"/>
		  <input name="sales" type="button" class="CustomizedButton1" value="SALES" onclick="window.open('SEARCH_SALES.php?item=<?php echo $row_INVENTORY['VPARTNO']; ?>','_self')" title="Find who has bought this in the past" />
          <input name="turns" type="button" class="CustomizedButton1" value="TURNS" onclick="window.open('TURNS.php','_self')" disabled="disabled" />
          <input name="delete" type="button" class="CustomizedButton1" value="DELETE" onclick="deleting('<?php echo $ids[$key+1]; ?>','<?php echo $row_INVENTORY['ITEM']." (".$row_INVENTORY['DESCRIP'].")"; ?>','xdelete');" title="Remove this item from inventory" />
          <input name="scan" type="button" class="CustomizedButton1" value="SCAN" onclick="window.open('INVENTORY_SEARCH_SCREEN.php','_self')" title="Go to Search Screen"/>
          <input name="cancel" type="reset" class="CustomizedButton1" value="CANCEL" onclick="<?php if (isset($_GET['check'])){echo "window.open('INVENTORY_SEARCH_SCREEN.php?togoback=toindex','_self');";} else {echo "history.back();";}?>" title="Do not save. Go back to Search Screen" /></td>
        <input type="hidden" name="check" value="1"  />
        </tr>
    </table></td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>