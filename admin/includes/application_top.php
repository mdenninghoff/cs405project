<?php
/* 
 * Filename: application_top.php
 * 
 * 
 * Include files that will be used with all admin pages.
 * database.php establishes a mysql connection or dies.
 * 
 * Verify that a user is logged in, or redirect them to the login page.
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

// Note: when application_top.php is included from a script in /admin/,
// then the path is /admin/, and not /admin/includes/, even though application_top
// resides in includes/.

error_reporting( E_ALL );
ini_set('display_errors', 1);

require('./includes/constants.php');
require('./includes/functions.php');
require('./includes/database.php');

require('./includes/class-dbentity.php');
require('./includes/class-staff.php');

// (Certain installations of PHP print warnings if default timezone is not set.)
date_default_timezone_set(TIMEZONE_DEFAULT);

// Set the static $mysqli object for all instances of DBEntity (and subclasses).
DBEntity::setDatabase($mysqli);

// Setup the cookie parameters: give an expiration time so that an admin
// doesn't not remain logged in indefinitely. Use a path so that
// our cookie applies only to our site.
session_set_cookie_params(COOKIE_EXPIRES_SEC, '/'.DIR_ADMIN);

// Send the cookie and session headers.
session_start();

//
// Initialize message stacks in session data. These allow us to display messages
// to a user after they've been redirected.
//

// Initialize the error stack if necessary.
if( ! isset($_SESSION[STACKNAME_ERRORS]) || !is_array($_SESSION[STACKNAME_ERRORS]))
{
    $_SESSION[STACKNAME_ERRORS] = array();
}

// Initialize the stack of notices if it isn't already.
if( ! isset($_SESSION[STACKNAME_NOTICE]) || !is_array($_SESSION[STACKNAME_NOTICE]))
{
    $_SESSION[STACKNAME_NOTICE] = array();
}

// Check if the script that included us wants us not to redirect a user to the
// login page if that user is not logged in.
// The login page should not redirect a user. Otherwise, they'd be in an
// infinite redirect loop.
if( ! defined('SKIP_REDIRECT_NOT_LOGGED_IN') || ! SKIP_REDIRECT_NOT_LOGGED_IN)
{
    //
    // Detect if the user is logged in.
    //

    // Fetch the desired URL before the redirect.
    $redirect_nice = '?page='.basename($_SERVER['SCRIPT_NAME']);

    // Redirect if session ID is not registered.
    // Note: the script stops running upon running http_redirect().
    if( ! isset($_SESSION[SESSION_ID_KEY]) )
    {
        http_redirect(FILENAME_LOGIN . $redirect_nice);
    }

    // Search for a staff with the session ID.
    $staff = new Staff();
    if( ! $staff->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
    {
        echo $mysqli->error;
        exit();
    }

    // If the query failed to find a value, then unset the $_SESSION value and
    // redirect the user to the login page.
    if( $staff->getKeyValue() === null )
    {
        unset($_SESSION[SESSION_ID_KEY]);
        http_redirect(FILENAME_LOGIN . $redirect_nice );
    }
    //
    // done verifying that user is logged in.
    //
}
// done checking if we should redirect.
