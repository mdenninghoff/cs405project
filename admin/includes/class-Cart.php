<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-Cart
 *
 * @author Paul
 */
class Cart extends DBEntity
{
//put your code here
    //vars
    public $itemId = null;
    public $qty = null;
    
    const SQL_COLUMN_LIST = " custId, itemId, qty ";
    const COL_SESSIONID = ' sessionId';
    
    public function __construct($id = null) 
    {
        parent::__construct($id);
        $this->tableName = 'Cart';
        $this->keyName = 'custId';
        //foreign keys need to be used
    }
    public function setCustId($cid)
    {
        $this->keyValue= $cid;
        ///return $this;
    }
    public function init_by_key($keyValue)
    {
        $retval = false;
        $stmt = self::$mysql->prepare("SELECT " . self::SQL_COLUMN_LIST . "FROM ".$this->tableName." WHERE ".$this->keyName." = ? ");
        
        if ( ! $stmt )
               throw new Exception (self::$mysqli->error, self::$mysqli->errno );
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
                    $this->itemId,
                    $this->qty);
            $retval = $stmt->fetch();
            
        } catch (Exception $ex) {
            $stmt->close();
            throw $ex;
        }
        $stmt->close();
        
        return $retval;
    }
    public function db_update() {
        $retval = false;
        
        if( $this->getKeyValue() != null )
        {
            $stmt = self::$mysqli->prepare("UPDATE ". $this->tableName." "
                . "SET qty=? "     
                . "WHERE custId=? AND itemId=? ");
            if($stmt)
            {
                if( $stmt->bind_param("iii", 
                        $this->qty,
                        $this->keyValue,
                        $this->itemId))
                {
                    $retval = $stmt->execute();
                }
                $stmt->close();
            }
        }
        return $retval;
    }
    public function db_insert() {
        $retval = false;
        if( $this->keyValue != null && $this->itemId != null )
        {
            $stmt = self::$mysqli->prepare("INSERT INTO ". $this->tableName
                    ." ( custId, itemId, qty ) VALUES (?,?,?)");
            if($stmt)
            {
                if( $stmt->bind_param("iii",
                        $this->keyValue,
                        $this->itemId,
                        $this->qty))
                {
                    $retval = $stmt->execute();
                }
                $stmt->close();
            }
        }
        
        return $retval;
    }
}
