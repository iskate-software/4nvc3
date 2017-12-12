// Amount invoiced through Invoicing
SELECT SUM(INVTOT) AS AITI FROM MSALES WHERE INVREVCAT < 90 ;

// Casual Sales
SELECT SUM(INVTOT) AS CS FROM MSALES WHERE INVREVCAT = 95 ;

// and sum these two for the subtotal (1)

//Summary Invoices
SELECT SUM(INVTOT) AS SI FROM MSALES WHERE INVREVCAT = 99 ;

// Service Charges
SELECT SUM(INVTOT) AS SC FROM MSALES WHERE INVREVCAT = 98 ;

// Discounts
SELECT SUM(INVTOT) AS D FROM MSALES WHERE INVREVCAT = 96 ;

// and sum these three for the subtotal (2)

// Then sum the two subtotals for Total Sales and Other Revenue.

//GST invoiced
SELECT SUM(INVTOT) AS GSTI FROM MSALES WHERE INVREVCAT = 90 ;

//PST invoiced
SELECT SUM(INVTOT) AS PSTI FROM MSALES WHERE INVREVCAT = 92 ;

// Then sum these two for the subtotal (3)

// and sum (1), (2) and (3) above subtotals for Total Sales Including Taxes

// Previous Period Cancels.
// Invoices

SELECT SUM(INVTOT) AS CI FROM MSALES WHERE INVREVCAT = 97 AND INVNO != 0 ;

// Service Charges

SELECT SUM(INVTOT) AS CSI FROM MSALES WHERE INVREVCAT = 97 AND INVNO = 0 ;

// GST

SELECT SUM(INVTOT) AS CGST FROM MSALES WHERE INVREVCAT = 90 AND INVTOT < 0 ;

// PST

SELECT SUM(INVTOT) AS CPST FROM MSALES WHERE INVREVCAT = 92 AND INVTOT < 0 ;

// and sum these three for previous period subtotal (4) 

// Then add (they are negative) (4) to the sum of (1), (2) and (3) above for Net Revenue.

// Pick out the GST rate.
 SELECT HGST AS MGSTRATE FROM CRITDATA ;

// and calculate the total GST taxable supplies

@GSTTS = GTSI / (MGSTRATE/100.00)

// The GST invoiced is GTSI

// The cancelled GST is CGST

// and sum these two for the subtotal (5)

//Net GST Payable is (5)

// PST Figures.

// Total Sales = (1) + (2) above

// Total PST taxable supplies is PSTI / (8/100.00)

// PST invoiced is PSTI

// Cancelled PST (adjustments) is CPST

// Net PST Payable is PSTI - CPST
