<?php
session_start();
require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);

//if (isset($_POST['start']) || isset($_POST['close'])) {

//prep the data, as on a full month closing, it seems to zap the tables before it can print them....

$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysql_error());
$closedate=mysql_fetch_array($closedate);

// now do funny things with this, to cope with the fact that most transactions have hour,min,sec of 00:00:00,
// but those that do not, get cut out of the <= comparison. So add 23 hours and 55 mins to the base date.

$Round_about_midnight = "SELECT DATE_ADD('$closedate[0]', INTERVAL '23:55' HOUR_MINUTE) AS LATER" ;
$Bump_it = mysql_query($Round_about_midnight, $tryconnection) or die(mysql_error()) ;
$Get_Bump = mysql_fetch_assoc($Bump_it) ;
$closedate[0] = $Get_Bump['LATER'] ;

$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%D %M %Y') " ;
$clm = mysql_query($closemonth, $tryconnection) or die(mysql_error()) ;
$clm1 = mysql_fetch_array($clm) ;

$clm2 = $clm1[0] ;



$DOCTAB11 = "TRUNCATE TABLE DOCTAB1" ;
$DOCAVG1 = mysql_query($DOCTAB11, $tryconnection) or die(mysql_error()) ;
echo ' DOCTAB1 zapped ' ;
$SETUP3 = "DROP TABLE IF EXISTS ARTEMPC" ;
$DOIT3 = mysql_query($SETUP3, $tryconnection) or die(mysql_error()) ;

$SETUP4 = "CREATE TABLE ARTEMPC LIKE CASHDEP" ;
$DOIT4 = mysql_query($SETUP4, $tryconnection) or die(mysql_error()) ;


$SETUP5 = "INSERT INTO ARTEMPC SELECT * FROM CASHDEP WHERE DTEPAID <= '$closedate[0]' and INSTR(REFNO, 'Corrn') = 0 AND INSTR(REFNO , 'DEP.AP') = 0";
$DOIT5 = mysql_query($SETUP5, $tryconnection) or die(mysql_error()) ;
$SETUP6 = "INSERT INTO ARTEMPC SELECT * FROM ARCASHR WHERE DTEPAID <= '$closedate[0]' and INSTR(REFNO, 'Corrn') = 0  AND INSTR(REFNO , 'DEP.AP') = 0";
$DOIT6 = mysql_query($SETUP6, $tryconnection) or die(mysql_error()) ;


$LIMIT1 = "SELECT FIRSTINV FROM PREFER LIMIT 1" ;
$DOIT1 = mysql_query($LIMIT1, $tryconnection ) or die(mysql_error()) ;
$FIRSTINV = mysql_fetch_array($DOIT1);

$LIMIT2 = "SELECT LASTINV FROM PREFER LIMIT 1" ;
$DOIT2 = mysql_query($LIMIT2, $tryconnection ) or die(mysql_error()) ;
$LASTINV = mysql_fetch_array($DOIT2);

$SETUP1 = "DROP  TABLE IF EXISTS ARTEMPI" ;
$SETUP2 = "CREATE TABLE ARTEMPI LIKE ARINVOI" ;
$SETUP3 = "INSERT INTO ARTEMPI SELECT * FROM ARINVOI WHERE INVDTE <= '$closedate[0]' " ;
$DOIT3 = mysql_query($SETUP1, $tryconnection ) or die(mysql_error()) ;
$DOIT4 = mysql_query($SETUP2, $tryconnection ) or die(mysql_error()) ;
$DOIT5 = mysql_query($SETUP3, $tryconnection ) or die(mysql_error()) ;

$SETUP1 = "DROP TABLE IF EXISTS MSALES" ;
$PREP1 = mysql_query($SETUP1, $tryconnection) or die(mysql_error()) ;

$SETUP2 = "CREATE TABLE MSALES LIKE SALESCAT" ;
$PREP2 = mysql_query($SETUP2, $tryconnection) or die(mysql_error()) ;

// $SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]' AND INVNO >='$FIRSTINV[0]' AND INVNO <= '$LASTINV[0]' AND INVDECLINE=0"  ; 
$SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]'  AND INVDECLINE=0"  ;
$PREP3 = mysql_query($SETUP3, $tryconnection) or die(mysql_error()) ;

