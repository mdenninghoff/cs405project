<?php
/* 
 * header.php
 * 
 * Print the HTML page header used in all admin pages.
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

include './includes/class-TableSet.php';
require('./includes/class-TablesetDefaultCSS.php');

//
// Make a page header to be shown in all admin pages. Includes the navigation
// box.
//

//TableSet::set_default_css();

?>
<!DOCTYPE html>
<html>
 <head>
  <title><?php echo STORE_NAME; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="stylesheet.css" >
    <style type="text/css">
     <?php     
    $TDC = new TablesetDefaultCSS();
    $TDC->set_css_th_value('background-color', 'whitesmoke');
    $TDC->set_css_footer_value('background-color', 'silver');
    $TDC->print_css();
     
     // Allow scripts that include us to add extra CSS in this block.
     if( isset($headerAdditionalCss))
     {
         echo $headerAdditionalCss;
     }
     ?>
    </style>
    <script type="text/javascript"></script>
 </head>
 <body>
  <div id="header">
   <img class="logo" width="100px" height="100px" src="<?php echo STORE_LOGO_IMG; ?>" />
   <?php echo STORE_NAME; ?>
  </div>
  <div id="navBox">
   <div class="boxlabel">Links</div>
   <ul>
    <li><a href="<?php echo href_link(FILENAME_ORDERS); ?>">Orders</a></li>
    <li><a href="<?php echo href_link(FILENAME_ITEMS); ?>">Items</a></li>
    <li><a href="<?php echo href_link(FILENAME_REPORTS); ?>">Reports</a></li>
    <li><a href="<?php echo href_link(FILENAME_LOGIN, array('action' => 'logout') ); ?>">Log Out</a></li>
   </ul>
  </div>
  <div id="mainContent">
  <?php
  // Print the error stack.
  if( count($_SESSION[STACKNAME_ERRORS]) > 0)
  {
      echo '<div id="errors">'."\n";
      
//      echo '<pre>'.print_r($_SESSION[STACKNAME_ERRORS], true) . '</pre>';
      
      foreach( $_SESSION[STACKNAME_ERRORS] as $msg )
      {
          echo "$msg<br/>\n";
      }
      // Clear the errors once they've been displayed; otherwise they are
      // permanently displayed.
      unset($_SESSION[STACKNAME_ERRORS]);
      echo "</div><!-- end error stack -->\n";
  }
  // done printing error stack.
  
  // Print the error stack.
  if( count($_SESSION[STACKNAME_NOTICE]) > 0)
  {
      echo '<div id="notices">'."\n";
      
      //      echo '<pre>'.print_r($_SESSION[STACKNAME_NOTICE], true) . '</pre>';

        foreach( $_SESSION[STACKNAME_NOTICE] as $msg )
        {
            echo "$msg<br/>\n";
        }
      
      // Clear the notices once they've been displayed; otherwise they are
      // permanently displayed.
      unset($_SESSION[STACKNAME_NOTICE]);
      echo "</div><!-- end notice stack -->\n";
  }
  // done printing notice stack. 
