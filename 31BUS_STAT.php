<?php 
//echo 'begun ' ;
session_start();
include("../ASSETS/tax.php");
require_once('../../tryconnection.php'); 
//echo ' now connected ' ;
mysql_select_db($database_tryconnection, $tryconnection);
//echo ' with database  ' ;
// $taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y'));
$taxname = 'HST' ;
// echo ' and tax ' ;
$GET_LAST = "SELECT DATE_FORMAT(LASTCLOSE,'%W %M %e %Y') AS MEDATE FROM PRACTICE ORDER BY LASTCLOSE DESC LIMIT 1" ;
$QUERY_D  = mysql_query($GET_LAST) or die(mysql_error()) ;
$ME_Date = mysql_fetch_assoc($QUERY_D) ;
$last = $ME_Date['MEDATE'] ;

$gethosp="SELECT HOSPNAME FROM CRITDATA LIMIT 1" ;
$Query_hosp = mysql_query($gethosp, $tryconnection) or die(mysql_error()) ;
$row_hosp = mysql_fetch_assoc($Query_hosp) ;
$hospname = $row_hosp['HOSPNAME'] ;

$getinv1 = "SELECT SUM(ITOTAL) AS INVOICES FROM ARINVOI WHERE INVNO <> '0000' AND  INSTR(PONUM,'CANC') = 0" ;
$getinv2 = "SELECT COUNT(INVNO) AS INVOICEN FROM ARINVOI  WHERE INVNO <> '0000' AND ITOTAL <> 0 AND INSTR(PONUM,'CANC') = 0" ;
$getcash1 = "SELECT SUM(AMTPAID) AS CASH1 FROM CASHDEP " ;
$getcash2 = "SELECT SUM(AMTPAID) AS CASH2 FROM ARCASHR " ;
$gettax = "SELECT SUM(GST) AS HST FROM ARGST " ;
$getar = "SELECT SUM(IBAL) AS AR1 FROM ARARECV " ;
$getcred = "SELECT SUM(CREDIT) AS AR2 FROM ARCUSTO " ;
$getyear = "SELECT YEAR(NOW()) AS YEAR" ;
$getmonth = "SELECT MONTH(NOW()) AS MONTH " ;

$yearago = "SELECT DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR) AS LASTYR" ;
$get_yrago = mysql_query($yearago, $tryconnection) or die(mysql_error()) ;
$row_lastyear = mysql_fetch_array($get_yrago) ;
$lastyear = $row_lastyear['LASTYR'] ;

$yearagoday = "SELECT DAY('$lastyear') AS DDATE" ;
$get_yragoday = mysql_query($yearagoday, $tryconnection) or die(mysql_error()) ;
$row_lastyeard = mysql_fetch_array($get_yragoday) ;
$lastyeard = $row_lastyeard['DDATE'] - 1;
echo ' The days to be subtracted are ' . $lastyeard ;
$yearago1 = "SELECT DATE_SUB('$lastyear', INTERVAL '$lastyeard' DAY) AS LASTBEG" ;
$get_yrago1 = mysql_query($yearago1, $tryconnection) or die(mysql_error()) ;
$row_lastyeard1 = mysql_fetch_array($get_yrago1) ;
$period_begin = $row_lastyeard1['LASTBEG'] ;
echo ' last year '.$lastyeard . ' beginning ' . $period_begin  ;
$get_inv1 = mysql_query($getinv1, $tryconnection) or die(mysql_error()) ;
$get_inv2 = mysql_query($getinv2, $tryconnection) or die(mysql_error()) ;
$get_cash1 = mysql_query($getcash1, $tryconnection) or die(mysql_error()) ;
$get_cash2 = mysql_query($getcash2, $tryconnection) or die(mysql_error()) ;
$get_tax = mysql_query($gettax, $tryconnection) or die(mysql_error()) ;
$get_ar = mysql_query($getar, $tryconnection) or die(mysql_error()) ;
$get_cred = mysql_query($getcred, $tryconnection) or die(mysql_error()) ;
$get_year = mysql_query($getyear, $tryconnection) or die(mysql_error()) ;
$get_month = mysql_query($getmonth, $tryconnection) or die(mysql_error()) ;
//echo ' did second      etc ' ;
$row_inv1 = mysql_fetch_array($get_inv1) ;
$row_inv2 = mysql_fetch_array($get_inv2) ;
$row_cash1 = mysql_fetch_array($get_cash1) ;
$row_cash2 = mysql_fetch_array($get_cash2) ;
$row_tax = mysql_fetch_array($get_tax) ;
$row_ar = mysql_fetch_array($get_ar) ;
$row_cred = mysql_fetch_array($get_cred) ;
$totcash = $row_cash1['CASH1'] + $row_cash2['CASH2'] ;
$totar = $row_ar['AR1'] - $row_cred['AR2'] ;
$row_thisyear = mysql_fetch_array($get_year) ;
/*
echo ' this year is ' . $row_thisyear['YEAR'] ;
$row_thismonth = mysql_fetch_array($get_month) ;
echo ' this month is ' . $row_thismonth['MONTH'] ;
$targetmonth = $row_thisyear['YEAR']*12 + $row_thismonth['MONTH'] ;
echo ' Target month is ' .$targetmonth ;
*/

