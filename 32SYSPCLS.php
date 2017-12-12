<?php
/*

  Note. This version applies negative invoices to any positive balances, and creates a credit if there are
  no more invoices to pay off.
  
  Some Large animal practices do not want this to happen.
  
 MONTH END CLOSING ROUTINE.
 
 This procedure requires a month end closing data '$closedate[0]' (defaulting to the nearest Month end from 
 today backwards (e.g. if it is the 3rd of the month, it defaults to the end of the previous month.
 
 To allow for laggard documentation, the key summary of the medical cases is checked for invoices in the 
 current files, those cases are then deleted. Further, any cases more than two days old are deleted.
 This keeps this file (MEDNOTES) in somewhat reasonable shape.
 
 The current cash, invoice, detailed line items and sales analysis files are rolled into the 
 previous month's files, after they in turn have been rolled into the history files.
 
 The accounts receivable file is rolled into the previous month's file, after it has been rolled 
 into the history files. Then the current receivables are scanned for negative balances (credits) 
 which are then applied against any positive balances for that client. If there are none left, but
 there is still a credit available, the client file has the credit added to it.
 
 Statistics for the curent month (new clients, new patients, invoice totals, cash receipt totals) 
 are calculated and PRACTICE is updated.
 
 Finally the month end revenue analysis is printed out, showing the breakdown of revenue by species 
 and by service and products.


In summary: 

1) Clean up any outstanding medical history files.
2) Summarize the invoices for the months
3) Summarize the cash receipts for the month.
4) Update the invoice file.
5) Update the cash receipts file
6) Update the detailed invoice file
7) Update the Accounts Receivable Files
8) Update Client Balances
9) Update the Inventory Value
10) Update the Hospital Files
11) Update the system files.
12) Only retain three months data in the TRACER table, which protects from Safari crashes


In order to allow for clinics with multiple locations, PREFER has to be checked to get the first
and last invoice numbers. Then, all the various invoice related totals have to be calculated 
(Cancels, Cancelled GST, Cancelled PST, Last Month Cancels, Last Month Cancelled PST and GST,
New net total business)
Then the temporary table to add it all up is created. (Now done in ME_INVOICES, and ME_CASH.)
*/
$_POST['check'] = 1 ;
if (isset($_POST['check'])){
require_once('../../tryconnection.php'); 
mysql_select_db($database_tryconnection, $tryconnection);

echo 'Step 1 ' ;
$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate1= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysql_error());
$closedate=mysql_fetch_array($closedate1);

// now do funny things with this, to cope with the fact that most transactions have hour,min,sec of 00:00:00,
// but those that do not, get cut out of the <= comparison. So add 23 hours and 55 mins to the base date.

$Round_about_midnight = "SELECT DATE_ADD('$closedate[0]', INTERVAL '23:55' HOUR_MINUTE) AS LATER" ;
$Bump_it = mysql_query($Round_about_midnight, $tryconnection) or die(mysql_error()) ;
$Get_Bump = mysql_fetch_assoc($Bump_it) ;
$closedate[0] = $Get_Bump['LATER'] ;

echo 'Step 2 ' ;

$Clean_up = "DELETE FROM MEDNOTES WHERE EXISTS (SELECT CUSTNO FROM ARINVOI WHERE CUSTNO = MEDNOTES.NCUSTNO) " ;
$scour = mysql_query($Clean_up, $tryconnection) or die(mysql_error()) ;

$Clean_up2 = "DELETE FROM MEDNOTES WHERE NDATE <= DATE_SUB('$closedate[0]', INTERVAL 3 DAY) " ;
$scour2 = mysql_query($Clean_up2, $tryconnection) or die(mysql_error()) ;

$LIMIT1 = "SELECT FIRSTINV FROM PREFER LIMIT 1" ;
$DOIT1 = mysql_query($LIMIT1, $tryconnection ) or die(mysql_error()) ;
$FIRSTINV = mysql_fetch_array($DOIT1);

echo 'Step 3 ' ;
$LIMIT2 = "SELECT LASTINV FROM PREFER LIMIT 1" ;
$DOIT2 = mysql_query($LIMIT2, $tryconnection ) or die(mysql_error()) ;
$LASTINV = mysql_fetch_array($DOIT2);

