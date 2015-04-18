<?php
/* 
 * class-Orders.php
 * 
 * Defines the order db entity class.
 * 
 * This class is more than just a wrapper of table data: it also contains
 * OrderItem objects and a Customer object.
 * 
 * The calling class needs to include the required classes.
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


class Orders extends DBEntity
{   
    /**
     *
     * @var int
     */
    public $custId = null;
    
    /**
     *
     * @var int
     */
    public $statusId = null;
    
    /**
     *
     * @var string
     */
    public $shipTo = null;
    
    /**
     *
     * @var DateTime 
     */
    protected $dateOrdered = null;
    
    /**
     *
     * @var OrderItem[] An array of OrderItem objects.
     */
    protected $items;
    
    /**
     *
     * @var Customers
     */
    protected $Customer = null;
    
    const SQL_COLUMN_LIST = " orderId, custId, dateOrdered, statusId, shipTo ";
    
    // this is sloppy, but for now, this should be whatever the status id in
    // the db for pending is.
    const STATUS_ID_PENDING = 1;
    const STATUS_ID_SHIPPED = 2;
    
    public function __construct( $id = null )
    {
        parent::__construct($id);
        $this->tableName = 'Orders';
        $this->keyName = 'orderId';
        
        $this->items = array();
    }
    
    /**
     * Returns a DateTime object containing the date the order was placed.
     * 
     * @return DateTime
     */
    public function get_dateOrdered()
    {
        return $this->dateOrdered;
    }
    
    /**
     * 
     * @param OrderItem $Item Add or replace an item to this order.
     */
    public function set_item( $Item )
    {
        if( $Item->itemId != null )
        {
            // Set orderId for the OrderItem to be same as our orderId.
            $Item->keyValue = $this->keyValue;
            
            // Add the item.
            $this->items[ $Item->itemId ] = $Item;
        }
    }
    
    /**
     * 
     * @param int $itemId
     * @return OrderItem
     */
    public function get_item($itemId)
    {
        if( isset($this->items[$itemId]))
        {
            return $this->items[$itemId];
        }
        return null;
    }
    
    /**
     * 
     * @return OrderItem[]
     */
    public function get_item_list()
    {
        return $this->items;
    }
    
    /**
     * Loads all OrderItems associated with this order into this->items.
     * 
     * @return boolean
     */
    public function load_all_items()
    {
        $retval = false;
        
        if( $this->keyValue != null )
        {
            $stmt = self::$mysqli->prepare("SELECT itemId, price, qty FROM OrderItem WHERE orderId = ? ");
            if( $stmt )
            {
                if( $stmt->bind_param(MYSQLI_BIND_TYPE_INT, $this->keyValue))
                {
                    if( $stmt->execute())
                    {
                        $stmt->bind_result($itemId, $price, $qty);
                        
                        while( $stmt->fetch())
                        {
                            $OItem = new OrderItem($this->keyValue);
                            $OItem->itemId = $itemId;
                            $OItem->price = $price;
                            $OItem->qty = $qty;
                            
                            $this->items[ $itemId ] = $OItem;
                        }
                        $retval = true;
                    }
                }
                $stmt->close();
            }
        }
        
        return $retval;
    }
  
    /**
     * Set the class member values by fetching values from the database.
     * If the key was not found, then the class member fields remain null.
     * 
     * Also loads all order items into this->items, and loads customer into
     * this->customer.
     * 
     * @param string $value
     * The table key to search the database for.
     * 
     * @return boolean
     * Returns true if data was fetched.
     * Returns false if there was an error.
     * Returns null if no data was found.
     */
    public function init_by_key($value)
    {
        $retval = false;
        $stmt = self::$mysqli->prepare("SELECT " . self::SQL_COLUMN_LIST
                . "FROM ".$this->tableName." WHERE ".$this->keyName." = ? ");
        
        if( ! $stmt )
            throw new Exception (self::$mysqli->error, self::$mysqli->errno );
        
        $dateOrdered_str = null;
        // Try to fetch the results; throw an exception on failure.
        try
        {
            if( ! $stmt->bind_param(MYSQLI_BIND_TYPE_INT, $value) )
            {
                throw new Exception('Failed to bind_param');
            }
            
            if( ! $stmt->execute() )
            {
                throw new Exception('Failed to execute statement:' . self::$mysqli->error);
            }
            
            $stmt->bind_result($this->keyValue, 
                            $this->custId,
                            $dateOrdered_str,
                            $this->statusId,
                            $this->shipTo  );
            $retval = $stmt->fetch();
            
        } catch (Exception $ex) {
            $stmt->close();
            throw $ex;
        }
        // end catch exception.
        $stmt->close();

        $this->Customer = new Customers();
        $this->Customer->init_by_key($value);

        $this->dateOrdered = new DateTime($dateOrdered_str);
        
        $this->load_all_items();
        
        return $retval;
    }
    // end find_by_key().
    
    /**
     * Update a record matching this record's key with this class member's values.
     * Note: changing an order's date is not supported now.
     * 
     * @return boolean
     */
    public function db_update()
    {
        $retval = false;
        
        if( $this->getKeyValue() == null )
            throw new Exception('Cannot update with null key value.');

        $stmt = self::$mysqli->prepare("UPDATE ". $this->tableName." "
            . "SET custId=?, statusId=?, shipTo=? "
            . "WHERE ".$this->keyName."=?");

        if( ! $stmt )
            throw new Exception(self::$mysqli->error, self::$mysqli->errno);
        
        if( $stmt->bind_param("isisi",
                $this->custId,
                $this->statusId,
                $this->shipTo,
                $this->keyValue))
        {
            $retval = $stmt->execute();
        }
        $stmt->close();

        return $retval;
    }
    // end update_db().
    
    /**
     * Update only the order status in the database with this->statusId. Other
     * fields remain unmodified.
     * 
     * @return boolean
     * @throws Exception upon database error.
     */
    public function db_update_status()
    {
        $retval = false;
        
        if( $this->getKeyValue() == null )
            throw new Exception('Cannot update with null key value.');

        $stmt = self::$mysqli->prepare("UPDATE ". $this->tableName." "
            . "SET statusId=? WHERE ".$this->keyName."=?");

        if( ! $stmt )
            throw new Exception(self::$mysqli->error, self::$mysqli->errno);
        
        if( $stmt->bind_param("ii", $this->statusId, $this->keyValue))
        {
            $retval = $stmt->execute();
        }
        $stmt->close();

        return $retval;
    }
    // end db_update_status().
    
    public function db_update_items()
    {
        throw new Exception('Not implemented yet');
        
        // Update each of the order item values.
        // @TODO: what if the orderItem didn't already exist?
        foreach($this->items as $OItem )
        {
            $OItem->db_update();
        }
    }
    
    /**
     * Inserts a new record into the database with the given value of name.
     * The auto-incremented id is set to this->keyValue.
     * The date of the order gets set to now().
     * 
     * @return boolean
     */
    public function db_insert()
    {
        $retval = false;
        
        $stmt = self::$mysqli->prepare("INSERT INTO ".$this->tableName
            ." ( custId, dateOrdered, statusId, shipTo ) VALUES (?,now(),?,? )");

        if($stmt)
        {
            if( $stmt->bind_param("iis",
                        $this->custId,
                        $this->statusId,
                        $this->shipTo ))
            {
                if($stmt->execute())
                {
                    $this->keyValue = self::$mysqli->insert_id;
                    $retval = true;
                }
            }
            $stmt->close();
        }
        // end if stmt good.
        
        return $retval;
    }
    // end db_insert().
    
    /**
     * Return all Orders optionally having the specified statusId(s).
     * 
     * @param mixed $status
     * Default of null does not filter by statusId.
     * Single integer value searches records with matching statusId.
     * Array of integers searches records with any of the given statusIds.
     * 
     * @return Order[]
     * Returns an array containing Order objects or an empty array if none
     * were found.
     */
    public static function fetch_all($status = null)
    {
        $statusstr = "";
        if(is_array($status))
        {
            $statusstr = "WHERE statusId IN (".implode(',', $status).") ";
        }
        else if($status !== null )
        {
            $statusstr = " WHERE statusId=".(int)$status;
        }
        
        $retval = array();
        $stmt = self::$mysqli->prepare("SELECT orderId FROM Order " . $statusstr );

        // Use an array because mysqli doesn't allow two concurrent open statements.
        $orderIdList = array();
        
        if( $stmt )
        {
            if( $stmt->execute() )
            {
                $stmt->bind_result($id );
                
                while( $stmt->fetch() )
                {
                    $orderIdList[] = $id;
                }
                // done fetching rows.
            }
            // end if execute was good.
                
            // end if bind succeeded.
            $stmt->close();
            
            foreach($orderIdList as $id)
            {
                $Object = new Orders();
                $Object->init_by_key($id);

                $retval[] = $Object;
            }
        }
        // end if stmt good.
        
        return $retval;
    }
    // end fetch_all().
    
    /**
     * Returns the customer object stored in this class.
     * 
     * @return Customer
     */
    public function get_customer()
    {
        return $this->Customer;
    }
    // end get_customer().
    
    /**
     * Mark the order as shipped.
     * 
     * Post-Conditions: If missing was supplied, it was initialized to an
     * empty array and possibly filled with OrderItem objects.
     * 
     * @param string[] $missing If there were any missing items, then
     * this array gets filled with messages describing why.
     * 
     * @return boolean Returns false when quantity available is insufficient or
     * if commit failed.
     * Returns true when quantity available was sufficient and commit succeeded.
     * 
     * @throws Exception Upon any database error, orderId is null, or order
     * is already shipped, exception is thrown.
     */
    public function shipIt(& $missing = array() )
    {
        $retval = false;
        
        if( !is_array($missing))
            $missing = array();
        
        if( $this->keyValue == null )
            throw new Exception('OrderId is null');
        
        if( $this->statusId == self::STATUS_ID_SHIPPED )
            throw new Exception('Already shipped');
        
        /*
         * If all the components are available, the status of the order changes
         *  from "Pending" to "Shipped" and the quantities in the inventory are
         *  decreased. If the components are not available, some error page
         *  listing the missing components is generated and the order remains 
         * "Pending".
         */
        
        // Start a transaction.
        // Load ordered quantities and available qtys.
        //
        //
        // Commented out autocommit(false):
        // "With START TRANSACTION, autocommit remains disabled until you end
        // the transaction with COMMIT or ROLLBACK. The autocommit mode then
        // reverts to its previous state. "
        // https://dev.mysql.com/doc/refman/5.0/en/commit.html
//        self::$mysqli->autocommit(false);
        
//        $fh = fopen('log.txt', 'a');
        $this->begin_transaction();
        
        $doCommit = true;
        try
        {
            // Lookup the available quantity of each item in this order. 
            foreach($this->items as $OrderItem )
            {
                $OrderItem->qty;

                // Lookup the available quantity. (init_by_key does the lookup).
                $Item = new Item();
                $Item->init_by_key($OrderItem->itemId);
                
//                fwrite($fh, print_r($Item,true));
//                fwrite($fh, print_r($OrderItem,true));

                // Compare the available inventory quantity to the desired
                // quantity in the order.
                // If any item has insufficient quantity, then break out
                // of the loop and flag for a rollback.
                if( $Item->qty_available < $OrderItem->qty )
                {
                    $doCommit = false;
//                    break;
                    $missing[] = sprintf('%s needed %d but only %d were available.',
                            $Item->name, $OrderItem->qty, $Item->qty_available);
                }
                else
                {
                    // Update the database: subtract the order quantity from
                    // the available quantity.
                    // This change may be rolled back.
                    $Item->qty_available -= $OrderItem->qty;
                    
//                    fwrite($fh, print_r($Item,true));
//                    fwrite($fh, print_r($OrderItem,true));    
                    
                    
                    
                    $Item->db_update_qty();
                }
                unset($Item);
            }
            // done looking up.
            
            if( $doCommit )
            {
                $this->statusId = Orders::STATUS_ID_SHIPPED;
                $this->db_update_status();
                
                $this->commit();
                $retval = true;
            }
            else
            {
                $this->rollback();
            }
        } catch (Exception $ex)
        {
            $this->rollback();
//            fclose($fh);
            throw $ex;
        }
//        fclose($fh);
        return $retval;
    }
    // end shipIt.
}
// end class Staff.
