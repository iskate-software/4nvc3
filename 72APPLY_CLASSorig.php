<?php 

echo ' started ' ;
session_start();
require_once('../../tryconnection.php');
mysqli_select_db($tryconnection, $database_tryconnection);
$query_CLASSES = "SELECT CLASSID, CLASS, REGITM3, VAR3, ROUNDER1, MINPRICE1, REGITM6, VAR6, ROUNDER4, MINPRICE4 FROM FORMULA1 ORDER BY CLASS";
$CLASSES = mysqli_query($tryconnection, $query_CLASSES) or die(mysqli_error($mysqli_link));
$row_CLASSES = mysqli_fetch_assoc($CLASSES);

echo ' While I use up space which should not be needed Looking for check ';
  $_SESSION["class"] = "x" ;
if (isset($_GET['check'])) {
 echo 'Check is set' ;

// This function takes the rounding unit from the markup formula and returns the result
// The rounding unit comes in as cents, so has to be converted to an integer first.
function invtround($x,$y)
{
 $factor = round(100/($x*100),0) ;
 $result = round(($y*$factor)/$factor,2) ;
 return $result ;
} 
  $class = $_GET['class'] ;
  echo ' class is ' . $class . ' ' ;
  $_SESSION["class"] = $class ;
  $CLASSX = "SELECT REGITM3,REGITM6,ROUNDER1,MINPRICE1,ROUNDER4,MINPRICE4 FROM FORMULA1 WHERE CLASS = '$class' LIMIT 1" ;

  $get_CLASS = mysqli_query($tryconnection, $CLASSX) or die(mysqli_error($mysqli_link)) ;
  $row_CLASS = mysqli_fetch_assoc($get_CLASS) ;
 
  $count = 0 ;
   $meddle = 0 ;
  $AFFECTED = "SELECT ITEM, ITEMID, COST,PRICE,UPRICE,PKGQTY, MARKUP, MANUAL, CLASS FROM ARINVT WHERE CLASS = '$class' " ;

  $get_AFFECTED = mysqli_query($tryconnection, $AFFECTED) or die(mysqli_error($mysqli_link)) ;
  
  
  while ($row_AFFECTED = mysqli_fetch_assoc($get_AFFECTED)) {
   if ($row_AFFECTED['MANUAL'] != 1 && $row_AFFECTED['MARKUP'] == 1 ) {
   
      $price = $row_AFFECTED['PRICE'] ;
      $uprice = $row_AFFECTED['UPRICE'] ;
      $cost = $row_AFFECTED['COST'] ;
      $pkgqty = $row_AFFECTED['PKGQTY'] ;
      $itemid = $row_AFFECTED['ITEMID'] ;
     
      $price  = ROUND($cost * $row_CLASS['REGITM3'] / $pkgqty,2) ;
      if ($pkgqty == 1) {
        $uprice = $price ;
      }
      else {
       $uprice = ROUND($cost * $row_CLASS['REGITM6'] / $pkgqty,2) ;
      }
      
      if ($price < $row_CLASS['MINPRICE1'])  {
        $price = $row_CLASS['MINPRICE1'] ;
      }
      if ($uprice < $row_CLASS['MINPRICE4'])  {
        $uprice = $row_CLASS['MINPRICE4'] ;
      }
      
    
  // and apply the round to a particular unit formula (nickels, dimes, quarters, half looneys, looneys).
      $price = invtround($row_CLASS['ROUNDER1'],$price) ;
      $uprice = invtround($row_CLASS['ROUNDER4'],$uprice) ;
      
     $UPDFEE = "UPDATE ARINVT SET PRICE = '$price', UPRICE = '$uprice' WHERE ITEMID = '$itemid' LIMIT 1 " ;

      $do_it = mysqli_query($tryconnection, $UPDFEE) or die(mysqli_error($mysqli_link)) ;
    
     $count++ ;
     
   }  
  } 
//  $message = "'There were   $count changes made'";
//  echo $message ;
  echo "<script type='text/javascript'>" ;
  echo "var w = window.open(''.'nameofwindow')" ;
//  echo "alert('$message');" ;
  echo "window.close() ;" ;
  echo "</script>";
  
  header("Location: CONSTANTS_DIRECTORY.php");
 
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>APPLY PRICE CLASSES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+40);
}

