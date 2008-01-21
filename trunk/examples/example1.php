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

error_reporting(E_ALL);
require("../phpajax/phpajax.php");
require("showsource.php");

/**
 *  Example 1
 *  This class shows how to read something from the client
 *  and write something on the brower.
 *  
 *  @author Cesar D. Rodas
 *  @package PHP-Ajax-Examples
 */
class example1 extends phpajax {
    /**
     *  Input
     *
     *  Read all what is needed from client.
     *
     */
    var $inputs=array("input1","input2");
    var $hotkeys="ctrl-h";
    
    function loading() {
        aprint('loading', 'Loading...');
        ashow('loading');
    }

    function main() {
        /*
         *  The variables that was read in the method
         *  "input" is referenced here as local variable
         *  for made easy in the code time.
         */
        $a = & $this->input1;
        $b = & $this->input2;

        /* Sleep awhile for show the loading */
        sleep(3);


        if ( !is_numeric($a) ) {
            alert("A ($a) must be numeric");
            ahide("output1");
            /* stop ajax */
            return;
        }

        if ( !is_numeric($b) ) {
            alert("A ($b) must be numeric");
            ahide("output1");
            /* stop ajax */
            return;
        }

        aprint('output1', $a*$b);
        aprint('input1', $a);
        aprint('input2', $b);
        js("alert('hola mundo');");
        ahide("ashow");

    }
}


/* Initiliaze php ajax*/
phpajax::init();

?>
<html>
<head>
    <title>Example of how to implement PHP Ajax</title>
<?php phpajax_js("../phpajax/");?>
</head>
<body>
<div id='loading' style="visibility:hidden;">
Cargando...
</div>

    A = <input type="text" name="input1" id="input1"><br/>
    B = <input type="text" name="input2" id="input2"><br/>
    A * B =  <span id="output1"></span><br/>
    <input type="submit" value="Click me(control + h)" onclick="example1()">
    <input type="submit" value="40 x 50" onclick="example1(40,50)">
<hr>
<input type="submit" value="show source" onclick="showsource('example1.php')"><br />
<div id="source"></div>
</body>
</html>
