<?php

require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);

$cutoff = $GET_scdate ;
$invdte = $GET_invdate ;
$pctg = $GET_pcntg ;
$minchg = $GET_minchg ;
$minbal = $GET_minbal ;

/*
if (!empty($_GET['scdate'])){
$scdate=$_GET['scdate'];
}
else {
 $DAY = "SELECT DAY(NOW()) AS DAY" ;
 $GET_day = mysql_query($DAY, $tryconnection) or die(mysql_error());
 $row_day = mysql_fetch_assoc($GET_day) ;
 $GETIT_scdte= "SELECT DATE_FORMAT(NOW(),'%m/%d/%Y') AS SCDATE" ;
 $FETCH1_it = mysql_query($GETIT_scdte, $tryconnection) or die(mysql_error()) ;
 $row_FETCH1 = mysql_fetch_assoc($FETCH1_it) ;
 $scdte = $row_FETCH1['INVDATE'] ;
}
*/
$scdate1="SELECT STR_TO_DATE('$cutoff','%m/%d/%Y')";
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

$SC3 = "INSERT INTO SC1 (CUSTNO,COMPANY,INVNO,INVDTE,IBAL) SELECT CUSTNO, COMPANY,INVNO,INVDTE,IBAL FROM ARARECV WHERE INVDTE < '$scdate3' ";
$SC3A = mysql_query($SC3, $tryconnection) or die(mysql_error()) ; 

$SC4 = "DELETE FROM SC1 WHERE IBAL = 0"  ;
$SC4A = mysql_query($SC4, $tryconnection) or die(mysql_error()) ;

$SC5 = "CREATE TEMPORARY TABLE SC2 LIKE SC1" ;
$SC5A = mysql_query($SC5, $tryconnection) or die(mysql_error()) ;

$SC6 = "INSERT INTO SC2 (CUSTNO,COMPANY,IBAL) SELECT CUSTNO,COMPANY,SUM(IBAL) FROM SC1 GROUP BY CUSTNO" ;
$SC6A = mysql_query($SC6, $tryconnection) or die(mysql_error()) ;

$SC7 = "UPDATE SC2 JOIN ARCUSTO ON (SC2.CUSTNO = ARCUSTO.CUSTNO) SET IBAL = 0 WHERE ARCUSTO.SVC = 0" ;
$SC7A = mysql_query($SC7, $tryconnection) or die(mysql_error()) ;

$SC8 = "DELETE FROM SC2 WHERE IBAL <=0 ";
$SC8A = mysql_query($SC8, $tryconnection) or die(mysql_error()) ;

$SC9 = "UPDATE SC2 SET IBAL = IBAL * '$pctg' ";
$SC9A = mysql_query($SC9, $tryconnection) or die(mysql_error()) ;

$SC10 = "UPDATE SC2 SET IBAL = '$minchg' WHERE IBAL < '$minchg' ";
$SC10A = mysql_query($SC10, $tryconnection) or die(mysql_error()) ;

$SC11 = "UPDATE SC2 SET INVNO = '0000',INVDTE = '$invdte3',PONUM = 'SERVICE CHARGE', TAX = 0, PTAX = 0,  AMTPAID = 0,  DISCOUNT = 0,  REFNO = ' ', SALESMN = ' ',  ITOTAL = IBAL" ;
$SC11A = mysql_query($SC11, $tryconnection) or die(mysql_error()) ;

$SC12 = "INSERT INTO ARARECV (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN, PONUM,REFNO, DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME) SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN, PONUM,REFNO, DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME FROM SC2 ";
$SC12A = mysql_query($SC12, $tryconnection) or die(mysql_error()) ;

$SC13 = "INSERT INTO ARINVOI (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY, SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,DATETIME) SELECT INVNO,INVDTE,INVTIME,CUSTNO, COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,DATETIME FROM SC2" ;
$SC13A = mysql_query($SC13, $tryconnection) or die(mysql_error()) ;

$SC14 = "UPDATE ARINVOI JOIN ARARECV ON (ARINVOI.INVNO = ARARECV.INVNO AND ARINVOI.CUSTNO = ARARECV.CUSTNO AND ARINVOI.INVDTE = ARARECV.INVDTE) SET ARINVOI.UNIQUE1 = ARARECV.UNIQUE1 ";
$SC14A = mysql_query($SC14, $tryconnection) or die(mysql_error()) ;

