<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('SKIP_REDIRECT_NOT_LOGGED_IN', TRUE);
require './includes/application_top.php';
//include '../admin/includes/class-dbentity.php';
//include '../admin/includes/class-Customers.php';



$errors = array();


if( isset($_GET['action']))
{
   // echo'<pre>'. print_r($_POST, true).'</pre>';
   // exit();
    if($_GET['action'] == 'register')
    {
          
            
            $cust = new Customers();
            $cust->name = $_POST['username'];
            $cust->password = crypt($_POST['pass'], CRYPT_SALT);
            
           
            
            
            $_SESSION[SESSION_ID_KEY] = session_id();
            
            $cust->sessionId = $_SESSION[SESSION_ID_KEY];
            $cust->db_insert();
           
            
        

          //  http_redirect(FILENAME_INDEX);
            
            
         
        
       
    }
    // end switch _GET[action]
}
// end if( isset($_GET['action'])).

include './cheader.php';

//error handling


if( isset($_POST['username']) )
{
    echo '<h2>Welcome ' . $_POST['username'] . '</h2>';
}
else
{
?>
   <ul class='flex-container'>
 
    <form action="register.php?action=register" method="POST">
    <li class='flex-menu2'>
    Name: <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" /></br>
    Password: <input type="password" name="pass" value="<?php echo isset($_POST['pass']) ? $_POST['pass'] : ''; ?>" /></br>
    <input type="submit" value="Submit" /></li>
    </form>
   </ul>
   
<?php
}


if( isset($_GET['pass']))
    echo password_hash($_GET['pass']);



include './cfooter.php';