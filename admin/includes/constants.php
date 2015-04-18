<?php
/* 
 * constants.php
 * 
 * Define global constants for use in the application here.
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

define('SESSION_ID_KEY', 'sessID');

define('STORE_NAME', 'Amazam.cam');
define('STORE_LOGO_IMG', 'logo.jpg');

// The path of admin folder. This is used to set the cookie path, and it's
// used in SITE_BASE_URL_ADMIN, which is used by http_href() function.
// It's also used in FS_ADMIN_BASE_DIR.
define('DIR_ADMIN', 'cs405/admin/');

define('DIR_STORE', 'cs405/');

// Numer of seconds before a logged-in admin's cookie expires. 7200 = 2 hours.
define('COOKIE_EXPIRES_SEC', 7200);

// Used by http_href() function.
define('SITE_BASE_URL', 'http://localhost/cs405/');
define('SITE_BASE_URL_ADMIN', 'http://localhost/'.DIR_ADMIN);

// The base filesystem directory where the admin scripts exist.
// this makes it easy to include files without needing to worry about which
// directory the running script is located.
// Note: each path string must have a trailing slash.
define('FS_ADMIN_BASE_DIR', '/Library/WebServer/Documents/' . DIR_ADMIN);

define('FS_STORE_BASE_DIR', '/Library/WebServer/Documents/' . DIR_STORE);

define('DIR_INCLUDES', 'includes/');

define('DIR_IMAGES', 'images/');
define('DIR_IMAGES_PRODUCTS', 'images/products/');

define('FILENAME_INDEX', 'index.php');
define('FILENAME_LOGIN', 'login.php');
define('FILENAME_ITEMS', 'items.php');
define('FILENAME_ORDERS', 'orders.php');
define('FILENAME_REPORTS', 'reports.php');
define('FILENAME_SPECIALS', 'specials.php');

define('MYSQLI_BIND_TYPE_INT',    'i');
define('MYSQLI_BIND_TYPE_DOUBLE', 'd');
define('MYSQLI_BIND_TYPE_STRING', 's');
define('MYSQLI_BIND_TYPE_BLOB',   'b');

define('TIMEZONE_DEFAULT', 'America/New_York');

define('CRYPT_SALT', 'm1RMaNe!');

//DATE
define('DATE_ADMIN_ORDER_BRIEF', 'D Y-M-d g:i A');  // in the list of orders. 
define('DATE_ADMIN_ORDER_DETAIL', 'D M d, Y g:i A');    // in the detail of an order.

define('STACKNAME_ERRORS','errors');
define('STACKNAME_NOTICE','notice');