$SC15 = "SELECT SUM(IBAL) FROM SC2" ;
$SC15A = mysql_query($SC15, $tryconnection) or die(mysql_error()) ;

$row_SC15 = mysql_fetch_assoc($SC15A) ;
$sctot = $row_SC15['IBAL'] ;

$SC16 = "INSERT INTO SALESCAT SET INVNO = '0000', INVDTE = '$invdate',INVMAJ = 98, INVREVCAT = 98, INVDECLINE = 0, INVTOT = '$sctot' ";
$SC16A = mysql_query($SC16, $tryconnection) or die(mysql_error()) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>COMPUTE SERVICE CHARGES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
</script>





<style type="text/css">
<!--
#shadow {	background-color: #556453;
	width: 441px;
	height: auto;
}
#shadowedtable {	position: relative;
	width: 440px;
	height: 227;
	left: -4px;
	top: -4px;
	background-color:#FFFFFF;
	border: solid #556453 thin;
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
<form name="comp_service" method="get" action="SERVICE_REGISTER_RESULTS.php" >

<table width="732" height="553" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <!--DWLayoutTable-->
  <tr>
    <td width="128" height="108"></td>
    <th width="495" height="20"><p>COMPUTE SERVICE CHARGES</p></th>
    <td width="109">&nbsp;</td>
  </tr>
  <tr>
    <td height="337">&nbsp;</td>
    <td align="center" valign="top"><div id="shadow">
      <div id="shadowedtable">
        <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="cols">
          <tr>
            <td width="48%" align="center" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="35" align="center" valign="bottom" class="Verdana12"><strong><u>PARAMETERS</u></strong></td>
              </tr>
              <tr>
                <td height="38" align="center" class="Labels2">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" class="Verdana12">
                &nbsp;&nbsp;&nbsp;
                <label>&nbsp;
                  <input name="svpct" type="text" id="pcntg" value="$svpct" size="4" />
                  Service Charge Percent</label></td>
              </tr>
              <tr>
                <td height="30" align="left" class="Verdana12">
                &nbsp;&nbsp;&nbsp;
                <label>&nbsp;
                  <input name="minsc" type="text" id="minchg"  value="$minsc" size="4"/>
                  Minimum Service Chg.</label></td>
              </tr>
              <tr>
                <td height="30" align="left" class="Verdana12">
                &nbsp;&nbsp;&nbsp;
                <label>&nbsp;
                  <input name="minbal" type="text" id="minbal"  value="$minbal" size="4"/>
                  Minimum Balance</label></td>
              </tr>
              <tr>
                <td height="30" align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="52%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="35" colspan="2" align="center" valign="bottom" class="Verdana12"><strong><u>DATES</u></strong></td>
              </tr>
              <tr>
                <td colspan="2"></td>
                </tr>
              <tr>
                <td width="50%" height="35">&nbsp;</td>
                <td width="50%" height="35" align="center" class="Labels2">MM/DD/YYYY</td>
              </tr>
              <tr>
                <td height="36" align="right" class="Verdana12">Cut-off date for charges</td>
                <td height="36" align="center" class="Verdana12"><input name="cutdate" id="cutdate" type="text" class="Input" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this);" value="<?php echo date('m/d/Y'); ?>"/></td>
              </tr>
              <tr>
                <td height="25" align="right" class="Verdana12">Date on S/C invoices</td>
                <td height="25" align="center" class="Verdana12"><input name="invdate" id="invdate" type="text" class="Input" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this);" value="<?php echo date('m/d/Y'); ?>"/></td>
              </tr>
              <tr>
                <td colspan="2" class="Verdana12">&nbsp;</td>
                </tr>
             
              <tr>
                <td height="25" colspan="2" align="left" class="Verdana12">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label class="hidden">&nbsp;
                  <input type="checkbox" name="checkbox2" id="checkbox2" />
                  Totals only</label></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2" align="center" class="ButtonsTable">
            <input class="button" type="submit" name="Submit" value="CALCULATE" />
            <input class="hidden" type="button" name="Submit2" value="PRINT" />
            <input class="button" type="reset" name="Submit3" value="CANCEL" onclick="history.back();" /></td>
          </tr>
        </table>
      </div>
    </div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="108">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