$YTD_SALES1 = "SELECT SUM(ITOTAL) AS PASTI FROM ARYINVO WHERE YEAR(INVDTE)*12 + MONTH(INVDTE) >= $targetmonth - 11" ;
$YTD_SALES2 = "SELECT SUM(ITOTAL) AS LASTI FROM INVLAST WHERE INVNO <> '0000'" ;              
$YTD_CASH1 = "SELECT SUM(AMTPAID) AS PASTC FROM ARYCASH WHERE YEAR(DTEPAID)*12 + MONTH(DTEPAID) >= $targetmonth - 11" ;
$YTD_CASH2 = "SELECT SUM(AMTPAID) AS LASTC FROM LASTCASH" ;
$YTD_SALESN1 = "SELECT COUNT(INVNO) AS YINVOICEN FROM ARYINVO  WHERE INVNO <> '0000' AND ITOTAL <> 0 AND INSTR(PONUM,'CANC') = 0 
                AND YEAR(INVDTE)*12 + MONTH(INVDTE) >= $targetmonth - 11" ;
$YTD_SALESN2 = "SELECT COUNT(INVNO) AS LINVOICEN FROM INVLAST WHERE INVNO <> '0000' AND ITOTAL <> 0 AND INSTR(PONUM,'CANC') = 0 " ; 
$PYM_SALESN1 = "SELECT COUNT(INVNO) AS PLINVOICEN FROM ARYINVO  WHERE INVNO <> '0000' AND ITOTAL <> 0 AND INSTR(PONUM,'CANC') = 0 
                AND (YEAR(INVDTE)*12 + MONTH(INVDTE) = $targetmonth - 12)" ;
$PYM_SALESN2 = "SELECT COUNT(INVNO) AS PYINVOICEN FROM ARYINVO WHERE INVNO <> '0000' AND ITOTAL <> 0 AND INSTR(PONUM,'CANC') = 0 
                AND (YEAR(INVDTE)*12 + MONTH(INVDTE) < $targetmonth - 11) AND (YEAR(INVDTE)*12 + MONTH(INVDTE) >= $targetmonth - 23)" ;
$YTD_HST1 = "SELECT SUM(GST) AS TAX FROM ARYGST WHERE YEAR(INVDTE)*12 + MONTH(INVDTE) >= $targetmonth - 11" ;

