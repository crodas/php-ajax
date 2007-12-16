<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by Sixdegrees                                 *
*   cesar@sixdegrees.com.br                                               *
*   "Working with freedom"                                                *
*   http://www.sixdegrees.com.br                                          *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/

/**
 *  Ajax Read
 *
 *  Transform the input required into javascript code.
 *
 *  @access private
 */
function aread($p) {
    $t = & $GLOBALS[AJAX_INPUT];
    $t .= "\tinput[i] = args && args[i] ? {name:'$p',value:args[i]} : {name:'$p',source:'$p'};\n";
    $t .= "\ti++;\n";
}

/**
 *  Ajax Print
 *
 *  Prints the result in a HTML object in the client
 *  side.
 *
 *  @param string $dst HTML object where print
 *  @param string $txt Text to print
 */
function aprint($dst,$txt,$override=true) {
    $v = & $GLOBALS[AJAX_PRINT][$dst];
    if ( !isset($v['text']) )   $v['text']='';
    $v['text'] .= $txt;
    $v['override'] =  $override;
}

/**
 *  Show a message box in the client.
 *
 *  @param string $text String to show
 */
function alert($txt) {
     $v = & $GLOBALS[AJAX_ALERT];
     $v[] = $txt;
}

/**
 *  Ajax Hide
 *
 *  Hide a HTML object.
 *
 *  @param string $obj Object "id".
 */
function ahide($obj) {
   $v  = & $GLOBALS[AJAX_SHOW_HIDE];
   $v[]= "ahide('$obj');";

}

/**
 *  Ajax Show
 *
 *  Show a HTML object.
 *
 *  @param string $obj Object "id".
 */
function ashow($obj) {
    $v  = & $GLOBALS[AJAX_SHOW_HIDE];
    $v[]= "ashow('$obj');";
}

function js($js) {
    $v  = & $GLOBALS[AJAX_JS];
    $v[]= "$js";
}
?>
