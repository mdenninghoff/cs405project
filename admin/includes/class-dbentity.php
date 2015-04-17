<?php
/* 
 * File: class-dbentity.php
 * 
 * Superclass for database entity classes.
 * A variable named $mysqli must contain a database connection.
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

abstract class DBEntity
{
    const RESULT_ASSOC_ARRAY = 0;
    const RESULT_OBJECTS = 1;
    
    /**
     * The mysqli connection object.
     * This is static so that there is only one reference to it amongst
     * all the subclasses.
     *
     * @var mysqli
     */
    static public $mysqli;
    
    /**
     * The name of the table.
     *
     * @var string 
     */
    protected $tableName;
    
    /**
     * The string name of the key.
     *
     * @var string
     */
    protected $keyName;
    
    /**
     * The value of a key.
     *
     * @var int
     */
    protected $keyValue;
    
    /**
     * Array of column names. Do not include the key column.
     *
     * @var array 
     */
//    protected $columnNames;
    
    
    /**
     * Array of chars; should be one of:
     * MYSQLI_BIND_TYPE_INT, _DOUBLE, _BLOB, or _STRING (i, d, b, s).
     *
     * @var array
     */
//    protected $columnBindTypes;
    
    /**
     * Find a 
     */
    public abstract function init_by_key($keyValue);

    public abstract function db_update();
    public abstract function db_insert();
    

    
    /**
     * 
     * @param int $id Use to set this keyValue in the constructor.
     * 
     */
    public function __construct($id = null)
    {
        if( $id != null )
        {
            $this->keyValue = $id;
        }
        else
        {
            $this->keyValue = null;
        }
    }
    // end constructor.
    
    /**
     * Returns a null value if the key was not set.
     * Should return an integer if the key was set.
     * 
     * @return mixed
     */
    public function getKeyValue()
    {
        return $this->keyValue;
    }
    // end getKeyValue().
    
    /**
     * Start a transaction on $mysqli.
     * PHP >= 5.5 have mysqli->begin_transaction() method, but our PHP is 5.3, MD.
     * 
     * @throws Exception
     */
    public function begin_transaction()
    {
        if( ! self::$mysqli->query("START TRANSACTION"))
            throw new Exception('Failed to start transaction: '.self::$mysqli->error);
    }
    
    /**
     * Rollback the active transaction on $mysqli.
     * 
     * @throws Exception
     */
    public function rollback()
    {
        if( ! self::$mysqli->query("ROLLBACK"))
            throw new Exception('Failed to rollback transaction: '.self::$mysqli->error);
    }
    
    /**
     * Commit the active transaction on $mysqli.
     * 
     * @throws Exception
     */
    public function commit()
    {
        if( ! self::$mysqli->query("COMMIT"))
            throw new Exception('Failed to commit transaction: '.self::$mysqli->error);
    }
    
    /**
     * 
     * @param mysqli $mysqli
     */
    static public function setDatabase($mysqli)
    {
        if(is_a($mysqli, 'mysqli'))
            DBEntity::$mysqli = $mysqli;
    }
    
    /**
     * Set the key value. 
     * 
     * @param int $val
     */
//    public function setKeyValue($val)
//    {
//        $this->keyValue = $val;
//    }
    
    
    /**
     * Update a record matching this record's key with this class member's values.
     * 
     * @return boolean
     */
//    public function db_update()
//    {
//        $retval = false;
//        
//        if( $this->getKeyValue() != null )
//        {
//            $query = "UPDATE ". $this->tableName." SET ";
//            
//            $colstr = "";
//            foreach( $this->columnNames as $col)
//            {
//                $colstr .= ",$col = ?";
//            }
//            // Remove the first comma.
//            $colstr = substr($colstr, 1);
//            
//            $query .= $colstr;
//            
//            
//            
//            $stmt = self::$mysqli->prepare();
//            
//            
////                . "SET name=?, isManager=?, password=?, ".self::COL_SESSIONID."=? "
////                . "WHERE ".$this->keyName."=?");
//
//            if($stmt)
//            {
//                if( $stmt->bind_param("sissi", $this->name, $this->isManager,
//                        $this->password, $this->sessionId, $this->keyValue))
//                {
//                    $retval = $stmt->execute();
//                }
//                $stmt->close();
//            }
//            // end if stmt good.
//        }
//        // end if not null.
//        
//        return $retval;
//    }
    // end update_db().
    
}
// end class DBEntity
