<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('SKIP_REDIRECT_NOT_LOGGED_IN', TRUE);
include '../admin/includes/class-dbentity.php';
include '../admin/includes/class-Customers.php';
require './includes/application_top.php';


$errors = array();


if( isset($_GET['action']))
{
   // echo'<pre>'. print_r($_POST, true).'</pre>';
   // exit();
    switch($_GET['action'])
    {
        case 'login':
            // Verify the user's credentials.
            
            if(! isset($_POST['username']) )
            {
                $errors[] = 'please enter a username';
                break;
            }
            
            if( ! isset($_POST['pass']))
            {
                $errors[] = 'please enter a password';
                break;
            }

            $cust = new Customer();
            
            
            
            $_SESSION[SESSION_ID_KEY] = session_id();
            
            $cust->sessionId = $_SESSION[SESSION_ID_KEY];
            if( ! $cust->db_update() )
            {
                $errors[] = 'Failed to update database: '. $mysqli->error;
                break;
            }
            
            // The credentials were good, so redirect them.
            if( isset($_POST['page']) && file_exists($_POST['page']))
            {
                http_redirect($_POST['page']);
            }

            http_redirect(FILENAME_INDEX);
            
            
            break;
        
       
    }
    // end switch _GET[action]
}
// end if( isset($_GET['action'])).

include './cheader.php';

//error handling


?>
   <ul class='flex-container'>
 
    <form action="login.php?action=login" method="POST">
    <li class='flex-menu2'>
        Name: <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" /></br>
    Password: <input type="password" name="pass" value="" /></br>
    <input type="submit" value="Submit" /></li>
    </form>
   </ul>
   
<?php

if( isset($_GET['pass']))
    echo password_hash($_GET['pass']);



include './cfooter.php';