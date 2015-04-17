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

const IDSTR = 'itemId';

require './includes/application_top.php';
require './includes/class-ItemType.php';
require './includes/class-Item.php';
require './includes/class-HtmlWrap.php';


if( isset($_GET['action']) )
{
//    die( print_r($_POST,true) . print_r($_GET,true));
    if( $_GET['action'] == 'update' && isset($_GET[IDSTR]) )
    {
            // @TODO: verify manager  type.

        $Item = new Item();
        $Item->init_by_key($_GET[IDSTR]);

        // @TODO: if item not found, show error message and redirect.

        if( ! isset($_POST['enabledbox'])) 
            $Item->enabled = false;
        else
            $Item->enabled = true;

        $Item->itemType = $_POST['itype'];
        $Item->qty_available = $_POST['qty'];
        $Item->name = $_POST['name'];
        
        // Only allow managers to update the promo rate.
        if( $staff->isManager)
            $Item->promoRate = $_POST['promo'];
        
        $Item->price = $_POST['price'];
        $Item->imageName = $_POST['image'];

        $Item->db_update();

        // @TODO: show any errors.

        // Redirct back to items.php.
        http_redirect(FILENAME_ITEMS);
    }
    else if( $_GET['action'] == 'insert' )
    {
        $Item = new Item();

        if( ! isset($_POST['enabledbox'])) 
            $Item->enabled = false;
        else
            $Item->enabled = true;

        $Item->itemType = $_POST['itype'];
        $Item->qty_available = $_POST['qty'];
        $Item->name = $_POST['name'];
        $Item->promoRate = $_POST['promo'];
        $Item->price = $_POST['price'];
        $Item->imageName = $_POST['image'];

//        var_dump($Item->db_insert());

        // @TODO: show any errors.

        // Redirct back to items.php.
        http_redirect(FILENAME_ITEMS);
        exit;
    }
}

$headerAdditionalCss = <<<ENDCSS
label { display:block; float:left; width:120px; }

form fieldset p { margin: 5px; }
ENDCSS;

require './header.php';

echo '<h1>Items</h1>'."\n";

$editItem = false;
if( isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET[IDSTR]))
{
    $editItem = true;
}

$stmt = $mysqli->prepare("SELECT itm.itemId, itm.enabled, typ.name,"
        . " itm.qty_available, itm.name, itm.promoRate, itm.price "
        . "FROM Item itm JOIN ItemType typ ON typ.itemTypeId = itm.itemType "
        . ($editItem ? " WHERE itm.itemId = ".(int)$_GET[IDSTR] : "")
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


//include('./includes/class-TableSet.php');

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

if( ! $editItem)
    $ts->add_column('&nbsp;');

// Compute the special price.
// Add the links to edit.
for($row=0,$n=$ts->get_num_rows(); $row < $n; $row++)
{
    $prom = $ts->get_value_at($row, 5);
    $prc = $ts->get_value_at($row, 6);
    
    // Only display a special price.
    if( $prom != 1)
    $ts->set_value_at($row, 7, $prom * $prc);
    
    if( ! $editItem)
    {
        $itemId = $ts->get_value_at($row, 0);
        $href = '<a href="'.href_link(FILENAME_ITEMS, array('action' => 'edit', IDSTR => $itemId))
                .'">edit</a>';
        $ts->set_value_at($row, 8, $href);
    }
    // end $editItem.
}
// done iterating over rows.

$ts->print_table_html();

if( ! $editItem )
    echo '<a href="'.  href_link(FILENAME_ITEMS, array('action'=>'create')).'">Insert</a><br/>'."\n";

if( isset($_GET['action']) && $_GET['action']=='create')
{
    // @TODO: combine insert and edit code.
    $HW = new HTMLWrap();
    
    echo '<form action="'.  href_link(FILENAME_ITEMS, array('action' => 'insert' )).'" method="POST">'."\n"
            .'<fieldset><legend>Creating New Item</legend>'
            ."\n";
    
    $itypes = ItemType::fetch_all($mysqli, ItemType::RESULT_ASSOC_ARRAY);
    
    $Item = new Item();
    
    echo '<p>';
    $HW->print_checkbox('enabledbox', 1, 'Enabled', $Item->enabled);
    echo "</p>";
    
    $HW->print_select('itype', $itypes, 'Item Type', $Item->itemType );
    echo "<br/>";
    
    $HW->print_textbox('qty', $Item->qty_available, 'Qty Avail');
    echo "<br/>";
    
    $HW->print_textbox('name', $Item->name, 'Name');
    echo "<br/>";
    
    if( $staff->isManager )
    {
        $HW->print_textbox('promo', $Item->promoRate, 'Promo Rate');
        echo "<br/>";
    }
    else
    {
        echo 'Promo Rate: '.$Item->promoRate."<br/>\n";
    }
    
    $HW->print_textbox('price', $Item->price, 'Price');
    echo "<br/>";
    
    $HW->print_textbox('image', $Item->imageName, 'Image');
    echo "<br/>";
    
    echo '<br><input type="submit" value="Insert" /> <a href="'.  href_link(FILENAME_ITEMS).'">Cancel</a>';
    echo "</fieldset>\n</form>\n";
}
// done insert form.

//
// Print a form to edit an individual item.
//
if(  $editItem )
{
    $HW = new HTMLWrap();
    
    echo '<form action="'.  href_link(FILENAME_ITEMS, array('action' => 'update', IDSTR => $_GET[IDSTR])).'" method="POST">'."\n"
            .'<fieldset><legend>Editing Item ID: '.$_GET[IDSTR].'</legend>'
            ."\n";
    
    $itypes = ItemType::fetch_all($mysqli, ItemType::RESULT_ASSOC_ARRAY);
    
    $Item = new Item();
    $Item->init_by_key($_GET[IDSTR]);
        
    $HW->print_checkbox('enabledbox', 1, 'Enabled', $Item->enabled);
    echo "<br/>";
    
    echo '<p>';
    $HW->print_select('itype', $itypes, 'Item Type', $Item->itemType );
    echo "</p>";
    
    echo '<p>';
    $HW->print_textbox('qty', $Item->qty_available, 'Qty Avail');
    echo "</p>";
    
    echo '<p>';
    $HW->print_textbox('name', $Item->name, 'Name');
    echo "</p>";
    
    echo '<p>';
    if( $staff->isManager )
    {
        $HW->print_textbox('promo', $Item->promoRate, 'Promo Rate');
        echo "<br/>";
    }
    else
    {
        echo '<label>Promo Rate</label> '.$Item->promoRate;
    }
    echo "</p>";
    
    echo '<p>';
    $HW->print_textbox('price', $Item->price, 'Price');
    echo "</p>";
    
    echo '<p>';
    $HW->print_textbox('image', $Item->imageName, 'Image');
    echo "</p>";

    echo '<input type="submit" value="Update" /> <a href="'.  href_link(FILENAME_ITEMS).'">Cancel</a>';
    echo "</fieldset>\n</form>\n";
}
// done printing edit form.

require './footer.php';

