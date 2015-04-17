<?php
/* 
 * File: class-Tableset.php
 * Author: Matthew Denninghoff
 * 
 * A class for printing out a formatted HTML table from a 2D array of data.
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


// Charset to output with the CSV header.
const CSV_CHARSET = 'utf-8';

// CSV delimiter character to use in CSV output. 
const CSV_DELIMITER = ',';

// CSV enclosing character to use in CSV output.
const CSV_ENCLOSURE = '"';

class TableSet
{
    const DEFAULT_CSS_CLASS = 'tableset';
    
    /**
     * Number of rows in a good result or 0.
     *
     * @var int
     */
    protected $num_rows;
    
    /**
     * Number of columns in a good result or 0.
     *
     * @var int
     */
    protected $num_cols;
    
    /**
     * Array to hold column names. Array index corresponds to column numbers.
     * With a successful query, each column will have a name value in this array.
     *
     * @var array
     */
    protected $column_names;
    
    /**
     * Array to hold column types. Array index corresponds to column numbers.
     * With a successful query, each column will have a name value in this array.
     *
     * @var array
     */
    protected $column_types;
    
    // Specify width value for the td tag. Not every index is set.
    protected $column_widths;
    
    // Printf format for column output.
    protected $column_formats;
    
    /**
     * A two dimensional array of data. The outside array has rows, and the 
     * inside array has columns.
     *
     * @var array 
     */
    protected $data;
    
    // String to print inside the table caption tag.
    // The default value of null prevents the caption tag from being output.
    public $caption;
    
    // String to print inside the tfoot.
    // The default value of null prevents table footer from being output.
    public $footer;
    
    /**
     * Add an additional class to the table tag; Then you can override the
     * default colors when you use multiple TableSets on a page.
     * @var string 
     */
    public $css_class;
    
    /**
     * Flag. When true, a column with row numbers is output along with the
     * query results.
     *
     * @var boolean
     */
    protected $show_row_numbers;
    
    
    public $csv_filename;
    
    // String names of the different cell data types this class recognizes.
    // Used for css formatting.
    const TYPE_STRING = 'string';
    const TYPE_INT    = 'int';
    const TYPE_REAL   = 'real';
    
    
    const COLUMN_DEFAULT_TYPE = TableSet::TYPE_STRING;
    
    /**
     * Initialize column and row counts to 0. Data is empty.
     */
    public function __construct()
    {
        $this->num_cols = 0;
        $this->num_rows = 0;
        $this->caption = null;
        $this->footer = null;
        $this->column_names = array();
        $this->column_types = array();
        $this->column_widths = array();
        $this->column_widths = array();
        $this->column_formats = array();
        $this->show_row_numbers = true;
        
        $this->data = array();
        
        // Default filename to send to browser upon download.
        $this->csv_filename = 'results.csv';
        $this->css_class = Tableset::DEFAULT_CSS_CLASS;
    }
    // end __construct().
    
                
        
    /**
     * Set the internal data table with $data.
     * Data must be a 2D array with integer indexes.
     * Returns false if $data was of the wrong format.
     * Returns true if $data was accepted and $this->data was set.
     * 
     * Post-Conditions: These fields are modified:
     * data, num_cols, num_rows, column_types, footer.
     * 
     * @param array $data
     * @return boolean
     */
    public function set_data($data )
    {
        $retval = false;
        if( is_array($data))
        {
            $rowcnt = count($data);
            
            if($rowcnt > 0 && isset($data[0]) && is_array($data[0]))
            {
                $this->num_cols = count($data[0]);
                
                $this->num_rows = $rowcnt;
                
                $this->data = $data;

                for($col=0; $col < $this->num_cols; $col++ )
                {
                    // Default to string type.
                    $this->column_types[$col] = self::TYPE_STRING;
                }
                
                $this->footer = $this->num_rows . ' rows';
                
                $retval = true;
            }
            // end if inside array exists.
        }
        // end if data is array.
        
        return $retval;
    }
    // end set_data().
    
    
    
    /**
     * Print HTTP headers that make the browser prompt the user to download
     * the subsequent data as a CSV file.
     * 
     * The headers must be sent before regular output is sent to the browser.
     * 
     */
    public function print_csv_headers()
    {
        // Tell the browser to expect a csv file
        // Note: try application/octet-stream if the browser doesn't try to save the file.
        // It works in Firefox 36 on Mac. MD.
        header('Content-Type: text/csv; charset='.CSV_CHARSET, TRUE);
    
        // Suggest a filename for the browser to use when prompting the user to
        // save.
        header('Content-Disposition: attachment; filename="'.$this->csv_filename.'"');
    }
    // end print_csv_headers().
    
    /**
     * Output the table data as CSV data.
     * 
     * Reference: 
     * http://code.stephenmorley.org/php/creating-downloadable-csv-files/
     * 
     */
    public function print_table_csv()
    {
        // Create a file pointer connected to the output stream.
        $output = fopen('php://output', 'w');

        // Print the column names.
        fputcsv($output, $this->column_names, CSV_DELIMITER, CSV_ENCLOSURE);

        // Iterate over each data row.
        for($row=0; $row < $this->num_rows; $row++)
        {
            fputcsv($output, $this->data[$row], CSV_DELIMITER, CSV_ENCLOSURE);
        }
        // done iterating over each row.

        // necessary? md.
        fclose($output);
    }
    // end print_table_csv().
    
    /**
     * Print HTML table of the internal $data array out to the
     * browser. If a caption is specified, then the table uses that caption.
     * Likewise, a specified footer is output.
     * 
     */
    public function print_table_html()
    {
        // Print the table tag and use the class string so that CSS styles
        // can apply to the table.
        echo '<table class="'.$this->css_class. '">'."\n";
        
        // The caption tag is displayed above the column headers.
        echo ($this->caption ? ' <caption>'.$this->caption.'</caption>' . "\n" : '')
        . " <thead><tr>\n";

        // Optionally, print the column heading for row numbers.
        if( $this->show_row_numbers)
        {
            echo "  <th>&nbsp;</th>\n";
        }

        // Print column headers for each column.
        for($col=0; $col < $this->num_cols; $col++)
        {
            echo '  <th';
            if( isset($this->column_widths[$col]))
            {
                echo ' width="'.$this->column_widths[$col].'"';
            }
            echo '>'.$this->column_names[$col]."</th>\n";
        }
        // done printing column headers.

        echo " </tr></thead>\n";

        // Print the table footer, which appears below the table rows.
        // The footer is one TD cell that spans the width of the table.
        if( $this->footer )
        {
            $span = $this->num_cols;
            if( $this->show_row_numbers ) $span += 1;
            echo ' <tfoot><tr><td colspan="'.$span.'">'.$this->footer.'</td></tr></tfoot>'."\n";
        }

        echo " <tbody>\n";

        // Iterate over every row in the table.
        $rowCnt = 1;
        for($row=0; $row < $this->num_rows; $row++)
        {
            // Print a class attribute so that odd rows may have different
            // colors.
            $oddeven_class = '';
            if( ($row % 2) == 1 )
            {
                $oddeven_class .= ' class="odd"';
            }
            
            echo "  <tr$oddeven_class>\n";

            // Print the row number and increment the counter.
            if( $this->show_row_numbers)
            {
                echo '   <td class="rowNo">'. $rowCnt++ . "</td>\n";
            }
            
            // Print each column value in this row.
            for($col=0; $col < $this->num_cols; $col++)
            {
                // The normal output is the raw data.
                $outstr = $this->data[$row][$col];
                
                // If there is a format string, call sprintf() using the format.
                if( isset($this->column_formats[$col]))
                {
                    $outstr = sprintf($this->column_formats[$col], $this->data[$row][$col]);
                }
                
                echo '   <td class="'. $this->column_types[$col] . '">'.$outstr . "</td>\n";
            }
            // done printing each column in this row.

            echo "  </tr>\n";
        }
        // done fetching each result row.

        echo "</tbody></table>\n";
    }
    // end print_table_string().
    
    /**
     * Sets a column name. Returns false if the column number was out of bounds.
     * 
     * @param int $colNo
     * @param string $name
     * @return boolean
     */
    public function set_column_name($colNo, $name)
    {
        if( ! $this->column_exists($colNo))
            return false;
        
        $this->column_names[$colNo] = $name;
        
        return true;
    }
    // end set_column_name().
    
    /**
     * Replace all column names with values in $arr.
     * 
     * @param array $arr
     * @return boolean
     */
    public function set_column_names($arr)
    {
        if( !is_array($arr))
            return false;
        
        if( count($arr) != $this->num_cols)
            return false;
        
        $this->column_names = $arr;
        
        return true;
    }
    // end set_column_names().
    
    /**
     * Sets a column type. Returns false if the column number was out of bounds.
     * Type should be 
     * 
     * @param int $colNo
     * @param string $type
     * @return boolean
     */
    public function set_column_type($colNo, $type)
    {
        if( ! $this->column_exists($colNo))
            return false;
        
        $this->column_types[$colNo] = $type;
        
        return true;
    }
    // end set_column_name().
    
    /**
     * Set all column types to the values in $types.
     * Types should be TableSet::TYPE_INT, TYPE_REAL, or TYPE_STRING.
     * 
     * @param array $types
     * @return boolean
     */
    public function set_column_types( $types )
    {
        if( !is_array($types) || count($types) != $this->num_cols)
            return false;
        
        $this->column_types = $types;
        return true;
    }
    // end set_column_types().
    
    /**
     * Set a single column's HTML output format string. If a column has a 
     * format, then the format goes into a call to sprintf() on the data.
     * This is useful for specifying the number of decimal places in floats.
     * 
     * @param int $colNo
     * @param string $format
     * @return boolean
     */
    public function set_column_format($colNo, $format)
    {
        if( ! $this->column_exists($colNo))
            return false;
        
        $this->column_formats[$colNo] = $format;
        
        return true;
    }
    // set_column_format().
    
    /**
     * Set all column formats to be used in HTML output. 
     * 
     * @param array $formats
     * Should be an array of length = number of data columns.
     * Array values should be format strings used for sprintf().
     * 
     * @return boolean
     */
    public function set_column_formats($formats)
    {
        if( !is_array($formats) || count($formats) != $this->num_cols)
            return false;
        
        $this->column_formats = $formats;
        return true;
    }
    // end set_column_formats().
    
    /**
     * Sets a column width. Returns false if the column number was out of bounds.
     * Width goes into the TH tag and should be of the form "10%" or "200px".
     * 
     * @param int $colNo
     * @param string $val
     * @return boolean
     */
    public function set_column_width($colNo, $val)
    {
        if( ! $this->column_exists($colNo))
            return false;
        
        $this->column_widths[$colNo] = $val;
        
        return true;
    }
    // end set_column_name().
    
    /**
     * Replace cell data for the specified column. The $arr argument should be
     * an associative array. For each cell value, if the value matches a key
     * in $arr, then the corresponding value in $arr[matching_key] replaces
     * the cell data.
     * 
     * The original column values are returned as an array.
     * (That allows you to display descriptive text but still use numeric
     * data in href links, for instance.)
     * 
     * @param int $colNo
     * @param array $arr
     * 
     * @return mixed
     * Returns false for invalid arguments. Returns the original column values
     * otherwise.
     */
    public function replace_column_values($colNo, $arr )
    {
        if( ! $this->column_exists($colNo))
            return false;
       
        if(! is_array($arr))
            return false;
        
        $old_values = array();
        
        // Replace column values in each row.
        for($row=0; $row < $this->num_rows; $row++)
        {
            $val = $this->data[$row][$colNo];
            
            // Put the backed-up value into old_values.
            $old_values[$colNo] = $val;
            
            // See if there exists a mapping to swap out the cell
            // value with a more descriptive value.
            if(  isset($arr[$val]) )
            {
                $this->data[$row][$colNo] = $arr[$val];
            }
        }
        // done iterating over each row.
        
        return $old_values;
    }
    // end replace_column_values().
    
    /**
     * Returns false if the column number was out of bounds; true otherwise.
     * 
     * @param int $colNo
     * @return boolean
     */
    protected function column_exists($colNo)
    {
        if( $colNo < 0 || $colNo >= $this->num_cols)
            return false;
        return true;
    }
    // end column_exists().
    
    /**
     * Returns false if the row number was out of bounds; true otherwise.
     * 
     * @param int $rowNo
     * @return boolean
     */
    protected function row_exists($rowNo)
    {
        if( $rowNo < 0 || $rowNo >= $this->num_rows)
            return false;
        return true;
    }
    // end row_exists().
    
    /**
     * Set the flag to show or hide the column containing row numbers.
     * 
     * @param boolean $show
     */
    public function show_row_numbers($show)
    {
        if( $show )
        {
            $this->show_row_numbers = true;
        }
        else
        {
            $this->show_row_numbers = false;
        }
    }
    // end showhide_row_numbers().
    
    /**
     * Return the number of rows in the table data.
     * 
     * @return int
     */
    public function get_num_rows()
    {
        return $this->num_rows;
    }
    // end get_num_rows().
    
    /**
     * Return the number of columns in the table data.
     * 
     * @return int
     */
    public function get_num_cols()
    {
        return $this->num_cols;
    }
    // end get_num_cols().
    
    /**
     * Returns the column name (i.e. header) for a column number.
     * 
     * @param int $colNo
     * @return mixed
     * Returns false if column doesn't exist, a string containing the name if
     * it does exist.
     */
    public function get_col_name($colNo)
    {
        if( ! $this->column_exists($colNo))
            return false;
        
        return $this->column_names[$colNo];
    }
    // end get_col_name().
    
    /**
     * Add an empty column to the end of the column set.
     * 
     * @param string $name
     */
    public function add_column( $name )
    {
        $newColNo = $this->num_cols;
        $this->num_cols += 1;
        
        // Add empty data to the new column.
        for($row=0; $row < $this->num_rows; $row++)
        {
            $this->data[$row][$newColNo] = null;
        }
        
        $this->column_types[$newColNo] = self::TYPE_STRING;
        $this->column_names[$newColNo] = $name;
    }
    // done add_column().
    
    /**
     * Return the value at the specified cell, or null if the cell doesn't
     * exist.
     * 
     * @param int $rowNo
     * @param int $colNo
     * 
     * @return mixed
     */
    public function get_value_at($rowNo, $colNo )
    {
        if( !$this->column_exists($colNo) || ! $this->row_exists($rowNo))
            return null;
        
        return $this->data[$rowNo][$colNo];
    }
    // end get_value_at().
    
    /**
     * Set the cell value at row, col. Returns the original value being
     * replaced.
     * 
     * @param int $rowNo
     * @param int $colNo
     * @param mixed $value
     * 
     * @return mixed
     * Returns false if arguments were invalid.
     * Returns the original value that has been replaced.
     */
    public function set_value_at($rowNo, $colNo, $value )
    {
        if( !$this->column_exists($colNo) || ! $this->row_exists($rowNo))
            return false;
        
        $old_val = $this->data[$rowNo][$colNo];
        
        $this->data[$rowNo][$colNo] = $value;
        
        return $old_val;
    }
    // end set_value_at().
}
// end class TableSet.
