<?php
/* 
 * class-staff.php
 * 
 * Defines the Staff class.
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

class Staff extends DBEntity
{
    /**
     *
     * @var string
     */
    public $name;
    
    /**
     *
     * @var boolean 
     */
    public $isManager;
    
    /**
     *
     * @var string
     */
    public $password;
    
    /**
     *
     * @var string
     */
    public $sessionId;
    
    const SQL_COLUMN_LIST = " staffId, name, isManager, password, sessionId ";
    const COL_SESSIONID = 'sessionId';
    
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'Staff';
        $this->keyName = 'staffId';
        
        $this->name = null;
        $this->isManager = null;
        $this->password = null;
        $this->sessionId = null;
    }
  
    /**
     * Set the class member values by fetching values from the database.
     * If the key was not found, then the class member fields remain null.
     * 
     * @param string $value
     * The sessionId to search the database for.
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
        
        if( ! $stmt )
            throw new Exception (self::$mysqli->error, self::$mysqli->errno );
        
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
            
            $stmt->bind_result($this->keyValue, $this->name, $this->isManager, $this->password, $this->sessionId );
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
    
    
    public function init_by_sessionId($sessid)
    {
        $retval = false;
        $stmt = self::$mysqli->prepare("SELECT " . self::SQL_COLUMN_LIST
                . " FROM ".$this->tableName
                . " WHERE ".self::COL_SESSIONID." = ?");
        if( $stmt )
        {
            if( $stmt->bind_param(MYSQLI_BIND_TYPE_STRING, $sessid))
            {
                if( $stmt->execute() )
                {        
                    $stmt->bind_result($this->keyValue, $this->name,
                            $this->isManager, $this->password, $this->sessionId );

                    $stmt->fetch();
                    $retval = true;
                }
                // end if execute was good.
            }
            // end if bind succeeded.
            $stmt->close();
        }
        // end if stmt good.
        
        return $retval;
    }
    // end init_by_sessionId().
    
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
                . "SET name=?, isManager=?, password=?, ".self::COL_SESSIONID."=? "
                . "WHERE ".$this->keyName."=?");

            if($stmt)
            {
                if( $stmt->bind_param("sissi", $this->name, $this->isManager,
                        $this->password, $this->sessionId, $this->keyValue))
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
     * Inserts a new record into the database with the given values of name,
     * isManager, password, and sessionId.
     * The auto-incremented staffId is set to this->keyValue.
     * 
     * @return boolean
     */
    public function db_insert()
    {
        $retval = false;
        
        $stmt = self::$mysqli->prepare("INSERT INTO ".$this->tableName." (name, isManager, password, sessionId) "
            . "VALUES (?,?,?,?)");

        if($stmt)
        {
            if( $stmt->bind_param("sissi", $this->name, $this->isManager, $this->password, $this->sessionId ))
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