// Clean up any weird revenue mapping.

$SETUP4 = "UPDATE MSALES SET INVREVCAT = INVREVCAT / 10 * 10, INVMAJ = INVMAJ / 10 * 10 " ;
$PREP4 = mysql_query($SETUP4, $tryconnection) or die(mysql_error()) ;

$DOCTAB12 = "CREATE  TABLE DOCTAB1 (DOCTOR VARCHAR(40), CAN9 FLOAT(8,2), FEL FLOAT(8,2), EQ FLOAT(8,2), BOV FLOAT(8,2),
            CAP FLOAT(8,2), PORC FLOAT(8,2), AV FLOAT(8,2), OTHER FLOAT(8,2), INVOICES INT(7), TOTAL FLOAT(9,2), AVERAGE FLOAT(9.2))" ;
//$DOCAVG2 = mysql_query($DOCTAB12, $tryconnection) or die(mysql_error()) ;

$DOCTAB13 =  "INSERT INTO DOCTAB1  (DOCTOR)  (SELECT DISTINCT INVORDDOC FROM MSALES JOIN DOCTOR ON INVORDDOC = DOCTOR ) ORDER BY PRIORITY " ;
$DOCAVG3 = mysql_query($DOCTAB13, $tryconnection) or die(mysql_error()) ;
echo ' Doctors added  now trying... ' ;
$DOCTAB13A = "UPDATE DOCTAB1 SET CAN9 = 0, FEL = 0, EQ = 0, BOV = 0, CAP = 0, PORC = 0, AV = 0, OTHER = 0, INVOICES = 0, TOTAL = 0, AVERAGE = 0" ;
echo $DOCTAB13A . '</br>' ;
$DOCAVG3A = mysql_query($DOCTAB13A, $tryconnection) or die(mysql_error()) ;
 echo ' Zeroed ' ;
$DOCTAB14 = "SELECT INVORDDOC,SUM(INVTOT) AS INVTOT,INVLGSM FROM MSALES WHERE INVDECLINE <> 1 AND INVTOT IS NOT NULL GROUP BY INVORDDOC, INVLGSM" ;
$DOCAVG4 = mysql_query($DOCTAB14, $tryconnection) or die(mysql_error()) ;

$DOCTAB14A = "SELECT INVORDDOC,SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVDECLINE <> 1 AND INVTOT IS NOT NULL GROUP BY INVORDDOC" ;
$DOCAVG4A = mysql_query($DOCTAB14A, $tryconnection) or die(mysql_error()) ;
            
$DOCTAB15 = "SELECT COUNT(DISTINCT INVNO) AS INVNO,INVORDDOC FROM MSALES GROUP BY INVORDDOC" ;
$DOCAVG5 = mysql_query($DOCTAB15, $tryconnection) or die(mysql_error()) ;

// do the species totals
 echo ' now filling it up ' ;
while ($row_T = mysql_fetch_assoc($DOCAVG4)) {
 $tot = $row_T['INVTOT'] ;
 $doc = $row_T['INVORDDOC'] ;
 $lgsm = $row_T['INVLGSM'] ;
 switch ($lgsm) {
  case  1 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET CAN9 = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  2 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET FEL = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  3 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET EQ = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  4 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET BOV = '$tot' WHERE DOCTOR = '$doc' LIMIT 1 " ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  5 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET CAP = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  6 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET PORC = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  7 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET AV = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
  case  8 ;
   $DOCTAB16 = "UPDATE DOCTAB1 SET OTHER = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB16, $tryconnection) or die(mysql_error()) ;
   break;
   
   }
}
 
// The invoice totals

while ($row_S = mysql_fetch_assoc($DOCAVG4A)) {
 $inv = $row_S['INVTOT'] ;
 $doc = $row_S['INVORDDOC'] ;

 $DOCTAB17 = "UPDATE DOCTAB1 SET TOTAL = TOTAL + '$inv' WHERE DOCTOR = '$doc' LIMIT 1" ;
 $DOCAVG7 = mysql_query($DOCTAB17, $tryconnection) or die(mysql_error()) ;
 
}

