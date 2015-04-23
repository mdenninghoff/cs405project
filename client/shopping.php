<?php
require './includes/application_top.php';
include '../admin/includes/class-Cart.php';
include '../admin/includes/class-Item.php';
require './cheader.php';

const IDSTR= 'itemId';
/*
?>
<script type="text/javascript">

        //function foo() {
            //    window.alert("blah");
        //}
        
        //definitely not going to work
        function foo2(ident){
                var string1="<?";
                var string2="php "
                var string3="?>";
                
                
                var teststring="$car_t = new Cart(); $car_t->itemId = ident; $car_t->db_insert();";
                alert(string1+ string2+ teststring + string3);
        }


</script>
 
 
<?php
*/
        //$con = mysqli_connect("mysql.cs.uky.edu", "madenn2", "happydragon1buffet", "madenn2");
        //if(!$con){
        //        die("Connection Failed: ". mysqli_connect_error());
        //}
if( isset($_GET['action']) )
{
    if( $_GET['action'] == 'cart' && isset($_GET[IDSTR]) )
    {
        $cust = new Customers();
        if( ! $cust->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
        {
            echo $mysqli->error;
            exit();
        }
        
        //need to make this more robust later
        $car_t = new Cart();
        $car_t->itemId = $_GET[IDSTR];
        $query = "SELECT qty FROM Cart WHERE custId=".$cust->getKeyValue(). " AND itemId=". $_GET[IDSTR];
        $result = $mysqli->query($query);
        $my_r = mysqli_fetch_assoc($result);
        if( $my_r["qty"])
        {
            $car_t->qty = ($my_r["qty"]+1);
            $car_t->setCustId($cust->getKeyValue());
            $car_t->db_update();
        }
        else
        {
            $car_t->qty =1;
            $car_t->setCustId($cust->getKeyValue());
            $car_t->db_insert();
        }
        $result->close();
        
    }
}
        $query = "SELECT name, price, promoRate, itemID, imageName FROM Item";


        $result = $mysqli->query($query);
  
        if(mysqli_num_rows($result)>0){
                echo "<ul class='flex-container'>";
                while($row = mysqli_fetch_assoc($result)) {
                        //echo "$row["name"]";
                        echo "<li class='flex-item'>";
                        echo $row["name"] . " ";
                        echo ($row["price"] * $row['promoRate']);
                        echo "</br>";
                        echo '<img src="/'.DIR_CLIENT . DIR_IMAGES_PRODUCTS.$row['imageName'].'" width="100px" /><br/>';
                        //echo " <button class='my-button' onclick='foo2({$row["itemID"]})'>add to cart</button>";
                        //echo ' <button class="my-button"><a href="' . href_link(FILENAME_SHOPPING, array('action'=>'cart')). '">add to cart</a> '</button>';
                        echo '<div class="my-button"><a href="'.  href_link(FILENAME_SHOPPING, array('action'=>'cart', IDSTR=>$row["itemID"])).'">add to cart</a></div>'."\n";
                        echo "</li>";
                }
                echo "</ul>";

        }
        else{

                echo "0 results";

        }





require './cfooter.php';
/* 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

