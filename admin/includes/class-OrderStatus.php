<?php
/* 
 * class-OrderStatus.php
 * 
 * Defines the OrderStatus class.
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

class OrderStatus extends DBEntity
{    
    public $name;
     
    const SQL_COLUMN_LIST = " statusId, name ";
    
    
    
    public function __construct( $id = null )
    {
        parent::__construct($id);
        $this->tableName = 'OrderStatus';
        $this->keyName = 'statusId';
        
        $this->name = null;
    }
  
    /**
     * Set the class member values by fetching values from the database.
     * If the key was not found, then the class member fields remain null.
     * 
     * @param string $value
     * The table key to search the database for.
     * 
     * @return mixed
     * Returns true if data was fetched.
     * Returns false if there was an error.
     * Returns null if no data was found.
     */
    public function init_by_key($value)
    {
        $retval = false;
        $stmt = self::$mysqli->prepare("SELECT " . self::SQL_COLUMN_LIST . "FROM ".$this->tableName." WHERE ".$this->keyName." = ? ");
        
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
            
            $stmt->bind_result($this->keyValue, $this->name );
            $retval = $stmt->fetch();
            
        } catch (Exception $ex) {
            $stmt->close();
            throw $ex;
        }
        // end catch exception.
        $stmt->close();
                
        return $retval;
    }
    // end find_by_key().
    
    /**
     * Update a record matching this record's key with this class member's values.
     * 
     * @return boolean
     */
    public function db_update()
    {
        $retval = false;
        
        if( $this->getKeyValue() != null )
        {
            $stmt = self::$mysqli->prepare("UPDATE ". $this->tableName." "
                . "SET name=? "
                . "WHERE ".$this->keyName."=?");

            if($stmt)
            {
                if( $stmt->bind_param("si", $this->name, $this->keyValue))
                {
                    $retval = $stmt->execute();
                }
                $stmt->close();
            }
            // end if stmt good.
        }
        // end if not null.
        
        return $retval;
    }
    // end update_db().
    
    /**
     * Inserts a new record into the database with the given value of name.
     * The auto-incremented staffId is set to this->keyValue.
     * 
     * @return boolean
     */
    public function db_insert()
    {
        $retval = false;
        
        $stmt = self::$mysqli->prepare("INSERT INTO ".$this->tableName." (name)  VALUES (?)");

        if($stmt)
        {
            if( $stmt->bind_param("s", $this->name ))
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
}
// end class Staff.