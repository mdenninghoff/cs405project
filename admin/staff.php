<?php

/* 
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

require './includes/application_top.php';


//
// Handle page requests here.
//



require './header.php';

echo '<h1>Staff</h1>'."\n";





//
// Page stuff.
//

echo '<form action="'.  basename($_SERVER['SCRIPT_NAME']).'" method="GET">'."\n";
echo 'Pass: <input type="text" name="passstring" value="'. (isset($_GET['passstring']) ? $_GET['passstring'] : '') .'" />';
echo '<input type="submit" value="Submit" />'."<br/>\n";
echo '</form>'."\n";



if( isset($_GET['passstring']))
{
    echo '<div style="margin-top:20px;">';
    echo 'Crypted: <input type="text" name="foo" value="'.password_hash($_GET['passstring']).'" />'."\n";
    echo '</div>';
}

require './footer.php';

