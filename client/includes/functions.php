<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function http_redirect($page = '')
{
    header('Location: '.SITE_BASE_URL_CLIENT.$page);
    exit;
}

function href_link($page, $arguments = null )
{
    $args = '';
    if(is_array($arguments) )
    {
        foreach( $arguments as $key => $val )
        {
            $args .= '&'.$key . '=' . $val;
        }
        
        // Remove the first '&' symbol and start with a '?'.
        $args = '?'. substr($args, 1);
    }
    
    return SITE_BASE_URL_CLIENT . $page . $args;
}

function logout_user()
{
    // Unset all of the session variables.
    $_SESSION = array();
    
    if( ini_get('session.use_cookies'))
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly'] );
    }
    
    session_destroy();
}

if( !function_exists('password_verify'))
{
    function password_verify($password, $hash )
    {
        return $hash == crypt($password, CRYPT_SALT);
    }
}

// password_hash only exists as of php >= 5.5
// UK Multilab has php 5.3.10.
if( !function_exists('password_hash'))
{
    // Mirror the PHP builtin password_hash function, and ignore the options.
    function password_hash($password, $algo = 0, $options = array() )
    {
        return crypt($password, CRYPT_SALT);
    }
}