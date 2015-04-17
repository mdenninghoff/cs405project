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

echo '<h1>Reports</h1>'."\n";
echo '<p>Sales Statistics View the list of all items and sales history in the previous (week, month, or year)</p>';

/**
 * @todo Make a form for choosing week, month, or year.
 * 
 * @todo Make queries to handle such data.
 */

$reportview = REPORTVIEW_WEEK;
if( isset($_GET[GETKEYVIEW]))
{
    $reportview = $_GET[GETKEYVIEW];
}
$stmt = $mysqli->prepare("SELECT itm.itemId, itm.enabled, typ.name,"
        . " itm.qty_available, itm.name, itm.promoRate, itm.price "
        . "FROM Item itm JOIN ItemType typ ON typ.itemTypeId = itm.itemType "
        . " ORDER BY itm.itemId"
        );

$stmt->execute();

$stmt->bind_result($itemid, $enab, $itype, $qty, $name, $promo, $price );

$data = array();

$colnames = array();
$coltypes = array();
$result = $stmt->result_metadata();

$flds = $result->fetch_fields();

//echo '<pre>'. print_r($flds,true).'</pre>';

foreach( $flds as $val )
{
    $colnames[] = $val->name;
    
    // Detect the field type for CSS formatting.
    $type = TableSet::TYPE_STRING;
    switch( $val->type)
    {
        case MYSQLI_TYPE_BIT:
        case MYSQLI_TYPE_LONG:
            $type = TableSet::TYPE_INT;
            break;
        
        case MYSQLI_TYPE_STRING:
            $type = TableSet::TYPE_STRING;
            break;
        
        case MYSQLI_TYPE_NEWDECIMAL:
            $type = TableSet::TYPE_REAL;
            break;
    }
    $coltypes[] = $type;
}

while( $stmt->fetch())
{    
    $col = array( $itemid, $enab, $itype, $qty, $name, $promo, $price );
    $data[] = $col;
}

$stmt->close();

$ts = new TableSet();
$ts->set_data($data);

$ts->show_row_numbers(false);
$ts->replace_column_values(1, array(0 => 'no', 1 => 'yes'));


//die( var_dump($ts->set_column_names($colnames)));
$ts->set_column_names($colnames);
$ts->set_column_name(2, 'Type');

$ts->set_column_types($coltypes);
$ts->set_column_type(1, TableSet::TYPE_STRING);

$ts->add_column('Special Price');
$ts->set_column_type(7, TableSet::TYPE_REAL);
$ts->set_column_width(7, '60px');

// Compute the special price.
// Add the links to edit.
for($row=0,$n=$ts->get_num_rows(); $row < $n; $row++)
{
    $prom = $ts->get_value_at($row, 5);
    $prc = $ts->get_value_at($row, 6);
    
    // Only display a special price.
    if( $prom != 1)
    $ts->set_value_at($row, 7, $prom * $prc);
    
}
// done iterating over rows.

$ts->print_table_html();


require './footer.php';

