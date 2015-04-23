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

// @TODO: verify manager  type.

if( isset($_GET['action']) )
{
    if( $_GET['action'] == 'shipit' && isset($_GET[IDSTR]) )
    {
        /*
         * If all the components are available, the status of the order changes
         *  from "Pending" to "Shipped" and the quantities in the inventory are
         *  decreased. If the components are not available, some error page
         *  listing the missing components is generated and the order remains 
         * "Pending".
         */
        
        // Verify that all the items are available.
        $Order = new Orders();
        $Order->init_by_key($_GET[IDSTR]);
        
        try
        {
            $missing = null;
            $res = $Order->shipIt($missing);
            
            if( $res )
            {
                $_SESSION[STACKNAME_NOTICE][] = 'Shipped';   
            }
            else
            {
                $_SESSION[STACKNAME_ERRORS][] = 'Quantity unavailable to ship order.';
                foreach( $missing as $msg )
                {
                    $_SESSION[STACKNAME_ERRORS][] = $msg;
                }
            }
        }
        catch( Exception $ex)
        {
            $_SESSION[STACKNAME_ERRORS][] = $ex->getMessage();
        }

        // Redirct back to items.php?oId=12345.
        http_redirect(FILENAME_ORDERS . '?action=edit&'.IDSTR.'='.$_GET[IDSTR]);
        
        exit;
    }
}

$headerAdditionalCss = <<<ENDCSS
 p.shipTo { background-color: white; } 
        
 fieldset table.tableset th { background-color: yellowgreen;}
 fieldset table.tableset { background-color: #ddd;}
        
label { display:block; float:left; width:120px; }

form fieldset p { margin: 5px; }

form fieldset {margin-top: 5px; }
form fieldset legend { background-color:#33a383; padding:6px; }
ENDCSS;
require './cheader.php';

echo '<h1>Orders</h1>'."\n";

//
// Print a form to edit an individual item.
//
if( isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET[IDSTR]))
{
    $HW = new HTMLWrap();
    
    $Orders = new Orders();
    $Orders->init_by_key($_GET[IDSTR]);
    
    echo '<form action="'.  href_link(FILENAME_ORDERS, array('action' => 'shipit', IDSTR => $_GET[IDSTR])).'" method="POST">'."\n"
            .'<fieldset><legend>Viewing Order# '.$Orders->getKeyValue().'</legend>'
            ."\n";
    
//    $itypes = ItemType::fetch_all($mysqli, ItemType::RESULT_ASSOC_ARRAY);
    
    $Date = $Orders->get_dateOrdered();
    
    echo '<p>Customer Name: ' . $Orders->get_customer()->name . "<br/>\n"
            .'Date: ' . $Date->format(DATE_ADMIN_ORDER_DETAIL) . "</p>\n";
    
    echo '<p class="shipTo">ShipTo: <br/>' . $Orders->shipTo. "</p>\n";
    
    $statuses = OrderStatus::fetch_all($mysqli);
    
    // Print the status name if it exists, otherwise print the status ID.
    echo '<p>Status: '
        . (isset($statuses[$Orders->statusId]) ? $statuses[$Orders->statusId] : $Orders->statusId)
        . '</p>'."\n";
    
    $ItemTS = new TableSet();
    $ItemTS->show_row_numbers(false);
  
    $columnNames = array('Item ID','Name','Price','Qty<br>Ordered','SubTotal','Qty<br>Available');
    $columnTypes = array(TableSet::TYPE_INT, TableSet::TYPE_STRING,
        TableSet::TYPE_REAL, TableSet::TYPE_INT, TableSet::TYPE_REAL,
        TableSet::TYPE_INT);
    
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
        
        
        
        $row[5] = $Item->qty_available;
        
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
    
//    echo '<pre>' . print_r($Orders,true) . "</pre>\n";
      
    echo '<br><input type="submit" value="SHIP IT" /> <a href="'.  href_link(FILENAME_ORDERS).'">Cancel</a>';
    echo "</fieldset>\n</form>\n";
}
// done printing edit form.
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
    //
    //$ts->set_column_types($coltypes);
    //$ts->set_column_type(1, TableSet::TYPE_STRING);
    //
    //$ts->add_column('Special Price');
    //$ts->set_column_type(7, TableSet::TYPE_REAL);
    //$ts->set_column_width(7, '60px');

    
    $ts->add_column('&nbsp;');

    // Add the links to edit.
    for($row=0,$n=$ts->get_num_rows(); $row < $n; $row++)
    {
        $itemId = $ts->get_value_at($row, 0);
        $href = '<a href="'.href_link(FILENAME_ORDERS, array('action' => 'edit', IDSTR => $itemId))
                .'">edit</a>';
        $ts->set_value_at($row, 5, $href);
    }
    // done iterating over rows.

    $ts->print_table_html();
    
}
// done showing table of orders.

require './cfooter.php';