echo 'Step 4 ' ;
$SETUP1 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP" ;
$SETUP2 = "CREATE TEMPORARY TABLE ARTEMP LIKE ARINVOI" ;
$SETUP3 = "INSERT INTO ARTEMP SELECT * FROM ARINVOI WHERE INVDTE <= '$closedate[0]' " ;
$DOIT3 = mysql_query($SETUP1, $tryconnection ) or die(mysql_error()) ;
$DOIT4 = mysql_query($SETUP2, $tryconnection ) or die(mysql_error()) ;
$DOIT5 = mysql_query($SETUP3, $tryconnection ) or die(mysql_error()) ;

echo 'Step 5 ';
$M_invo3 = "SELECT COUNT(INVNO) AS MINV FROM ARTEMP" ; 
$Doit3 = mysql_query($M_invo3, $tryconnection) or die(mysql_error()) ;
$MINV = mysql_fetch_assoc($Doit3);
echo 'Step 6 ' ;
$M_invo4 = "SELECT SUM(ITOTAL) AS MTINVSL FROM ARTEMP" ;
$Doit4 = mysql_query($M_invo4, $tryconnection) or die(mysql_error()) ;
$MTINVSL = mysql_fetch_assoc($Doit4);
echo 'Step 7 ' ;

$M_invo5 = "SELECT SUM(TAX) AS MTGST FROM ARTEMP" ;
$Doit5 = mysql_query($M_invo5, $tryconnection) or die(mysql_error()) ;
$MTGST = mysql_fetch_assoc($Doit5);
echo 'Step 8 ' ;

// Now the cash:
//
// Only select CASHDEP. Anything which has not gone through a bank deposit remains behind.
//
//
$SETUP4 = "DROP TEMPORARY TABLE IF EXISTS ARTEMP" ;
$DOIT6 = mysql_query($SETUP4, $tryconnection) or die(mysql_error()) ;
echo 'Step 9 ' ;

$SETUP5 = "CREATE TEMPORARY TABLE ARTEMP LIKE CASHDEP" ;
$DOIT7 = mysql_query($SETUP5, $tryconnection) or die(mysql_error()) ;
echo 'Step 10 ' ;

$SETUP6 = "INSERT INTO ARTEMP SELECT * FROM CASHDEP WHERE DTEPAID <= '$closedate[0]' ";
$DOIT8 = mysql_query($SETUP6, $tryconnection) or die(mysql_error()) ;
echo 'Step 11 ' ;

$M_cash2 = "SELECT SUM(AMTPAID) AS MTCASHSL FROM ARTEMP  WHERE REFNO <> 'Corrn.' ";
$Doit8 = mysql_query($M_cash2, $tryconnection) or die(mysql_error()) ;
$MTCASHSL = mysql_fetch_assoc($Doit8);
echo 'Step 12 ' ;

//Now do the real month end closing..
//Start with the cash.

$CASH_1 = "INSERT INTO ARYCASH (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DISCOUNT,AMTPAID,DTEPAID,DATETIME) 
                         SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DISCOUNT,AMTPAID,DTEPAID,DATETIME FROM LASTCASH " ;
