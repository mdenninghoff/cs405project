<?php

define('SKIP_REDIRECT_NOT_LOGGED_IN', TRUE);
require './includes/application_top.php';
require '../admin/includes/class-Orders.php';
require '../admin/includes/class-OrderItem.php';

if( isset($_GET['action']))
{
    if($_GET['action']=='order')
    {
        $cust1 = new Customers();
        if( ! $cust1->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
        {
            echo $mysqli->error;
            exit();
        }
        $c_order = new Orders();
        $c_order->custId = $cust1->getKeyValue();
        $c_order->shipTo = "Some address";
        $c_order->statusId = 1;
        $c_order->db_insert();
        $cquery = "SELECT itemId, qty FROM Cart WHERE custId=".$cust1->getKeyValue();
        $cresult = $mysqli->query($cquery);
        while($c_row = mysqli_fetch_assoc($cresult)) {
            $cquery2= "SELECT promoRate, price FROM Item WHERE itemId =". $c_row["itemId"]; 
            $cresult2 = $mysqli->query($cquery2);
            $cr_info = mysqli_fetch_assoc($cresult2);
            $c_order_item = new OrderItem();
            $c_order_item->orderId = $c_order->getKeyValue();
            $c_order_item->itemId = $c_row['itemId'];
            $c_order_item->price = ($cr_info['price'] * $cr_info['promoRate']);
            $c_order_item->qty = $c_row['qty'];
            $c_order_item->db_insert();
        }
    }
}


include './cheader.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 $cust = new Customers();
 if( ! $cust->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
 {
    echo $mysqli->error;
    exit();
 }
$cart_cost = 0;
$query = "SELECT itemId, qty FROM Cart WHERE custId=".$cust->getKeyValue();
$result = $mysqli->query($query);
//$query2= "SELECT name, promoRate, price, imageName FROM Item WHERE itemId =". $result["itemId"];
//$result2 = $mysqli->query($query2);
//$row2 = mysqli_fetch_assoc($result2);
if(mysqli_num_rows($result)>0){
                echo "<ul class='flex-container'>";
                while($row = mysqli_fetch_assoc($result)) {
                        //echo "$row["name"]";
                        $query2= "SELECT name, promoRate, price, imageName FROM Item WHERE itemId =". $row["itemId"]; 
                        $result2 = $mysqli->query($query2);
                        $r_info = mysqli_fetch_assoc($result2);
                        $r_price = $r_info["price"] * $r_info["promoRate"];
                        $cart_cost= $cart_cost+($r_price*$row["qty"]);
                        echo "<li class='flex-item'>";
                        echo $r_info["name"] . " ";
                        echo $r_price . " </br>";
                        //echo $row["name"]. " ";
                        echo $row["qty"];
                       // echo "</br>";
                        //echo " <button class='my-button' onclick='foo2({$row["itemID"]})'>add to cart</button>";
                        //echo ' <button class="my-button"><a href="' . href_link(FILENAME_SHOPPING, array('action'=>'cart')). '">add to cart</a> '</button>';
                       // echo '<button class="my-button"><a href="'.  href_link(FILENAME_SHOPPING, array('action'=>'cart', IDSTR=>$row["itemID"])).'">add to cart</a></button>'."\n";
                        echo "</li>";
                }
                echo "</ul>";
                echo "<ul class='flex-container'>";
                echo "<li class='flex-menu'>";
                echo '<button class="my-button"><a href="'.  href_link(FILENAME_CART, array('action'=>'order')).'">order</a></button>'."\n";
                echo "total cost: ". $cart_cost;
                echo "</li>";
                echo "<li class='flex-menu'>";
                
                echo "</li>";
                echo "</ul>";
        }
        else{

                echo "0 results";

        }
include './cfooter.php';