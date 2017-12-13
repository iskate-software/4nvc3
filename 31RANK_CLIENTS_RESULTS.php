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
mysqli_select_db($tryconnection, $database_tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysqli_query($tryconnection, $startdate) or die(mysqli_error($mysqli_link));
$startdate=mysqli_fetch_array($startdate);

echo ' start ' . $startdate[0] ;
// In spite of the name, the following is the search argument in the invoice line item
$company = $_GET['company'] ;
echo ' search '. $company ;
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
echo ' end ' . $enddate[0] ;

$doc = $_GET['clinician2'] ;

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$gethosp="SELECT HOSPNAME FROM CRITDATA LIMIT 1" ;
$Query_hosp = mysqli_query($tryconnection, $gethosp) or die(mysqli_error($mysqli_link)) ;
$row_hosp = mysqli_fetch_array($Query_hosp) ;
$hospname = $row_hosp['HOSPNAME'] ;

$search_SETUP0 = "DROP TABLE IF EXISTS RANKTEMP " ;
$search_SETUP1 = "CREATE TABLE RANKTEMP (CUSTNO INT(8), ITOTAL FLOAT(10,2))" ;
$search_SETUP2 = "INSERT INTO RANKTEMP (CUSTNO, ITOTAL) SELECT CUSTNO,ITOTAL FROM ARYINVO WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' AND INSTR(PONUM,'CANC.') = 0 AND INVNO <> '0000'" ;
$search_SETUP3 = "INSERT INTO RANKTEMP (CUSTNO, ITOTAL) SELECT CUSTNO,ITOTAL FROM INVLAST WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' AND INSTR(PONUM,'CANC.') = 0 AND INVNO <> '0000'";
$search_SETUP4 = "INSERT INTO RANKTEMP (CUSTNO, ITOTAL) SELECT CUSTNO,ITOTAL FROM ARINVOI WHERE INVDTE >= '$startdate[0]' AND INVDTE <= '$enddate[0]' AND INSTR(PONUM,'CANC.') = 0 AND INVNO <> '0000'";
$search_SETUP5 = "DROP TABLE IF EXISTS FINALRANK " ;
$search_SETUP6 = "CREATE TABLE FINALRANK LIKE RANKTEMP" ;
$search_SETUP7 = "INSERT INTO FINALRANK SELECT CUSTNO, SUM(ITOTAL) FROM RANKTEMP GROUP BY CUSTNO" ;
$search_SETUP8 = "ALTER TABLE FINALRANK ADD COLUMN COMPANY CHAR(40)" ;
$search_SETUP9 = "UPDATE FINALRANK JOIN ARCUSTO ON FINALRANK.CUSTNO = ARCUSTO.CUSTNO SET FINALRANK.COMPANY = CONCAT(ARCUSTO.COMPANY,', ',ARCUSTO.CONTACT) ;" ;
$search_ARINVOI= "SELECT  COMPANY,ITOTAL FROM FINALRANK WHERE ITOTAL > '$company' ORDER BY ITOTAL DESC";
 if ($doc != "0") {$DOCONLY = "DELETE FROM RANKFINAL WHERE INVDOC <> '$doc' " ;}
$search_SELECT = "SELECT SUM(ITOTAL) AS TOTAL,  COUNT(CUSTNO) AS COUNT FROM FINALRANK  WHERE ITOTAL > '$company' ";
$search_TOTAL = "SELECT SUM(ITOTAL) AS TOTAL,  COUNT(CUSTNO) AS COUNT FROM FINALRANK  ";
$Query_0 = mysqli_query($tryconnection, $search_SETUP0) or die(mysqli_error($mysqli_link)) ;
$Query_1 = mysqli_query($tryconnection, $search_SETUP1) or die(mysqli_error($mysqli_link)) ;
$Query_2 = mysqli_query($tryconnection, $search_SETUP2) or die(mysqli_error($mysqli_link)) ;
//$Query_3 = mysql_query($search_SETUP3, $tryconnection) or die(mysql_error()) ;
//$Query_4 = mysql_query($search_SETUP4, $tryconnection) or die(mysql_error()) ;
$Query_5 = mysqli_query($tryconnection, $search_SETUP5) or die(mysqli_error($mysqli_link)) ;
$Query_6 = mysqli_query($tryconnection, $search_SETUP6) or die(mysqli_error($mysqli_link)) ;
$Query_7 = mysqli_query($tryconnection, $search_SETUP7) or die(mysqli_error($mysqli_link)) ;
$Query_8 = mysqli_query($tryconnection, $search_SETUP8) or die(mysqli_error($mysqli_link)) ;
$Query_9 = mysqli_query($tryconnection, $search_SETUP9) or die(mysqli_error($mysqli_link)) ;

if ($doc != "0") {
 $Query_doc = mysqli_query($tryconnection, $DOCONLY) or die(mysqli_error($mysqli_link)) ;
}
$ARINVOI=mysqli_query($tryconnection, $search_ARINVOI) or die(mysqli_error($mysqli_link)) ;
$row_ARINVOI=mysqli_fetch_assoc($ARINVOI);

$Query_TOT = mysqli_query($tryconnection, $search_TOTAL) or die(mysqli_error($mysqli_link)) ;
$row_TOT = mysqli_fetch_assoc($Query_TOT) ;

$Query_SEL = mysqli_query($tryconnection, $search_SELECT) or die(mysqli_error($mysqli_link)) ;
$row_SEL = mysqli_fetch_assoc($Query_SEL) ;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>RANK CLIENTS BY EXPENDITURE FROM <?php echo $_GET['startdate'].' TO '.$_GET['enddate'] . ' ' . $doc ; ?></title>
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
    <td colspan="3" height="30" align="center" class="Verdana13B"><?php echo $hospname ; ?>
    </td>
    </tr>
     <tr id="prtpurpose">
    <td colspan="3" height="15" align="center" class="Verdana13">Rank Clients By Expenditure  <?php   if ($startdate == $enddate) {echo $stdum ;} else {echo $stdum .' - '. $enddum ;} if ($doc != "0") {echo ' for ' . $doc ;}?><br />&nbsp;</td>
     </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="60" align="center">Ranking</td>
    <td width="187"align="center">Client</td>
    <td width="253" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount</td>
  </tr>
  <tr>
    <td colspan="3" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  $rank = 1 ;
  setlocale(LC_MONETARY, 'en_US');
  do {
  echo '
  <tr id="'.$rank.'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.'ARINVOI'.'&invdte='.$row_ARINVOI['INVDTE'].'&invno='.$row_ARINVOI['INVNO'].'\',\'_blank\')">
    <td width="60" align="left" class="Verdana13">'.$rank.'&nbsp;</td>
    <td width="167" class="Verdana13">'.'&nbsp;'.substr($row_ARINVOI['COMPANY'],0,40).'</td>
    <td width="219" class="Verdana13">'.'&nbsp;'. money_format('%(#10n',$row_ARINVOI['ITOTAL']).'</td>
  </tr>';
  $rank ++ ;
  }
  while ($row_ARINVOI=mysqli_fetch_assoc($ARINVOI));
  
  ?>
  
</table>
    </div>
    

    <table width="70%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Client Ranking Summary</td>
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
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo 'Total clients:&nbsp;&nbsp;' .$row_TOT['COUNT'] . '&nbsp; dollar total: &nbsp;'.  money_format('%(#10n',$row_TOT['TOTAL']) . '</br>'
           . ' Selected clients:&nbsp;&nbsp;' .$row_SEL['COUNT'].'&nbsp;&nbsp; for dollar total ' .  money_format('%(#10n',$row_SEL['TOTAL']) ; ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8  ">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='INV_REPORTS_DIR.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
