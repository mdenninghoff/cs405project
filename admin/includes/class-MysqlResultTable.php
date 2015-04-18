<?php
/*
 * File: class-MysqlResultTable.php
 * Author: Matthew Denninghoff.
 * 
 * This class gives simple, standard way to print mysql query results as
 * a html table or as CSV text.
 * 
 * Copyright 2015 Fishback Research and Management.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class MysqlResultTable extends TableSet
{
    /**
     * This holds the mysql query result object.
     *
     * @var mixed
     */
    protected $query_result;
    
    /**
     * If the query failed, this holds the string result of mysql_error().
     *
     * @var string
     */
    protected $error_message;
    
    /**
     * Holds the raw SQL query string that was used.
     *
     * @var string
     */
    protected $query_string;

    protected $mysqli;
    

    /**
     * A mapping of MYSQLI column types defined as constants in the mysqli
     * php extension. Those are mapped to TableSet column types.
     * These help in determining the default method to use for
     * rendering a table cell.
     *
     * @var array<int,string> 
     */
    public static $TYPEMAP = array(
        MYSQLI_TYPE_DATE => self::TYPE_STRING,
        MYSQLI_TYPE_DATETIME => self::TYPE_STRING,
        MYSQLI_TYPE_TIMESTAMP => self::TYPE_STRING,
        
        MYSQLI_TYPE_INT24 => self::TYPE_INT,
        MYSQLI_TYPE_LONG => self::TYPE_INT,
        MYSQLI_TYPE_LONGLONG => self::TYPE_INT,
        
        MYSQLI_TYPE_FLOAT => self::TYPE_REAL,
        MYSQLI_TYPE_DECIMAL => self::TYPE_REAL,
        MYSQLI_TYPE_DOUBLE => self::TYPE_REAL,
        MYSQLI_TYPE_NEWDECIMAL => self::TYPE_REAL,
        
        MYSQLI_TYPE_BIT => self::TYPE_INT,
        
        MYSQLI_TYPE_STRING => self::TYPE_STRING,
        MYSQLI_TYPE_VAR_STRING => self::TYPE_STRING,
        MYSQLI_TYPE_CHAR => self::TYPE_STRING,
        
        MYSQLI_TYPE_NULL => self::TYPE_STRING
        );
    
    /**
     * Class constructor.
     * @param mysqli $mysqli A connected instance of the mysqli class.
     */
    public function __construct($mysqli)
    {
        parent::__construct();
        
        $this->query_result = false;
        $this->error_message = null;
        $this->query_string = '';
        $this->mysqli = $mysqli;
    }
    // end __construct().
    
    /**
     * Execute a query and store the results as a 2D array in this->data.
     * 
     * Post-Conditions: These fields are modified:
     * data, num_cols, num_rows, column_types, footer, column_names,
     * query_string.
     * 
     * Upon error, false is returned, and error_message is set.
     * 
     * @param string $queryString
     * 
     * @return boolean Returns false if the query failed, true otherwise.
     */
    public function executeQuery($queryString)
    {
        $this->query_string = $queryString;
        
        $query_rsc = $this->mysqli->query($this->query_string);

        if( ! $query_rsc )
        {
            $this->error_message = $this->mysqli->error;
            return false;
        }
        
        $this->data = array();

        //
        //  Handle good query result.
        //
        $this->num_cols = $query_rsc->field_count;
        $this->num_rows = $query_rsc->num_rows;

        // Get the field names and types for each column.
        // These values may be overridden later.
        for($col=0; $col < $this->num_cols; $col++)
        {
            $field = $query_rsc->fetch_field_direct($col);
            
            $this->column_names[$col] = $field->name;
            
            if( isset(MysqlResultTable::$TYPEMAP[$field->type]))
            {
                $this->column_types[$col] = MysqlResultTable::$TYPEMAP[$field->type];
            }
            else
            {
                // Default to string type.
                $this->column_types[$col] = self::TYPE_STRING;
            }
        }
        // done fetching each column's info.
        
        // Fetch the result data.
        while( $row = $query_rsc->fetch_row())
        {
            $this->data[] = $row;
        }
        
        // Set a default footer string: the number of rows.
        $this->footer = $this->num_rows . ' rows';

        // done handling good query result.
        return true;
    }
    // end executeQuery().
    
    /**
     * Return the string stored as the query the last execute() ran.
     * 
     * @return string
     */
    public function get_query_string()
    {
        return $this->query_string;
    }
    
    /**
     * Return the error message stored by the last bad query.
     * 
     * @return string
     */
    public function get_error_message()
    {
        return $this->error_message;
    }
}
// end class MysqlResultTable.