$LASTYMON_SALES = "SELECT SUM(ITOTAL) AS PASTMI FROM ARYINVO WHERE YEAR(INVDTE)*12 + MONTH(INVDTE) = $targetmonth - 11" ;
$LASTYMON_CASH = "SELECT SUM(AMTPAID) AS PASTMC FROM ARYCASH WHERE YEAR(DTEPAID)*12 + MONTH(DTEPAID) = $targetmonth - 11" ;
$PYTD_SALES = "SELECT SUM(ITOTAL) AS PYTDI FROM ARYINVO WHERE (YEAR(INVDTE)*12 + MONTH(INVDTE)) < ($targetmonth - 11) AND (YEAR(INVDTE)*12) + MONTH(INVDTE) >= ($targetmonth - 23)" ;
$PYTD_CASH = "SELECT SUM(AMTPAID) AS PYTDC FROM ARYCASH WHERE (YEAR(DTEPAID)*12 + MONTH(DTEPAID)) < ($targetmonth - 11) AND (YEAR(DTEPAID)*12) + MONTH(DTEPAID) >= ($targetmonth - 23)" ;
//echo ' through the SQL ' ;
$get_ytd1 = mysql_query($YTD_SALES1, $tryconnection) or die(mysql_error())  ;
$get_ytd2 = mysql_query($YTD_SALES2, $tryconnection) or die(mysql_error())  ;
$get_ycash1 = mysql_query($YTD_CASH1, $tryconnection) or die(mysql_error())  ;
$get_ycash2 = mysql_query($YTD_CASH2, $tryconnection) or die(mysql_error())  ;
$get_yhst = mysql_query($YTD_HST1, $tryconnection) or die(mysql_error()) ;
$get_yinvn1 = mysql_query($YTD_SALESN1, $tryconnection) or die(mysql_error()) ;
$get_yinvn2 = mysql_query($YTD_SALESN2, $tryconnection) or die(mysql_error()) ;
$get_pastmi = mysql_query($LASTYMON_SALES, $tryconnection) or die(mysql_error())  ;
$get_pastmc = mysql_query($LASTYMON_CASH, $tryconnection) or die(mysql_error())  ;
$get_pytdi = mysql_query($PYTD_SALES, $tryconnection) or die(mysql_error())  ;
$get_pytdc = mysql_query($PYTD_CASH, $tryconnection) or die(mysql_error())  ;
$get_pymn1 = mysql_query($PYM_SALESN1, $tryconnection) or die(mysql_erro()) ;
$get_pymn2 = mysql_query($PYM_SALESN2, $tryconnection) or die(mysql_erro()) ;

