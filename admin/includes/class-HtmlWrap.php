<?php
/* 
 * Dumb wrapper for printing common html functions. It's in a class in case
 * I want to tweak settings in the future.
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

class HTMLWrap
{

    /**
     * Print a html select tag with the given values. 
     * 
     * @param string $name
     * @param array $values Associative array. Keys go into the option value 
     * attribute. Array values are the text shown.
     * 
     * @param string $label
     * @param mixed $selected
     * Pass a string to select a single value.
     * Pass an array to select multiple.
     * 
     * @param boolean $multiple
     * Set to true to allow multiple selections.
     * Defaults to false.
     * 
     * @param int $size
     * Number of visible options. Cannot be larger than count($values).
     * Defaults to 1.
     * 
     * @return string
     */
    public function print_select( $name, $values, $label, $selected = '', $multiple = false, $size = 1 )
    {
        if( !is_array($values))
            return '';

        echo '<label for="'.$name.'">'.$label.'</label> <select name="'.$name.'"'
                . ($multiple ? ' multiple' : '');

        if( $size > count($values) )
            $size = count($values);

        echo ' size="'. (int)$size . '">';

//        die( print_r($values,true) . var_dump($selected));
        
        foreach( $values as $key => $val )
        {
            $sel = '';
            if($multiple && is_array($selected) && in_array($key, $selected))
            {
                $sel = ' selected="selected"';
            }
            elseif( $key == $selected)
            {
                $sel = ' selected="selected"';
            }
            echo '  <option value="'.$key.'"'.$sel.'>'.$val."</option>\n";
        }
        // done iterating over values.

        echo "</select></label>\n";
    }
    // end print_select().

    /**
     * Print an input tag inside of a label tag.
     * 
     * @param string $name For the name attribute of the input.
     * @param mixed $value The value of the text field.
     * @param string $label The Label to the left of the input.
     */
    public function print_textbox( $name, $value, $label )
    {
        echo '<label for="'.$name.'">'.$label.'</label> <input type="text" name="'.$name.'" value="'.$value.'" /></label>'."\n";
    }
    // end print_textbox().
    
    public function print_checkboxes( $name, $values, $selected = '', $linebreaks = true )
    {
        if( !is_array($values))
        {
            return '';
        }
        
        foreach( $values as $key => $val )
        {
            $chk = '';
            if(is_array($selected) && in_array($key, $selected))
            {
                $chk = ' checked';
            }

            echo '<input type="checkbox" name="'.$name.'[]" value="'.$key.'"'.$chk.'/>'."$val\n";
            if( $linebreaks ) echo "<br>";
        }
    }
    // end print_checkboxes().
    
    /**
     * Print a single checkbox and check it if $value equals $selected
     * 
     * Note: placing the input tag inside of the label tag allows a user
     * to click on the label to click on the box.
     * 
     * @param string $name name attribute for input.
     * @param mixed $value value attribute for input.
     * @param string $label label to the right of checkbox.
     * @param string $selected compared to value to decide if checked.
     */
    public function print_checkbox( $name, $value, $label, $selected = '')
    {
        $chk = $value == $selected ? ' checked="checked"' : '';
        echo '<label><input type="checkbox" name="'.$name.'" value="'.$value.'"'.$chk.'/>'.$label.'</label>'."\n";
    }
}
// end class HTMLWrap