function bodyonunload() {
  self.close() ; 
  localStorage.setItem('class','getElementById("class")' ;)
}

//function putInClass(x){
//opener.document.getElementById('class').value=x;
//self.close();
//}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="GET" action="" name="classes" id="classes" style="position:absolute; top:0px; left:0px;">
    <table height="553" width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr height="10" class="Verdana11Bwhite">
        <td width="" bgcolor="#000000">&nbsp;</td>
        <td width="" align="center" bgcolor="#000000">Class</td>
        <td width="70" align="center" bgcolor="#000000">Formula</td>
        <td width="" align="center" bgcolor="#000000">Rounding</td>
        <td align="center" bgcolor="#000000">Min. Price</td>
      </tr>
	  <tr>
      	<td colspan="5">
<div style="height:490px; overflow:auto;">    
<table  width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">      <tr>
        <td width="35"></td>
        <td width="20" height='0'></td>
        <td width="200" height='0'></td>
        <td width="100" height='0'></td>
        <td height='0'></td>
      </tr>
  <?php do { ?>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>"  onmouseover="document.getElementById(this.id).style.cursor='pointer'" bgcolor="#EEEEEE" title="<?php echo $row_CLASSES['CLASS']; ?>: Package Price">
      <td height="18"></td>
    <td align="left" class="Verdana12B" ><input type="radio" name="class" value ="<?php echo $row_CLASSES['CLASS']; ?>"/><?php echo $row_CLASSES['CLASS']; ?> </td>
    <td align="left">Cost / Pack Qty * <?php echo $row_CLASSES['REGITM3']; ?></td>
    <td align="center">
				<?php 
				switch ($row_CLASSES['ROUNDER1']) {
					case 0.01:
						echo "Cent";
						break;
					case 0.05:
						echo "Nickel";
						break;
					case 0.10:
						echo "Dime";
						break;
					case 0.25:
						echo "Quarter";
						break;
					case 0.50:
						echo "Half Dollar";
						break;					
					case 1.00:
						echo "Dollar";
						break;
				} 
				
			?>
	</td>
    <td align="center"><?php echo $row_CLASSES['MINPRICE1']; ?></td>
  </tr>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>x"   onmouseover="document.getElementById(this.id).style.cursor='pointer'" title="<?php echo $row_CLASSES['CLASS']; ?>: Unit Price">
      <td height="18"></td>
    <td align="center"><?php echo $row_CLASSES['']; ?></td>
    <td align="center">Cost / Pack Qty * <?php echo $row_CLASSES['REGITM6']; ?></td>
    <td align="center">
			<?php 
				switch ($row_CLASSES['ROUNDER4']) {
					case 0.01:
						echo "Cent";
						break;
					case 0.05:
						echo "Nickel";
						break;
					case 0.10:
						echo "Dime";
						break;
					case 0.25:
						echo "Quarter";
						break;
					case 0.50:
						echo "Half Dollar";
						break;					
					case 1.00:
						echo "Dollar";
						break;
				} 
				
			?>
    </td>
    <td align="center"><?php echo $row_CLASSES['MINPRICE4']; ?></td>
  </tr>
    <?php } while ($row_CLASSES = mysqli_fetch_assoc($CLASSES)); ?>
</table>
</div>
		</td>
      </tr>      
      <tr>
        <td colspan="5" align="center" class="ButtonsTable">
          <input name="apply" type="submit" class="button" id="apply" value="APPLY"  />
          <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" /></td>
      </tr>
      <input type="hidden" name="check" value="1"  />
     
    </table>
    </form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
