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
 *  Example 2
 *  This is a chat example.
 *  
 *  @author Cesar D. Rodas
 *  @package PHP-Ajax-Examples
 */
class chat_put extends phpajax {
    var $hotkeys=array("ctrl-m","alt-y");
    var $inputs=array("text","usr_id");

    function loading() {
        aprint('loading', 'Sending...');
        ashow('loading');
        ahide('send');
    }

    function main() {
        global $mysql;
        
        $a = & $this->text;

        # Add what I've chat to the chat-text.
        # the third paramete is false, because we don't
        # want to loose all the previous content.
        aprint('chat-text',"me> $a\n",false);
        mysql_query("insert into chat values('','".$this->usr_id."','".$_SERVER['REMOTE_ADDR']."','".addslashes($a)."')",$mysql);
        #set the input box empty
        aprint('text','');
        ashow('send');
        js("getObject('chat-text').scrollTop = getObject('chat-text').scrollHeight;");
    }
}

class chatupdater extends phpajax {
    var $inputs=array("last","usr_id");
    
    function main() {
        global $mysql;
        
        $last = $this->last;

        $sql="select * from chat where userid != '".$this->usr_id."' && postid  > $last order by postid desc limit 10";
        $r = mysql_query($sql,$mysql);
        $i=0;
        while ($row = mysql_fetch_array($r)){
            if ( $i++ == 0) {
                #last id
                $last = $row[0];
                #add a javascript code
                js("getObject('chat-text').scrollTop = getObject('chat-text').scrollHeight;") ;
            }
            aprint('chat-text',$row[2]."> ".$row[3]."\n",false);

        }
        mysql_free_result($r);
        mysql_close($mysql);
        aprint('last',$last);
    }
}

$mysql = mysql_connect("localhost","root","");
mysql_select_db("chat",$mysql);

/* Initiliaze php ajax*/
phpajax::init();

?>
<html>
<head>
    <title>Example of how to implement PHP Ajax</title>
<?php phpajax_js("../phpajax/");?>
<script>
    setInterval('chatupdater()',3000);
</script>
</head>
<body>

<textarea id="chat-text" readonly=1 cols=50 rows=10></textarea><br/>
<input type="text" id="text"><input type="button" id ="send" value="Send" onclick="chat_put()"><span id='loading' style="visibility:hidden;">
Cargando...
</span>
<input type=hidden name="last" id = "last" value=0>
<input type=hidden name="usr_id" id = "usr_id" value="<?=md5(time())?>">

<hr>
<input type="submit" value="show source" onclick="showsource('example3.php')"><br />
<div id="source"></div>
</body>
</html>
