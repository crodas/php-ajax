<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by PHP Ajax Team                              *
*   info@phpajax.org                                                      *
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

/**
 *  PHP Ajax
 *
 *  This is the PHP Ajax main class.
 *
 *
 *  @package PHP-Ajax
 *  @author Cesar D. Rodas <saddor@gmail.com>
 *  @copyright PHP Ajax
 *  @abstract
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
			 *	The actual request is not an ajax
			 *	request.
			 */
			if ( !isset($f)  ) return;
			
			/**
			 *	Now we see if the ajax  request is valid,
			 *	and if it cames thought prototype or an iframe.
			 *	If this came thought iframe this is probabily
			 *	because we're reciving a file. Thought iframe
			 *	also the response is diferrent, we need to send
			 * javascript code, and inside the javascript our json
			 *	information.
			 */
			if (trim($f) == "iframe") {
				$s = new stdClass;
				
			} else {
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
				if ( isSet($GLOBALS['PHPAJAXINPUTPRINT']) )
					foreach($GLOBALS['PHPAJAXINPUTPRINT'] as $k => $v) {
						$s->aprint[]=$k;
						$s->aprint[]=$v;
					}	
            /* ahide - ashow */
            if ( isSet($GLOBALS['PHPAJAXINPUTSHOWHIDE']) )
                foreach($GLOBALS['PHPAJAXINPUTSHOWHIDE'] as $v)
                    $s->ahideshow[]=$v;
                
            /* alert  */
            if ( isSet($GLOBALS['PHPAJAXALERT']) )
                foreach($GLOBALS['PHPAJAXALERT'] as $v)
                    $s->alert[]=$v;

            /************************************************************/
            echo json_encode($s);
            exit;
        }
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
 *  Ajax Read
 *
 *  Transform the input required into javascript code.
 *
 *  @access private
 */
function aread($p) {
    $t = & $GLOBALS['PHPAJAXINPUT'];
    $t .= "\tinput[i++] = '$p';\n";
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
function aprint($dst,$txt) {
    $v = & $GLOBALS['PHPAJAXINPUTPRINT'][$dst];
    $v.= $txt;
}

/**
 *  Show a message box in the client.
 *
 *  @param string $text String to show
 */
function alert($txt) {
     $v = & $GLOBALS['PHPAJAXALERT'];
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
   $v  = & $GLOBALS['PHPAJAXINPUTSHOWHIDE'];
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
    $v  = & $GLOBALS['PHPAJAXINPUTSHOWHIDE'];
    $v[]= "ashow('$obj');";
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
        imprimir("var i=0;");
        imprimir("var input = new Array();");
        /* inputs */
        $GLOBALS['PHPAJAXINPUT']='';
        $obj->input();
        echo $GLOBALS['PHPAJAXINPUT'];
        /* end */
        /* loading */
        $v = & $GLOBALS['PHPAJAXINPUTSHOWHIDE'];
        $print = & $GLOBALS['PHPAJAXINPUTPRINT'];
        $v = array();
        $print = array();

        $obj->loading();
        $callback="";
        if (  count($v) > 0 ) {
            $callback="{$name}_loaded";
            foreach($print as $k => $value)
                imprimir("aprint('$k','".js_encode($value)."');");

            foreach($v as $value) {
                imprimir($value);
            }
        }
        /* end */
        $uri = $_SERVER['REQUEST_URI'];
        imprimir("phpajax_execute('$uri','$name',input,'$callback');");
        echo "}\n";
        if (  count($v) > 0 ) {
            echo "function $callback() {\n";
            foreach($v as $value) {
                /**
                 *  We must automatically hide what we have
                 *  show in the loading event
                 */
                if ( substr($value,0,5) == "ashow") {
                    imprimir(  "ahide".substr($value,5) );
                }
            }
            echo "}\n";
        }

    }
    echo "</script>\n"; 
	 echo "<div id='phpajax-div'></div>";
}
/**
 *  Print JS code
 *
 *  @access private
 */
function imprimir($f) {
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
     *  @access private
     */
    function json_decode($obj,$arr=false) {
        $json = new JSON;
        $var = $json->unserialize( $obj );
        if ( !$arr ) return $var;
        $f = get_object_vars($var);
        foreach($f as $property)
            $ret[$property] = $var->$property;
        return $ret;
    }
}
?>