$CASH_1q = mysql_query($CASH_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 13 ' ;

$CASH_2 = "TRUNCATE LASTCASH ";
$CASH_2q = mysql_query($CASH_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 14 ' ;

$CASH_3 = "INSERT INTO LASTCASH (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,DISCOUNT,AMTPAID,DTEPAID,REFNO,DATETIME) 
                          SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,DISCOUNT,AMTPAID,DTEPAID,REFNO,DATETIME FROM CASHDEP WHERE DTEPAID <= '$closedate[0]'" ;
$CASH_3q = mysql_query($CASH_3, $tryconnection) or die(mysql_error()) ;
echo 'Step 15 ' ;

$CASH_4 = "DELETE FROM CASHDEP WHERE DTEPAID <= '$closedate[0]' " ;
$CASH_4q = mysql_query($CASH_4, $tryconnection) or die(mysql_error()) ;
echo 'Step 16 ' ;

//now the invoices 

$INVOICE_1 = "INSERT INTO ARYINVO (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,PRTID,REFVET,REFCLIN,NPFEE,DATETIME,INVORDDOC,PDEAD,INVPET,UNIQUE1) 
                            SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,PRTID,REFVET,REFCLIN,NPFEE,DATETIME,INVORDDOC,PDEAD,INVPET,UNIQUE1 FROM INVLAST ";
$INVOICE_1q = mysql_query($INVOICE_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 17 ' ;
$INVOICE_2 = "TRUNCATE INVLAST" ;
$INVOICE_2q = mysql_query($INVOICE_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 18 ' ;
$INVOICE_3 = "INSERT INTO INVLAST (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,PRTID,REFVET,REFCLIN,NPFEE,DATETIME,INVORDDOC,PDEAD,INVPET,UNIQUE1) 
                            SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,PRTID,REFVET,REFCLIN,NPFEE,DATETIME,INVORDDOC,PDEAD,INVPET,UNIQUE1 FROM ARINVOI WHERE INVDTE <= '$closedate[0]'";
$INVOICE_3q = mysql_query($INVOICE_3, $tryconnection) or die(mysql_error()) ;
echo 'Step 19 ' ;
$INVOICE_4 = "DELETE FROM ARINVOI WHERE INVDTE <= '$closedate[0]'";
$INVOICE_4q = mysql_query($INVOICE_4, $tryconnection) or die(mysql_error()) ;
echo 'Step 20 ' ;

$DVMI_1 = "INSERT INTO ARYDVMI (INVNO,INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVFLAGS,INVVPC,DATETIME, INARCLOG,IRADLOG,ISURGLOG,IUAC,SATELLITE,INVDECLINE,PETNAME,INVOICECOMMENT,INVPRU,INVGAB,INVLGSM,INVSEQ,UNIQUE1) 
                         SELECT INVNO,INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVFLAGS,INVVPC,DATETIME, INARCLOG,IRADLOG,ISURGLOG,IUAC,SATELLITE,INVDECLINE,PETNAME,INVOICECOMMENT,INVPRU,INVGAB,INVLGSM,INVSEQ,UNIQUE1 FROM DVMINV WHERE INVDATETIME <= '$closedate[0]'" ;
$DVMI_1q = mysql_query($DVMI_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 21 ' ;

$DVMI_2 = "TRUNCATE TABLE DVMILAST " ;
$DVMI_2q = mysql_query($DVMI_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 22 ' ;
$DVMI_3 = "INSERT INTO DVMILAST (INVNO,INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVFLAGS,INVVPC,DATETIME, INARCLOG,IRADLOG,ISURGLOG,IUAC,SATELLITE,INVDECLINE,PETNAME,INVOICECOMMENT,INVPRU,INVGAB,INVLGSM,INVSEQ,UNIQUE1) 
                          SELECT INVNO,INVCUST,INVPET,INVDATETIME,INVMAJ,INVMIN,INVORDDOC,INVDOC,INVSTAFF,INVUNITS,INVDESCR,INVPRICE,INVTOT,INVREVCAT,INVTAX,INVFLAGS,INVVPC,DATETIME, INARCLOG,IRADLOG,ISURGLOG,IUAC,SATELLITE,INVDECLINE,PETNAME,INVOICECOMMENT,INVPRU,INVGAB,INVLGSM,INVSEQ,UNIQUE1 FROM DVMINV WHERE INVDATETIME <= '$closedate[0]'";
$DVMI_3q = mysql_query($DVMI_3, $tryconnection) or die(mysql_error()) ;
echo 'Step 23 ' ;
$DVMI_4 = "DELETE FROM DVMINV WHERE INVDATETIME <= '$closedate[0]' " ;
$DVMI_4q = mysql_query($DVMI_4, $tryconnection) or die(mysql_error()) ;
echo 'Step 24 ' ;

$YGST_1 = "INSERT INTO ARYGST SELECT * FROM ARGST WHERE INVDTE <= '$closedate[0]' " ;
$YGST_1q = mysql_query($YGST_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 25 ' ;
$YGST_2 = "DELETE FROM ARGST WHERE INVDTE <= '$closedate[0]' " ;
$YGST_2q = mysql_query($YGST_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 26 ' ;

$SALE_1 = "INSERT INTO ARYSALE (INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID, INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO,INVCUST,INVTNO,DATETIME,INVDECLINE,UNIQUE1) 
                         SELECT INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID, INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO,INVCUST,INVTNO,DATETIME,INVDECLINE,UNIQUE1 FROM OLDSALE " ;
$SALE_1q = mysql_query($SALE_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 27 ' ;
$SALE_2 = "TRUNCATE TABLE OLDSALE " ;
$SALE_2q = mysql_query($SALE_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 28 ' ;
$SALE_3 = "INSERT INTO OLDSALE (INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID, INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO,INVCUST,INVTNO,DATETIME,INVDECLINE,UNIQUE1) 
                         SELECT INVMAJ,INVTOT,INVGST,INVTAX,INVDISC,INVDOC,INVORDDOC,INVDESC,INVPAID, INVAR,INVLGSM,INVREVCAT,INVDTE,INVNO,INVCUST,INVTNO,DATETIME,INVDECLINE,UNIQUE1 FROM SALESCAT WHERE INVDTE <= '$closedate[0]' " ;
$SALE_3q = mysql_query($SALE_3, $tryconnection) or die(mysql_error()) ;
echo 'Step 29 ' ;
$SALE_4 = "DELETE FROM SALESCAT WHERE INVDTE <= '$closedate[0]' " ;
$SALE_4q = mysql_query($SALE_4, $tryconnection) or die(mysql_error()) ;
echo 'Step 30 ' ;

$RECV_1 = "INSERT INTO ARYRECHS (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME,UNIQUE1) 
                          SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME,UNIQUE1 FROM ARRECHS " ;
$RECV_1q = mysql_query($RECV_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 31 ' ;
$RECV_2 = "TRUNCATE TABLE ARRECHS " ;
$RECV_2q = mysql_query($RECV_2, $tryconnection) or die(mysql_error()) ;
echo 'Step 32 ' ;
$RECV_3 = "INSERT INTO ARRECHS (INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME,UNIQUE1)
                         SELECT INVNO,INVDTE,INVTIME,CUSTNO,COMPANY,SALESMN,PONUM,REFNO,DTEPAID,TAX,PTAX,ITOTAL,DISCOUNT,AMTPAID,IBAL,DATETIME,UNIQUE1 FROM ARARECV WHERE INVDTE <='$closedate[0]' " ;
$RECV_3q = mysql_query($RECV_3, $tryconnection) or die(mysql_error()) ;
echo 'Step 33 ' ;
//
////here we need the magic for negative invoices.
//
$RECV_4 = "DELETE FROM ARARECV WHERE IBAL = 0 AND INVDTE <= '$closedate[0]' AND DTEPAID <= '$closedate[0]'" ;
$RECV_4q = mysql_query($RECV_4, $tryconnection) or die(mysql_error()) ;
echo 'Step 35 ' ;
//
////Client file
// not done any more, as statements do this so the field actually reflects what
// the client saw on the statement, and receptionist can use it on payments...
//$CUST_1 = "UPDATE ARCUSTO SET LASTMON = BALANCE "; 
//$CUST_1q = mysql_query($CUST_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 36 ' ;
$CUST_2 = "SELECT COUNT(CUSTNO) AS MCUST FROM ARCUSTO " ;
$CUST_2q = mysql_query($CUST_2, $tryconnection) or die(mysql_error()) ; 
$MCUST = mysql_fetch_assoc($CUST_2q);
echo 'Step 37 ' ;
//
$CUST_3 = "SELECT COUNT(CUSTNO) AS MCUST2 FROM ARCUSTO WHERE  DATE_SUB('$closedate[0]', INTERVAL 365 DAY) < LDATE" ;
$CUST_3q = mysql_query($CUST_3, $tryconnection) or die(mysql_error()) ;
$MCUST2 = mysql_fetch_assoc($CUST_3q);
echo 'Step 38 ' ;

$CUST_4 = "SELECT COUNT(CUSTNO) AS MCUST3 FROM ARCUSTO  WHERE DATE_SUB('$closedate[0]', INTERVAL 730 DAY) < LDATE " ;
$CUST_4q = mysql_query($CUST_4, $tryconnection) or die(mysql_error()) ;
$MCUST3 = mysql_fetch_assoc($CUST_4q);
echo 'Step 39 ' ;

////Magic required to recalculate balances from ararecv 

// The final part of this is required in some clinics, but not all. This version  applies the credits. 
// Simply figure out what the total credit invoices are, then create a 
// credit in the client file for this. Some large animal clinics want the staff to choose where to apply the credit.
// The credit invoices are then eliminated from the receivables, as they have
// fulfilled their purpose.

$select_NEGIBAL="SELECT DISTINCT CUSTNO FROM ARARECV WHERE IBAL < 0 AND INVDTE <= '$closedate[0]'";
$NEGIBAL=mysql_query($select_NEGIBAL, $tryconnection) or die(mysql_error());
// $row_NEGIBAL=mysql_fetch_assoc($NEGIBAL);
echo 'Step 40 ' ;
$invcust=array();
while ($row_NEGIBAL=mysql_fetch_assoc($NEGIBAL)) {
$invcust[]=$row_NEGIBAL['CUSTNO'];
} //while ($row_NEGIBAL=mysql_fetch_assoc($NEGIBAL));

echo 'Step 41 ' ;
foreach ($invcust as $invcust2){

 $select_SUMNEGIBAL="SELECT SUM(IBAL) AS IBAL FROM ARARECV WHERE IBAL<0 AND CUSTNO='$invcust2' AND INVDTE < '$closedate[0]' ";
 $SUMNEGIBAL=mysql_query($select_SUMNEGIBAL, $tryconnection) or die(mysql_error());
 $row_SUMNEGIBAL=mysql_fetch_assoc($SUMNEGIBAL);

 echo 'Step 42 ' ;

 $sumnegibal=(0-$row_SUMNEGIBAL['IBAL']);
  echo ' Using ' .$sumnegibal . ' Client ' . $invcust2 . ' ' ;
 $get_rid = "UPDATE ARARECV SET IBAL = 0, REFNO = 'APPLD' WHERE CUSTNO = '$invcust2' AND IBAL < 0  AND INVDTE < '$closedate[0]' " ;
 $query_finish = mysql_query($get_rid, $tryconnection) or die(mysql_error()) ;
 echo 'Step 43 ' ;

 $select_IBALINVNO="SELECT IBAL, CUSTNO, INVNO,INVDTE,UNIQUE1 FROM ARARECV WHERE IBAL>0 AND CUSTNO='$invcust2' ORDER BY INVDTE, UNIQUE1 ASC";
 $IBALINVNO=mysql_query($select_IBALINVNO, $tryconnection) or die(mysql_error());


 while ($row_POSIBAL=mysql_fetch_assoc($IBALINVNO)) {
 
	  if ($sumnegibal > 0 ) {
		if ($row_POSIBAL['IBAL'] >= $sumnegibal){
	        echo ' Step 43.5 ' . $row_POSIBAL['UNIQUE1'] ;
			$update_ARARECV="UPDATE ARARECV SET IBAL=(IBAL-$sumnegibal), AMTPAID = AMTPAID + $sumnegibal, REFNO = 'DEP.APP.', DTEPAID = '$closedate[0]' WHERE UNIQUE1 = '$row_POSIBAL[UNIQUE1]'  LIMIT 1";
			$ARARECV=mysql_query($update_ARARECV, $tryconnection) or die(mysql_error());
			$sumnegibal=0;
		}
		else{
	        echo ' Step 43.6 '.$row_POSIBAL['UNIQUE1'] ;
			$sumnegibal=$sumnegibal-$row_POSIBAL['IBAL'];
			$update_ARARECV="UPDATE ARARECV SET IBAL=0, AMTPAID = ITOTAL, REFNO = 'DEP.APP.', DTEPAID = '$closedate[0]'  WHERE UNIQUE1 = '$row_POSIBAL[UNIQUE1]'  LIMIT 1";
			$ARARECV=mysql_query($update_ARARECV, $tryconnection) or die(mysql_error());
		}
	 } // if ($sumnegibal > 0 )
	}  // while ($row_POSIBAL=mysql_fetch_assoc($IBALINVNO))
	 if ($sumnegibal > 0) {
	    $make_credit = "UPDATE ARCUSTO SET CREDIT = CREDIT + '$sumnegibal' where custno = '$invcust2'  LIMIT 1" ;
        $query_credit = mysql_query($make_credit, $tryconnection) or die(mysql_error()) ;
        echo 'Step 44 ' ;
     }
    
}  //foreach ($invcust as $invcust2)

unset($invcust) ;

// Now get rid of all the zero balance receivables..

$empty_out = "DELETE FROM ARARECV WHERE IBAL = 0 " ;
$Query_empty = mysql_query($empty_out, $tryconnection) or die(mysql_error()) ;

// Patient File

$PETM_1 = "SELECT COUNT(petid) AS MPATNT FROM PETMAST WHERE PFIRSTDATE <= '$closedate[0]'" ;
$PETM_1q = mysql_query($PETM_1, $tryconnection) or die(mysql_error()) ;
$MPATNT = mysql_fetch_assoc($PETM_1q);
echo 'Step 45 ' ;

$PETM_2 = "SELECT COUNT(petid) AS ACTIVE_PATS FROM PETMAST WHERE (PDEAD + PMOVED = 0) AND PFIRSTDATE <= '$closedate[0]' ";
$PETM_2q = mysql_query($PETM_2, $tryconnection) or die(mysql_error()) ;
$ACTIVE_PATS = mysql_fetch_assoc($PETM_2q);
echo 'Step 46 ' ;

$PETM_3 = "SELECT COUNT(petid) AS ACT_PATS2 FROM PETMAST WHERE (DATE_SUB('$closedate[0]', INTERVAL 365 DAY) < PLASTDATE) AND (PDEAD + PMOVED = 0) " ;
$PETM_3q = mysql_query($PETM_3, $tryconnection) or die(mysql_error()) ;
$ACT_PATS2 = mysql_fetch_assoc($PETM_3q);
echo 'Step 47 ' ;

$PETM_4 = "SELECT COUNT(petid) AS ACT_PATS3 FROM PETMAST WHERE (DATE_SUB('$closedate[0]', INTERVAL 730 DAY) < PLASTDATE) AND (PDEAD + PMOVED = 0) ";
$PETM_4q = mysql_query($PETM_4, $tryconnection) or die(mysql_error()) ;
$ACT_PATS3 = mysql_fetch_assoc($PETM_4q);
echo 'Step 48 ' ;


// The PRACTICE file, and CRITDATA.

$PRACT_1 = "INSERT INTO PRACTICE (LASTCLOSE, CLIENTS, CLIENTS2, CLIENTS3, PATIENTS, ACTIVEPAT, ACTPAT2, ACTPAT3, INVOICE, INVSALES, CASHREC, GST) VALUES ('$closedate[0]', '$MCUST[MCUST]', '$MCUST2[MCUST2]', '$MCUST3[MCUST3]', '$MPATNT[MPATNT]', '$ACTIVE_PATS[ACTIVE_PATS]', '$ACT_PATS2[ACT_PATS2]', '$ACT_PATS3[ACT_PATS3]', '$MINV[MINV]', '$MTINVSL[MTINVSL]', '$MTCASHSL[MTCASHSL]', '$MTGST[MTGST]')" ;
$PRACT_1q = mysql_query($PRACT_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 49 ' ;

$CRITD_1 = "UPDATE CRITDATA SET MEDATE = '$closedate[0]', MTHDATE = 'NOW()'  WHERE CRITDATAID = 1 ";
$CRITD_1Q = mysql_query($CRITD_1, $tryconnection) or die(mysql_error()) ;
echo 'Step 50 ' ;

$TRFLUSH = "DELETE FROM TRACER WHERE DATETIME < DATE_SUB(NOW(), INTERVAL 3 MONTH)" ;
$doflush = mysql_query($TRFLUSH, $tryconnection) or die(mysql_error()) ;
$opFlush = "OPTIMIZE TABLE TRACER" ;
$doOpt = mysql_query($opFlush, $tryconnection) or die(mysql_error()) ;

}

?>