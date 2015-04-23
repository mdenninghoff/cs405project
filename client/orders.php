<?php
/* 
 * File: orders.php
 * 
 * @TODO: check if we need a foreign key constraint between OrderItem
 * and Item.
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

const IDSTR = 'oId';


require './includes/application_top.php';
require '../admin/includes/class-ItemType.php';
require '../admin/includes/class-Item.php';
require '../admin/includes/class-OrderItem.php';
require '../admin/includes/class-OrderStatus.php';
require '../admin/includes/class-Orders.php';
require '../admin/includes/class-HtmlWrap.php';
require '../admin/includes/class-TableSet.php';
require '../admin/includes/class-TablesetDefaultCSS.php';

require './cheader.php';

?>
<style type="text/css">
 <?php     
$TDC = new TablesetDefaultCSS();

$TDC->set_css_th_value('background-color', 'tomato');
$TDC->set_css_table_value('background-color', 'white');
$TDC->set_css_footer_value('background-color', 'whitesmoke');
$TDC->print_css();

 // Allow scripts that include us to add extra CSS in this block.
 if( isset($headerAdditionalCss))
 {
     echo $headerAdditionalCss;
 }
 ?>
</style>
<?php

echo '<h1>Orders</h1>'."\n";

//
// Print a form to detail an individual item.
//
if( isset($_GET['action']) && $_GET['action'] == 'detail' && isset($_GET[IDSTR]))
{
    $HW = new HTMLWrap();
    
    $Orders = new Orders();
    $Orders->init_by_key($_GET[IDSTR]);
    
    echo '<fieldset><legend>Viewing Order# '.$Orders->getKeyValue().'</legend>'."\n";
    
//    $itypes = ItemType::fetch_all($mysqli, ItemType::RESULT_ASSOC_ARRAY);
    
    $Date = $Orders->get_dateOrdered();
    
    echo '<p>Customer Name: ' . $Orders->get_customer()->name . "<br/>\n"
            .'Date: ' . $Date->format('D M d, Y g:i A') . "</p>\n";
    
    echo '<p class="shipTo">ShipTo: <br/>' . $Orders->shipTo. "</p>\n";
    
    $statuses = OrderStatus::fetch_all($mysqli);
    
    // Print the status name if it exists, otherwise print the status ID.
    echo '<p>Status: '
        . (isset($statuses[$Orders->statusId]) ? $statuses[$Orders->statusId] : $Orders->statusId)
        . '</p>'."\n";
    
    $ItemTS = new TableSet();
    $ItemTS->show_row_numbers(false);
  
    $columnNames = array('Item ID','Name','Price','Qty<br>Ordered','SubTotal' );
    $columnTypes = array(TableSet::TYPE_INT, TableSet::TYPE_STRING,
        TableSet::TYPE_REAL, TableSet::TYPE_INT, TableSet::TYPE_REAL );
    
    $data = array();
    
    //
    // Make a table and show every item in the order.
    //
    $itemList = $Orders->get_item_list();
    foreach( $itemList as $id => $OrderItem )
    {
        $Item = new Item();
        $Item->init_by_key($id);
        
        $row = array();
        $row[0] = $id;
        $row[1] = $Item->name;
        $row[2] = $OrderItem->price;
        $row[3] = $OrderItem->qty;
        
        $row[4] = $OrderItem->price * $OrderItem->qty;
        
        $data[] = $row;
    }
    // done creating item table data.
    
    $ItemTS->set_data($data);
    
    $ItemTS->set_column_names($columnNames);
    $ItemTS->set_column_types($columnTypes);
    $ItemTS->set_column_format(2, '$%0.2f');
    $ItemTS->set_column_format(4, '$%0.2f');
    $ItemTS->footer = null;
    
    $ItemTS->print_table_html();
    
      
    echo '<br><a href="'.  href_link(FILENAME_ORDERS).'">Back</a>';
    echo "</fieldset>\n\n";
}
// done printing detail form.
else
{
        $cust = new Customers();
        if( ! $cust->init_by_sessionId($_SESSION[SESSION_ID_KEY]) )
        {
            echo $mysqli->error;
            exit();
        }
    // Show the table of orders.
    $stmt = $mysqli->prepare("SELECT o.orderId, c.name as custname,"
        . " o.dateOrdered, os.name as ostatus, o.shipTo "
        . "FROM Orders o JOIN OrderStatus os ON os.statusId = o.statusId "
        . "JOIN Customer c ON c.custId = o.custId WHERE c.custId = ". $cust->getKeyValue()
        . " ORDER BY o.orderId" );

    $stmt->execute();

    $stmt->bind_result($orderId, $custId, $dateOrdered, $statusId, $shipTo );

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
        $col = array( $orderId, $custId, $dateOrdered, $statusId, $shipTo );
        $data[] = $col;
    }

    $stmt->close();


    //include('./includes/class-TableSet.php');

    $ts = new TableSet();
    $ts->set_data($data);

    $ts->show_row_numbers(false);
    //$ts->replace_column_values(1, array(0 => 'no', 1 => 'yes'));


    $ts->set_column_names($colnames);

    $ts->set_column_name(0, 'Order #');
    $ts->set_column_name(1, 'Customer');
    $ts->set_column_name(2, 'Date');
    $ts->set_column_name(3, 'Status');
    
    
    $ts->add_column('&nbsp;');

    // Add the links to detail.
    for($row=0,$n=$ts->get_num_rows(); $row < $n; $row++)
    {
        $itemId = $ts->get_value_at($row, 0);
        $href = '<a href="'.href_link(FILENAME_ORDERS, array('action' => 'detail', IDSTR => $itemId))
                .'">detail</a>';
        $ts->set_value_at($row, 5, $href);
    }
    // done iterating over rows.

    $ts->print_table_html();
    
}
// done showing table of orders.

require './cfooter.php';