$row_ytd1 = mysql_fetch_array($get_ytd1) ;
$row_ytd2 = mysql_fetch_array($get_ytd2) ;
$row_ycash1 = mysql_fetch_array($get_ycash1) ;
$row_ycash2 = mysql_fetch_array($get_ycash2) ;
$row_yhst = mysql_fetch_array($get_yhst) ;
$row_yinvn1 = mysql_fetch_array($get_yinvn1) ;
$row_yinvn2 = mysql_fetch_array($get_yinvn2) ;
$row_pastmi = mysql_fetch_array($get_pastmi) ;
$row_pastmc = mysql_fetch_array($get_pastmc) ;
$row_pytdi = mysql_fetch_array($get_pytdi) ;
$row_pytdc = mysql_fetch_array($get_pytdc) ;
$row_pymn1 = mysql_fetch_array($get_pymn1) ;
$row_pymn2 = mysql_fetch_array($get_pymn2) ;
//echo ' now the past ' ;
$thisi = $row_ytd1['PASTI'] + $row_ytd2['LASTI'] + $row_inv1['INVOICES'] ;
$thisn = $row_yinvn1['YINVOICEN'] ;
$thisn2 = $row_yinvn2['LINVOICEN'] ;
$thisc = $row_ycash1['PASTC'] + $row_ycash2['LASTC'] + $totcash ;
$thish = $row_yhst['TAX'] + $row_tax['HST'] ;
$pastmi = $row_pastmi['PASTMI'] ;
$pastmc = $row_pastmc['PASTMC'] ;
$pytdi = $row_pytdi['PYTDI'] ;
$pytdc = $row_pytdc['PYTDC'] ;
$pymn1 = $row_pymn1['PLINVOICEN'] ;
$pymn2 = $row_pymn1['PYINVOICEN'] ;
echo ' YTD CASH ' . $thisc ;
echo ' YTD INVOICES ' . $thisi ;
echo ' YTD # of invoices ' . $thisn . '  ' . $thisn2;
echo ' This month, last year invoices '.$pastmi . ' this month, last year cash ' . $pastmc ;
echo '</br>' ;
echo ' Previous year invoices ' . $pytdi . ' Previous year cash ' . $pytdc ;
echo '</br>' ;
echo ' This month, last year invoice # ' . $pymn1 . ' Last year ytd invoice# ' . $pymn2 ;
echo ' and third' ;
?>
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Business Status</title>
<style type="text/css">
<!--
.style1 {font-size: 12px}
-->
</style>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload()
{
document.getElementById('inuse').innerText=localStorage.xdatabase;

var irresults=document.getElementById('irresults');
irresults.scrollTop = irresults.scrollHeight;
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

</script>

<style type="text/css">
#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

</style>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../ASSETS/calendar.js"></script>
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
<table width="100%" height="30" border="0"cellpadding="0" cellspacing="0"> 
 <tr> <td &nbsp; ></td>
 </tr>
 </table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr id="prthospname">
    <td colspan="2" height="30" align="center" class="Verdana13B"><?php echo $hospname ; ?>
    </td>
    </tr>
    <tr id="prtpurpose">
    <td colspan="1" height="15" align="center" class="Arial15">Business Status Report from <?php echo $last; ?> to <?php echo date('l j F Y')?><br />&nbsp;</td>
    </tr>
    </table>
<table width="45%" height="30" border="1" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <th width="50%" height="30" scope="row"><div align="left" ><span class="Arial15">&nbsp;Invoice amount</span></div></th>
    <td width="50%" align="right" ><span class="Arial15"><?php  setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_inv1['INVOICES']);?>&nbsp;</span></td>
  </tr>
  <tr>
    <th width="50%"  height="30"  scope="row"><div align="left"><span class="Arial15">&nbsp;No. of Invoices</span></div></th>
    <td align="right" ><span class="Arial15"><?php echo $row_inv2['INVOICEN'];?>&nbsp;</span></td>
  </tr>
  <tr>
    <th width="50%" height="30"  scope="row"><div align="left" class="Arial15">&nbsp;Cash received</div></th>
    <td align="right" ><span class="Arial15"><?php  setlocale(LC_MONETARY, 'en_US');echo money_format('%(#10n',$totcash );?>&nbsp;</span></td>
  </tr>
  <tr>
    <th  width="50%"  height="30" scope="row"><div align="left" class="Arial15">&nbsp;<?php echo $taxname.' collected' ;?></div></th>
    <td  align="right" ><span class="Arial15"><?php  setlocale(LC_MONETARY, 'en_US');echo money_format('%(#10n',$row_tax['HST']);?>&nbsp;</span></td>
  </tr>
  <tr>
    <th  width="50%" height="30"  scope="row"><div align="left" class="Arial15">&nbsp;Net Receivables</div></th>
    <td  align="right" ><span class="Arial15"><?php  setlocale(LC_MONETARY, 'en_US');echo money_format('%(#10n',$totar) ;?></span>&nbsp;</td>
  </tr>
  <tr>
    <th  width="50%"  height="30" scope="row"><div align="left" class="Arial15">&nbsp;Credits</div></th>
    <td  align="right" ><span class="Arial15"><?php  setlocale(LC_MONETARY, 'en_US');echo money_format('%(#10n',$row_cred['AR2']) ;?>&nbsp;</span></td>
  </tr>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="7">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="history.back();" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
  </tr>
</table>
<!--
<p>&nbsp;</p> -->
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>