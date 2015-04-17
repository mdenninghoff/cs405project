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

session_set_cookie_params(COOKIE_EXPIRES_SEC, '/'.DIR_CLIENT);
session_start();

/*
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
}*/