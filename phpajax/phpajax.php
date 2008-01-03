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

define('PHPAJAX_DIR',dirname(__FILE__));
require(PHPAJAX_DIR."/defines.php");
require(PHPAJAX_DIR."/jsphp.php");

/**
 *  PHP Ajax
 *
 *  This is the PHP Ajax main class.
 *
 *
 *  @package PHP-Ajax
 *  @author Cesar D. Rodas <cesar@sixdegress.com.br>
 *  @copyright PHP Ajax
 *  @version 1.0
 */
class phpajax {
    /**
     *  Class constructor
     *
     */
    function phpajax() {
    }


    function setVar($name) {
        foreach($name as $k => $v)
            $this->$k = $v;
    }

    /**
     *  PHP Ajax Initialize
     *
     *  This method call the php ajax process. This method
     *  could be called without a instance.
     *  @access public
     */
    function init() {
            $f = & $_POST['phpajax'];
            /**
             *  The actual request is not an ajax
             *  request.
             */
            if ( !isset($f)  ) return;
            
            /**
             *  Now we see if the ajax  request is valid,
             *  and if it cames thought prototype or an iframe.
             *  If this came thought iframe this is probabily
             *  because we're reciving a file. Thought iframe
             *  also the response is diferrent, we need to send
             * javascript code, and inside the javascript our json
             *  information.
             */
     
                $input = json_decode(stripslashes($f),true);
                if (! ( isset($input['fnc']) && isset($input['version']) )) return;
                /* create object */
                $obj = new $input['fnc'];
                /* set variable */
                $obj->setVar($input);
                /* execute */
                $obj->main();
                /************************************************************/
                $s = new stdClass;
                /* aprint */
                if ( isSet($GLOBALS[AJAX_PRINT]) )
                    foreach($GLOBALS[AJAX_PRINT] as $k => $v) {
                        $s->aprint[]=$k;
                        $s->aprint[]=$v['text'];
                        $s->aprint[]=$v['override'];
                    }
                /* ahide - ashow */
                if ( isSet($GLOBALS[AJAX_SHOW_HIDE]) )
                    foreach($GLOBALS[AJAX_SHOW_HIDE] as $v)
                        $s->ahideshow[]=$v;

                /* alert  */
                if ( isSet($GLOBALS[AJAX_ALERT]) )
                    foreach($GLOBALS[AJAX_ALERT] as $v)
                        $s->alert[]=$v;
                /* javascript  */
                if ( isSet($GLOBALS[AJAX_JS]) )
                    foreach($GLOBALS[AJAX_JS] as $v)
                        $s->js[]=$v;
                /************************************************************/
                if ( isset($_POST['iframe']) ) echo  "<script> f = ";
                echo json_encode($s);
                if ( isset($_POST['iframe']) )  {
                    if ( isset($_POST['callback']) and strlen($_POST['callback']) > 2 )
                        echo ";parent.".$_POST['callback']."()";
                    echo ";parent.process(f); b = parent.getObject('".$_POST["div"]."'); b.innerHTML = '';";
                    echo "</script>";
                }
                exit;
        
    }
    /**
     *  Ajax Main
     *
     *  This method is executed when an Ajax action is executed.
     *
     *  @abstract
     */
    function main(){}

    /**
     *  Ajax Input
     *
     *  This method is trigged for read all inputs from client.
     *
     *  @abstract
     */
    function input() {}
    /**
     *  Shows a message in the client while the ajax request
     *  is processing
     *
     *  @abstract
     */
    function loading() {}

}



/**
 *  PHP Ajax Javascript
 *
 *  Generate the Javascript code for PHP ajax could be executed in the client-side.
 *
 *  @param string $dir Directory where is the phpajax package
 *  @return String
 */
function phpajax_js($dir="./") {
    $f = get_declared_classes();
    echo "<script src=\"${dir}json.js\"></script>\n";
    echo "<script src=\"${dir}phpajax.js\"></script>\n";
    echo "<script src=\"${dir}prototype.js\"></script>\n";

    echo "<script type='text/javascript'>\n";
    echo "/** \n";
    echo " * Powered by phpajax - www.phpajax.org \n";
    echo " */ \n";
    for($i=count($f)-1; $i >= 0;$i--) {
        $name = &  $f[$i];
        if (  $name == 'phpajax') break;
        $obj = new $name;
        if ( !is_subclass_of( $obj ,  'phpajax') ) continue;
        echo "function $name() {\n";
        print_source("var i=0;");
        print_source("var args = {$name}.arguments;");
        print_source("var input = new Array();");
        /* inputs */
        $GLOBALS[AJAX_INPUT]='';
        $obj->input();
        echo $GLOBALS[AJAX_INPUT];
        /* end */
        /* loading */
        $v = & $GLOBALS[AJAX_SHOW_HIDE];
        $print = & $GLOBALS[AJAX_PRINT];
        $v = array();
        $print = array();

        $obj->loading();
        $callback="";
        if (  count($v) > 0 ) {
            $callback="{$name}_loaded";
            foreach($print as $k => $value)
                print_source("aprint('$k','".js_encode($value['text'])."',".($value['override'] ? "true" : "false").");");

            foreach($v as $value) {
                print_source($value);
            }
        }
        /* end */
        $uri = $_SERVER['REQUEST_URI'];
        print_source("phpajax_execute('$uri','$name',input,'$callback');");
        echo "}\n";
        if (  count($v) > 0 ) {
            echo "function $callback() {\n";
            foreach($v as $value) {
                /**
                 *  We must automatically hide what we have
                 *  show in the loading event
                 */
                if ( substr($value,0,5) == "ashow") {
                    print_source(  "ahide".substr($value,5) );
                }
            }
            echo "}\n";
        }

    }
    echo "</script>\n";
    echo "<div id='phpajax-div' style='display: none'></div>";
}
/**
 *  Print JS code
 *
 *  @access private
 */
function print_source($f) {
    echo "\t$f\n";
}

/**
 *  Encode String
 *
 *  Encode string for print in a javascript statement.
 *
 *  @access private
 */
function js_encode($str) {
    return str_replace(array("\r","\t","\n"),array('\r','\t','\n'),$str);
}

if ( !is_callable('json_encode') ) {
    require_once(PHPAJAX_DIR."/JSON.php");
    /**
     *  JSON Encode for PHP4
     *
     *
     *  @access private
     */
    function json_encode($obj) {
        $json = new JSON;
        $var = $json->serialize( $obj );
        return $var;
    }
}

if ( !is_callable('json_decode') ) {
    require_once(PHPAJAX_DIR."/JSON.php");
    /**
     *  JSON Decode for PHP4
     *
     *
     *  @access private
     */
    function json_decode($obj,$arr=false) {
        $json = new JSON;
        $var = $json->unserialize( $obj );
        if ( !$arr ) return $var;
        return get_object_vars($var);
    }
}
?>
