<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting( E_ALL );
ini_set('display_errors', 1);

//add global includes here
require('./includes/constants.php');
require('./includes/database.php');
require('./includes/functions.php');

require('../admin/includes/class-dbentity.php');
require('../admin/includes/class-Customers.php');

date_default_timezone_set(TIMEZONE_DEFAULT);

DBEntity::setDatabase($mysqli);

session_set_cookie_params(COOKIE_EXPIRES_SEC, '/'.DIR_CLIENT);
session_start();
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

    // Search for a customer with the session ID.
    $cust = new Customers();
    if( ! $cust->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
    {
        echo $mysqli->error;
        exit();
    }

    // If the query failed to find a value, then unset the $_SESSION value and
    // redirect the user to the login page.
    if( $cust->getKeyValue() === null )
    {
        unset($_SESSION[SESSION_ID_KEY]);
        http_redirect(FILENAME_LOGIN . $redirect_nice );
    }
    //
    // done verifying that user is logged in.
    //
}
// Set the static $mysqli object for all instances of DBEntity (and subclasses).
