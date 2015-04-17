<?php
require './includes/application_top.php';
require './cheader.php';
?>
<script>

        function foo() {
                window.alert("blah");
        }
        function foo2(ident){
                window.alert(ident);
        }


</script>
<?php

        //$con = mysqli_connect("mysql.cs.uky.edu", "madenn2", "happydragon1buffet", "madenn2");
        //if(!$con){
        //        die("Connection Failed: ". mysqli_connect_error());
        //}

        $query = "SELECT name, price, itemID FROM Item";


        $result = $mysqli->query($query);
  
        if(mysqli_num_rows($result)>0){
                echo "<ul class='flex-container'>";
                while($row = mysqli_fetch_assoc($result)) {
                        //echo "$row["name"]";
                        echo "<li class='flex-item'>";
                        echo $row["name"] . " ";
                        echo $row["price"];
                        echo "</br>";
                        echo " <button class='my-button' onclick='foo2({$row["itemID"]})'>add to cart</button>";
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

