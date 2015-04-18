<?php

/* 
 * File: reports.php
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

require './includes/application_top.php';
require './includes/class-HtmlWrap.php';

const REPORTVIEW_WEEK  = 0;
const REPORTVIEW_MONTH = 1;
const REPORTVIEW_YEAR  = 2;
const GETKEYVIEW = 'view';

// Redirect anyone who is not a manager and give an error message.
if( ! $staff->isManager )
{
    $_SESSION[STACKNAME_ERRORS][] = 'Only Managers may view reports';
    http_redirect(FILENAME_INDEX);
}

$headerAdditionalCss = <<<ENDCSS
label { display:block; float:left; width:120px; }

form fieldset p { margin: 5px; }
ENDCSS;

require './header.php';
require './includes/class-MysqlResultTable.php';

echo '<h1>Reports</h1>'."\n";

// Default the report to show the last week.
$reportview = REPORTVIEW_WEEK;

// If the URL contained a different timeframe, then use it.
if( isset($_GET[GETKEYVIEW]))
{
    $reportview = $_GET[GETKEYVIEW];
}

// Construct the report time string.
$report_time_string = "1 WEEK";
switch($reportview)
{
    case REPORTVIEW_MONTH:
        $report_time_string = "1 MONTH";
        break;
    case REPORTVIEW_YEAR:
        $report_time_string = "1 YEAR";
}

echo '<p>Sales Statistics View the list of all items and sales history in the previous <b>'.$report_time_string.'</b></p>';

echo '<ul>';
echo '<li><a href="'.  href_link(FILENAME_REPORTS, array(GETKEYVIEW => REPORTVIEW_WEEK)).'">1 WEEK</a></li>';
echo '<li><a href="'.  href_link(FILENAME_REPORTS, array(GETKEYVIEW => REPORTVIEW_MONTH)).'">1 MONTH</a></li>';
echo '<li><a href="'.  href_link(FILENAME_REPORTS, array(GETKEYVIEW => REPORTVIEW_YEAR)).'">1 YEAR</a></li>';
echo '</ul>'
;

$MRT = new MysqlResultTable($mysqli);
$MRT->show_row_numbers(false);

$MRT->executeQuery("select 
    oi.itemId,
    max(i.name) as name,
    min(o.dateOrdered) firstOrdered,
    max(o.dateOrdered) as lastOrdered,
    sum(oi.qty) as qty, sum(oi.qty * oi.price) as subT, 
    max(i.itemType) as itype
FROM OrderItem oi JOIN Item i ON i.itemId = oi.itemId
JOIN Orders o ON o.orderId = oi.orderId
WHERE o.dateOrdered > date_sub(now(), INTERVAL $report_time_string)
GROUP BY oi.itemId
");

$MRT->print_table_html();

require './footer.php';

