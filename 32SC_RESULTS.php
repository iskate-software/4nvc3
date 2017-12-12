<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");
$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$invdate = $_GET['invdate'] ;
$scdate = $_GET['scdate'] ;
$svpct = $_GET['svpct'] ;
$minbal = $_GET['minbal'] ;
$minchg = $_GET['minsvchg'] ;

$scdate1="SELECT STR_TO_DATE('$scdate','%m/%d/%Y')";
$scdate2=mysql_query($scdate1, $tryconnection) or die(mysql_error());
$scdate3=mysql_fetch_array($scdate2);
                                      
$invdte1="SELECT STR_TO_DATE('$invdate','%m/%d/%Y')";
$invdte2=mysql_query($invdte1, $tryconnection) or die(mysql_error());
$invdte3=mysql_fetch_array($invdte2);

$SC0 = "DROP TEMPORARY TABLE IF EXISTS SC1" ;
$SC0A = mysql_query($SC0, $tryconnection) or die(mysql_error()) ;

$SC00 = "DROP TEMPORARY TABLE IF EXISTS SC2" ;
$SC00A = mysql_query($SC00, $tryconnection) or die(mysql_error()) ;

$SC1 = "CREATE TEMPORARY TABLE SC1 LIKE ARARECV" ;
$SC1A = mysql_query($SC1, $tryconnection) or die(mysql_error()) ;

$SC2 = "ALTER TABLE SC1 DROP COLUMN UNIQUE1" ;
$SC2A = mysql_query($SC2, $tryconnection) or die(mysql_error()) ;

$SC3 = "INSERT INTO SC1 (CUSTNO,COMPANY,INVNO,INVDTE,IBAL) SELECT CUSTNO, COMPANY,INVNO,INVDTE,IBAL FROM ARARECV WHERE INVDTE <= '$scdate3[0]' ";
$SC3A = mysql_query($SC3, $tryconnection) or die(mysql_error()) ;

$SC4 = "DELETE FROM SC1 WHERE IBAL = 0"  ;
$SC4A = mysql_query($SC4, $tryconnection) or die(mysql_error()) ;

$SC5 = "CREATE TEMPORARY TABLE SC2 LIKE SC1" ;
$SC5A = mysql_query($SC5, $tryconnection) or die(mysql_error()) ;

$SC6 = "INSERT INTO SC2 (CUSTNO,COMPANY,IBAL) SELECT CUSTNO,COMPANY,SUM(IBAL) FROM SC1 GROUP BY CUSTNO" ;
$SC6A = mysql_query($SC6, $tryconnection) or die(mysql_error()) ;

$SC7 = "UPDATE SC2 JOIN ARCUSTO ON (SC2.CUSTNO = ARCUSTO.CUSTNO) SET IBAL = 0 WHERE ARCUSTO.SVC = 0" ;
$SC7A = mysql_query($SC7, $tryconnection) or die(mysql_error()) ;

$SC8 = "DELETE FROM SC2 WHERE IBAL < '$minbal' ";
$SC8A = mysql_query($SC8, $tryconnection) or die(mysql_error()) ;

$SC9 = "UPDATE SC2 SET IBAL = IBAL * ('$svpct'/100.00) ";
$SC9A = mysql_query($SC9, $tryconnection) or die(mysql_error()) ;

$SC10 = "UPDATE SC2 SET IBAL = '$minchg' WHERE IBAL < '$minchg' ";
$SC10A = mysql_query($SC10, $tryconnection) or die(mysql_error()) ;

$SC11 = "UPDATE SC2 SET INVNO = '0000',INVDTE = '$invdte3[0]', PONUM = 'SERVICE CHARGE', TAX = 0, PTAX = 0,  AMTPAID = 0,  DISCOUNT = 0,  REFNO = ' ', SALESMN = ' ', DTEPAID = '0000-00-00',  ITOTAL = IBAL" ;
$SC11A = mysql_query($SC11, $tryconnection) or die(mysql_error()) ;

