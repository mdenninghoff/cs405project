<?php
/* 
 * File: class-TablesetDefaultCSS.php
 * Author: Matthew Denninghoff
 * 
 * A class for setting the default CSS formatting of TableSet objects.
 * Using this class keeps the styles between tables identical.
 * 
 * The css_class member is set to TableSet::DEFAULT_CSS_CLASS. Therefore,
 * that class must be loaded before an instance of this class is constructed.
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

class TablesetDefaultCSS
{
    protected $css_table;
    protected $css_caption = null;
    protected $css_th;
    protected $css_td;
    protected $css_td_odd;
    protected $css_td_int;
    protected $css_td_str;
    protected $css_td_real;
    protected $css_footer;
    protected $tr_hover;

    public $css_class;
    
    /**
     * Initialize arrays and setup default style values.
     * The css_class member is set to TableSet::DEFAULT_CSS_CLASS. Therefore,
     * that class must be loaded before an instance of this class is constructed.
     * 
     */
    public function __construct()
    {
        $this->css_table = array();
        $this->css_caption = array();
        $this->css_td = array();
        $this->css_th = array();
        $this->css_footer = array();
        $this->tr_hover = array();
                
        $this->css_table['font-family'] = 'courier new, courier,monospace';
        $this->css_table['font-size'] = '12pt';
        $this->css_table['border-spacing'] = '0px';
        $this->css_table['border-left'] = 'solid 1px #777';
        $this->css_table['border-bottom'] = 'solid 1px #777';
        $this->css_table['background-color'] = '#999';
        
        $this->css_caption['font-family'] = 'arial';
        $this->css_caption['background-color'] = '#333';

        $this->css_th['font-family'] = 'arial';
        $this->css_th['background-color'] = '#333';
        $this->css_th['padding'] = '0px 10px;';
        $this->css_th['border-style'] = 'solid';
        $this->css_th['border-width'] = '1px 1px 0px 0px';
        $this->css_th['border-color'] = '#777';
        
        $this->css_td['padding'] = '0px 10px;';
        $this->css_td['border-style'] = 'solid';
        $this->css_td['border-width'] = '1px 1px 0px 0px';
        $this->css_td['border-color'] = '#777';
        
        $this->css_td_int = array('text-align' => 'right');
        $this->css_td_real = array('text-align' => 'right');
        $this->css_td_str = null;
        
        $this->css_td_odd = array('background-color' => '#ccc');
        
        $this->css_footer['text-align'] = 'right';
        
        $this->tr_hover['background-color'] = 'white';
        
        $this->css_class = Tableset::DEFAULT_CSS_CLASS;
        
//        $this->css[self::CSS_SEL_CLASSTABLESET.' p.rightDim'] = 'color:#aaa; margin:6px 10px 30px; text-align: right; width:69%;';
    } 
    // end set_default_css().
    
    

    /**
     * Set a CSS value for the main table.tableset.
     * 
     * @param string $key
     * @param string $val
     */
    public function set_css_table_value( $key, $val)
    {
        $this->css_table[$key] = $val;
    }
    // end set_css_table_value().
    
    public function set_css_td_value( $key, $val)
    {
        $this->css_td[$key] = $val;
    }
    
    public function set_css_th_value( $key, $val)
    {
        $this->css_th[$key] = $val;
    }
    
    public function set_css_tdint_value( $key, $val)
    {
        $this->css_td_int[$key] = $val;
    }
    
    public function set_css_tdreal_value( $key, $val)
    {
        $this->css_td_real[$key] = $val;
    }
    
    public function set_css_tdstr_value( $key, $val)
    {
        $this->css_td_str[$key] = $val;
    }
    
    public function set_css_caption_value( $key, $val)
    {
        $this->css_caption[$key] = $val;
    }
    
    public function set_css_footer_value($key, $val)
    {
        if( $val == null && isset($this->css_footer[$key]) )
        {
            unset($this->css_footer[$key]);
        }
        else
        {
            $this->css_footer[$key] = $val;
        }
    }
    
    public function set_css_tdOdd_value($key, $val)
    {
        if( $val == null && isset($this->css_td_odd[$key]) )
        {
            unset($this->css_td_odd[$key]);
        }
        else
        {
            $this->css_td_odd[$key] = $val;
        }
    }
    
    public function set_tr_hover_value($key, $val)
    {
        if( $val == null && isset($this->tr_hover[$key]) )
        {
            unset($this->tr_hover[$key]);
        }
        else
        {
            $this->tr_hover[$key] = $val;
        }
    }
    
    /**
     * Print the CSS defined in $this->css_table and other css fields.
     * This output should be surrounded with the style tag.
     * 
     */
    public function print_css()
    {
        echo "/* Start TableSet css. */\n";
        
        // Print the main table style.
        if( count($this->css_table) > 0 )
        {
            echo 'table.'.$this->css_class . '{';
            foreach($this->css_table as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print the style for TH.
        if( count($this->css_th) > 0 )
        {
            echo 'table.'.$this->css_class . ' th {';
            foreach($this->css_th as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print the style for TD.
        if( count($this->css_td) > 0 )
        {
            echo 'table.'.$this->css_class . ' td {';
            foreach($this->css_td as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        
        // Print the style for caption.
        if( count($this->css_caption) > 0 )
        {
            echo 'table.'.$this->css_class . ' caption {';
            foreach($this->css_caption as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        
        // Print the style for td.int .
        if( count($this->css_td_int) > 0 )
        {
            echo 'table.'.$this->css_class . ' td.'.Tableset::TYPE_INT.' {';
            foreach($this->css_td_int as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";   
        }
        
        // Print the style for td.real .
        if( count($this->css_td_real) > 0 )
        {
            echo 'table.'.$this->css_class . ' td.'.Tableset::TYPE_REAL.' {';
            foreach($this->css_td_real as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print the style for td.str .
        if( count($this->css_td_str) > 0 )
        {
            echo 'table.'.$this->css_class . ' td.'.Tableset::TYPE_STRING.' {';
            foreach($this->css_td_str as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print the footer style.
        if( count($this->css_footer) > 0 )
        {
            echo 'table.'.$this->css_class . ' tfoot td {';
            foreach($this->css_footer as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print style for cells in odd-rows.
        if( count($this->css_td_odd) > 0 )
        {
            echo 'table.'.$this->css_class . ' tr.odd td {';
            foreach($this->css_td_odd as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo "}\n";
        }
        
        // Print style for a row when the mouse pointer is hovering.
        if( count($this->tr_hover) > 0 )
        {
            echo 'table.'.$this->css_class.' tbody tr:hover td {';
            foreach($this->css_td_odd as $key => $val )
            {
                echo $key . ':' . $val . ';';
            }
            echo '}'."\n";
        }
        
        
        
        echo "/* end TableSet css. */\n";
    }
    // end print_css().
    
}
// end class TableSetCSS.
