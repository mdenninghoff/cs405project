<?php

/*
 * File: login.php
 *  
 * The MIT License
 *
 * Copyright 2015 matt.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Don't redirect a user, because this is where they'd be redirected to.
define('SKIP_REDIRECT_NOT_LOGGED_IN', TRUE);

require './includes/application_top.php';

$errors = array();

if( isset($_GET['action']))
{
    switch($_GET['action'])
    {
        case 'login':
            // Verify the user's credentials.
            
            if(! isset($_POST['staffId']) )
            {
                $errors[] = 'Staff ID missing';
                break;
            }
            
            if( ! isset($_POST['pass']))
            {
                $errors[] = 'Staff ID missing';
                break;
            }

            $staff = new Staff();
            
            if( ! $staff->init_by_key($_POST['staffId']))
            {
                $errors[] = 'Staff ID not found';
                break;
            }
            
            if( ! password_verify($_POST['pass'], $staff->password))
            {
                $errors[] = 'Invalid Password';
                break;
            }
            
            $_SESSION[SESSION_ID_KEY] = session_id();
            
            $staff->sessionId = $_SESSION[SESSION_ID_KEY];
            if( ! $staff->db_update() )
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
        
        case 'logout':
            logout_user();
            http_redirect(FILENAME_LOGIN);
            break;
    }
    // end switch _GET[action]
}
// end if( isset($_GET['action'])).

$headerAdditionalCss = <<<ENDCSS
 #mainContent { top: -100px; }
ENDCSS;
include './header.php';

if( isset($_GET['action']) && $_GET['action'] == 'logout')
{
    echo 'Logged out';
}

if( count($errors) > 0 )
{
    echo '<pre>';
    
    foreach( $errors as $msg )
    {
        echo $msg . "\n";
    }
    
    echo '</pre>';
}
// done printing errors.

$page = isset($_GET['page']) ? '<input type="hidden" name="page" value="'.$_GET['page'].'" />' :'';

?>
   <form action="login.php?action=login" method="POST">
   Staff ID <input type="text" name="staffId" value="<?php echo isset($_POST['staffId']) ? $_POST['staffId'] : ''; ?>" /><br/>
   Password <input type="password" name="pass" value="" /><br/>
   <?php echo $page; ?>
   <input type="submit" value="Submit" />
   </form>
   
<?php

if( isset($_GET['pass']))
    echo password_hash($_GET['pass']);


include './footer.php';
