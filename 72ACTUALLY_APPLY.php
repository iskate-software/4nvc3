<?php 

echo ' started ' ;
session_start();
require_once('../../tryconnection.php');
mysqli_select_db($tryconnection, $database_tryconnection);
$query_CLASSES = "SELECT CLASSID, CLASS, REGITM3, VAR3, ROUNDER1, MINPRICE1, REGITM6, VAR6, ROUNDER4, MINPRICE4 FROM FORMULA1 ORDER BY CLASS";
$CLASSES = mysqli_query($tryconnection, $query_CLASSES) or die(mysqli_error($mysqli_link));
$row_CLASSES = mysqli_fetch_assoc($CLASSES);





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
  $total = 0 ;
  
  while ($row_AFFECTED = mysqli_fetch_assoc($get_AFFECTED)) {
   $total++ ;
   if ($row_AFFECTED['MANUAL'] != 1 && $row_AFFECTED['MARKUP'] == 1 ) {
   
      $price = $row_AFFECTED['PRICE'] ;
      $oprice = $price ;
      $uprice = $row_AFFECTED['UPRICE'] ;
      $ouprice = $uprice ;
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
     if ($price != $oprice || $uprice != $ouprice) {
      $meddle++ ;
     }
     
   }  
  } 
  
 
  echo "<script type='text/javascript'>" ;
  echo "var w = window.open(''.'nameofwindow')" ;
//  echo "alert('$message');" ;
  echo "window.close() ;" ;
  echo "</script>";
  

 


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ACTUALLY APPLY</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){

}

function bodyonunload() {
  self.close() ; 
  history.back(-2) ;
  
}

</script>

<!-- InstanceEndEditable -->

</head>    
<!-- InstanceEndEditable -->
<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="GET" action="" name="classes" id="classes" style="position:absolute; top:0px; left:0px;">
  <table height="300" width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
     
	  <tr>
      	<td colspan="1">
<div style="height:433px; overflow:auto;">    
<table  width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">      
<tr> <td height="100" >&nbsp;</td>
</tr>
<tr> <td height="100"> &nbsp;</td>
<textarea rows="10" cols="60" class="Verdana14B">









</textarea>
</tr>
<tr>
        <textarea rows="10" cols="60" class="Verdana14B">
         Formula for Class <?php echo $class ; ?> was applied.
         
         There are a total of <?php echo $total ; ?> items in this class,
         including Manual priced items 
         and both Class and Cost based markups.
         
         There were <?php echo $count ; ?> items examined.
         
         <?php echo $meddle ;?> items were actually changed.
        </textarea>
        
      </tr>
 
</table>
</div>
		</td>
      </tr>      
      <tr>
        <td colspan="5" align="center" class="ButtonsTable">
          <input name="apply" type="submit" class="button" id="apply" value="DONE"  />
         </td>
      </tr>
     
    </table>
</form>
</body>
<!-- InstanceEnd -->
</html>