while ($row_T = mysql_fetch_assoc($DOCAVG5)) {
 $inv = $row_T['INVNO'] ;
 $doc = $row_T['INVORDDOC'] ;
 $DOCTAB18 = "UPDATE DOCTAB1 SET INVOICES = '$inv' WHERE DOCTOR = '$doc' LIMIT 1" ;
 $DOCAVG8 = mysql_query($DOCTAB18, $tryconnection) or die(mysql_error()) ;
 
}

// and complete the table (averages)
$DOCTAB19 = "UPDATE DOCTAB1 SET AVERAGE = ROUND(TOTAL / INVOICES,2)" ;
$DOCAVG9 = mysql_query($DOCTAB19, $tryconnection) or die(mysql_error()) ;

//}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>MONTH END CLOSING</title>
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
	document.getElementById('inuse').innerHTML="<?php echo Date('m/d/Y'); ?>";
	}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

//function syspcls(){
//var closedate=document.forms[0].closedate.value;
//	if (document.forms[0].prtall.checked){
//	var prtall=1;
//	} else {var prtall=0;}
//	if (document.forms[0].yearend.checked){
//	var yearend=1;
//	} else {var yearend=0;}
//window.open('SYSPCLS.php?closedate='+closedate+'&prtall='+prtall+'&yearend='+yearend,'_self');
//}

function monthend_report(){

//window.open('REVENUE_ANALYSIS_DOCTOT.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');

window.open('ME_INVOICES.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('ME_CASH.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISK9.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISFE.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISEQ.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISBV.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISCO.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISPC.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISAV.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISOT.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISTR.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.open('REVENUE_ANALYSISRS.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');

window.open('DOC_SPLITS.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=820');
document.month_end.submit();
}

function monthend_close(){
window.open('SYSPCLS.php?closedate=<?php echo $_GET['closedate']; ?>','_blank', 'status=no, width=720');
window.self.close() ;
window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');
document.month_end.submit();
}

function do_both() {
monthend_report()
monthend_close()
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
<form method="post" action="" name="month_end">

<input type="hidden" name="closedate" value="<?php echo $_GET['closedate']; ?>"  />
<input type="hidden" name="prtall" value="<?php echo $_GET['prtall']; ?>"  />
<input type="hidden" name="yearend" value="<?php echo $_GET['yearend']; ?>"  />
<input type="hidden" name="check" value="1"  />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="78" align="center" valign="bottom" class="Verdana13B">CONFIRM MONTH END CLOSING PARAMETERS</td>
  </tr>
  <tr>
    <td height="442" align="center" valign="top">
    <br  />
	<table width="80%" border="1" cellspacing="0" cellpadding="0" class="table1">
      <tr>
        <td align="center">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="15"></td>
            </tr>
            <tr>
              <td height="25" align="center" class="Verdana12">Month End Closing date is: <strong class="Verdana13BBlue"><?php echo $_GET['closedate']; ?></strong><br /></td>
              </tr>
            <tr>
              <td height="25" class="Verdana12">&nbsp;</td>
              </tr>
            <tr>
              <td height="25" align="center" class="Verdana12"><label>&nbsp;Print ALL the Sales Invoices and Cash Receipts for the month: <span class="Verdana12BBlue"><?php if ($_GET['prtall']==1) {echo "YES"; } else {echo "NO";} ?></span></label></td>
              </tr>
            <tr>
              <td height="25" align="center" class="Verdana12"><label>Year End: <span class="Verdana12BBlue"><?php if ($_GET['yearend']==1) {echo "YES"; } else {echo "NO";} ?></span></label></td>
            </tr>
            <tr>
              <td height="27" class="Verdana12">&nbsp;</td>
              </tr>
            <tr>
              <td height="25" align="center" class="Verdana13BRed">Before Continuing:<br />
                <br />
                PLEASE MAKE SURE YOU ARE READY TO PRINT<br />
                <br />
                AND<br />
                <br />
                ALL THE OTHER USERS ARE OFF THE SYSTEM<br /></td>
              </tr>
            <tr>
              <td height="15"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="start" type="button" class="button" id="start" value="MONTH END REPORTS" style="width:200px;" onclick="monthend_report();" />
    <input name="close" type="button" class="button" id="close" value="MONTH END CLOSE" style="width:200px;" onclick="do_both();" />
    <input name="button2" type="button" class="button" id="button2" value="CANCEL" onclick="history.back();" style="width:110px;"/>    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