$SC15 = "SELECT SUM(IBAL) AS IBAL FROM SC2" ;
$SC15A = mysql_query($SC15, $tryconnection) or die(mysql_error()) ;
$row_SC15B = mysql_fetch_assoc($SC15A) ;
$sctot = $row_SC15B['IBAL'] ;

$search_ARINVOI="SELECT INVNO,SC2.CUSTNO,CONCAT(ARCUSTO.COMPANY,', ',ARCUSTO.CONTACT) AS 'COMPANY', DATE_FORMAT(INVDTE, '%m/%d/%Y') AS 'INVDTE',ITOTAL,TAX,AMTPAID,IBAL FROM SC2 JOIN ARCUSTO ON (SC2.CUSTNO = ARCUSTO.CUSTNO) ORDER BY ARCUSTO.COMPANY,CONTACT ASC";
$search_NET = "SELECT SUM(ITOTAL - PTAX - TAX) AS Total_NET FROM SC2";
$search_TAX = "SELECT SUM(TAX) AS Total_TAX FROM SC2";
$search_PST = "SELECT SUM(PTAX) AS Total_PST FROM SC2";

$ARINVOI=mysql_query($search_ARINVOI, $tryconnection ) or die(mysql_error());
$row_ARINVOI=mysql_fetch_assoc($ARINVOI);

$NET = mysql_query($search_NET, $tryconnection ) or die(mysql_error()) ;
$TAX = mysql_query($search_TAX, $tryconnection ) or die(mysql_error()) ;
$PST = mysql_query($search_PST, $tryconnection ) or die(mysql_error()) ;

$row_NET = mysql_fetch_array($NET) ;
$row_TAX = mysql_fetch_array($TAX) ;
$row_PST = mysql_fetch_array($PST) ;

$totalsc = $row_NET + $row_TAX + $row_PST ;

$SCCHECK = "SELECT COUNT(INVNO) AS INVNO FROM ARARECV WHERE INVNO = '0000' AND INVDTE = '$invdte3[0]' AND IBAL > 0" ;
$get_check=mysql_query($SCCHECK, $tryconnection) or die(mysql_error()) ;
$row_SCCHECK = mysql_fetch_assoc($get_check) ;
$check = $row_SCCHECK['INVNO'] ;

if ($check[0] == 0 ) {
$SC12 = "INSERT INTO ARARECV (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN, PONUM,REFNO, DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME)
 SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN, PONUM,REFNO, DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME FROM SC2 ";
$SC12A = mysql_query($SC12, $tryconnection) or die(mysql_error()) ;

$SC13 = "INSERT INTO ARINVOI (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY, SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,DATETIME) SELECT INVNO,INVDTE,INVTIME,CUSTNO, COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,DATETIME FROM SC2" ;
$SC13A = mysql_query($SC13, $tryconnection) or die(mysql_error()) ;

$SC14 = "UPDATE ARINVOI JOIN ARARECV ON (ARINVOI.INVNO = ARARECV.INVNO AND ARINVOI.CUSTNO = ARARECV.CUSTNO AND ARINVOI.INVDTE = ARARECV.INVDTE) SET ARINVOI.UNIQUE1 = ARARECV.UNIQUE1 ";
$SC14A = mysql_query($SC14, $tryconnection) or die(mysql_error()) ;

$SC17="UPDATE ARCUSTO JOIN SC2 ON SC2.CUSTNO = ARCUSTO.CUSTNO SET ARCUSTO.BALANCE = ARCUSTO.BALANCE + SC2.ITOTAL";
$SC17A=mysql_query($SC17, $tryconnection) or die(mysql_error()) ;

$SC16 = "INSERT INTO SALESCAT SET INVNO = '0000', INVDTE = '$invdte3[0]',INVMAJ = 98, INVREVCAT = 98, INVDECLINE = 0, INVTOT = '$totalsc[0]' ";
$SC16A = mysql_query($SC16, $tryconnection) or die(mysql_error()) ;
}
else {
$SCCHECK1 = "SELECT SUM(IBAL) AS IBAL FROM ARARECV WHERE INVNO = '0000' AND INVDTE = '$invdte3[0]' AND IBAL > 0" ;
$get_check1=mysql_query($SCCHECK1, $tryconnection) or die(mysql_error()) ;
$row_SCCHECK1 = mysql_fetch_assoc($get_check1) ;
$totalbilled = $row_SCCHECK1['IBAL'] ;
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SERVICE CHARGE INVOICE REGISTER FOR <?php echo $_GET['invdate']  ;?></title>
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
<!--              <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->		</ul>
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
    <td colspan="8" height="30" align="center" class="Verdana13B"><script type="text/javascript">document.write(localStorage.hospname);</script>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="12" height="15" align="center" class="Verdana13">Service Charge Invoice Register for Month End Closing of <?php echo $_GET['invdate']  ;?><br />&nbsp;</td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="" align="right">Inv.#&nbsp;</td>
    <td width="120" align="center">Date</td>
    <td width="204">Client</td>
    <td width="65" align="right">Amount</td>
    <td width="65" align="right"><?php echo substr($taxname,0,3); ?></td>
    <td width="65" align="right">Total</td>
    <td width="65" align="right">On Acct.</td>
    <td width="65" align="right">Payment</td>
  </tr>
  <tr>
    <td colspan="8" class="Verdana12" align="center">
    
    <div id="irresults2">
    
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php 
  if ($check[0] == 0 ) {
  do {
  echo '
  <tr id="'.$row_ARINVOI['INVNO'].'" onmouseover="highliteline(this.id,\'#DCF6DD\'); CursorToPointer(this.id);" onmouseout="whiteoutline(this.id)" onclick="window.open(\'../../IMAGES/CUSTOM_DOCUMENTS/INVOICE_PREVIEW2.php?file2search='.$_GET['file2search'].'&invdte='.$row_ARINVOI['INVDTE'].'&invno='.$row_ARINVOI['INVNO'].'\',\'_blank\')">
    <td width="" align="right" class="Verdana13">'.$row_ARINVOI['INVNO'].'&nbsp;</td>
    <td width="120" align="center" class="Verdana13">'.$row_ARINVOI['INVDTE'].'</td>
    <td width="204" class="Verdana13">'.substr($row_ARINVOI['COMPANY'],0,29).'</td>
    <td width="65" align="right" class="Verdana13">'.number_format(($row_ARINVOI['ITOTAL']-$row_ARINVOI['TAX']),2).'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['TAX'].'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['ITOTAL'].'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['IBAL'].'</td>
    <td width="65" align="right" class="Verdana13">'.$row_ARINVOI['AMTPAID'].'</td>
  </tr>';
  }
  while ($row_ARINVOI=mysql_fetch_assoc($ARINVOI));
  } else {
  echo '<br /><br /><br /><tr><td class="Verdana13BRed" align="center">'.' There are already '.$check[0] . '  invoices for this date, totalling ' . $totalbilled[0] .'</td></tr>' ;
  echo '<tr> <td &nbsp; ></td></tr> ' ;
  echo '<tr> <td class="Verdana13" align="center">'.'You have already run this. Just press Finished. '.'</td></tr>0' ;
  echo '</class> ' ;
  }

  ?>
</table>
    </div>
    
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">&nbsp;<br />Service Charge Summary</td>
    </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="22%" height="18" class="Verdana12">&nbsp;</td>
    <td width="28%" class="Verdana12">Net</td>
    <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0]); ?></td>
    <td width="24%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">Tax</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_TAX[0]); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" class="Verdana12">&nbsp;</td>
    <td class="Verdana12">PST</td>
    <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_PST[0]); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
    <td height="1" colspan="2"><hr  /></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
    <td height="22" valign="top" class="Verdana13B">Grand Total</td>
    <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0] + $row_TAX[0] + $row_PST[0]); ?></td>
    <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="8">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='MONTH_END_DIRECTORY.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button"  type="button" class="button" id="button" value="CANCEL" onclick="history.back();"/></td>
  </tr>
</table>

<!-- InstanceEndEditable --></div>

</body>
<!-- InstanceEnd --></html>


