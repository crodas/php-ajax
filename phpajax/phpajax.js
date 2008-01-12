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

VERSION="PHPAJAX_1_1"
var formIdCnt = 0;
var PHPAJAX_Keys=new Array();

function phpajax_execute(url,fnc, params, callback) {
    var rta = {"fnc":fnc, "version":VERSION };

    /* Read variables */
    for(i=0; i < params.length; i++) {
        if ( params[i].source && getObject(params[i].source) && getObject(params[i].source).type   && getObject(params[i].source).type == "file")
            return phpajax_iframe_execute(url,fnc,params,callback);

        variable = params[i].value ? params[i].value : getObjValue(params[i].source);
        
        eval("rta." + params[i].name + "= variable; " );
    }

    var success  = function(t) {
        try {
            //alert(         t.responseText );
            elem = eval ( "(" + t.responseText  +")");

            if ( callback != '') {
                setTimeout ( callback + "()", 100 );
            }

            process(elem);
        } catch(e) {

            alert(e + " " + t.responseText);
        }
    }
    var failure  = function(t){ alert("Error the ajax request"); }
    var myAjax = new Ajax.Request(url,
        {
            method:'post',  onSuccess:success, onFailure:failure, parameters: "phpajax=" + rta.toJSONString()
        }
    ) 
}

function phpajax_iframe_execute(url, fnc, params, callback) {
    /* Getting information about the div container. */
    maincontainer = getObject('phpajax-div');
    
    /* creating a container */
    container = document.createElement("div");
    container.id = "container" + (++formIdCnt);
    
    destiny = document.createElement("iframe");
    destiny.name = "iframe" + formIdCnt;
    destiny.id = destiny.name;
    
    
    /* creating a form */
    form = document.createElement("form");
    form.id = "form" + formIdCnt;
    form.method = "POST";
    form.enctype="multipart/form-data" ;
    form.target = destiny.name;
    
    form.appendChild( destiny );
    
    /* adding information into the form */
    div = document.createElement("input");
    div.name = "div";
    div.value = container.id;
    form.appendChild( div );
    

    callback_form = document.createElement("input");
    callback_form.name = "callback";
    callback_form.value = callback;
    form.appendChild( callback_form );

    
    
    magic = document.createElement("input");
    magic.name = "iframe";
    magic.value = "iframe";
    form.appendChild( magic );
    
    var cntFiles = 0;
    var accInputs = {"fnc":fnc, "version":VERSION };

    for(i=0; i < params.length; i++) {
        tmp = getObject( params[i].source );
        
        /* adding the information */
        eval( "accInputs." + params[i].name + " = params[i].value ? params[i].value : getObjValue(params[i].value) ");
        if (tmp && tmp.type == "file") {
            tmp1 = tmp.cloneNode(false);
            tmp1.name = "phpajax_" + tmp1.name;
            tmp1.id = tmp1.name;
 
            form.appendChild(  tmp1  );
        }
    }

    vars = document.createElement("input");
    vars.name = "phpajax";
    vars.id = vars.name;

    vars.value = accInputs.toJSONString();
    
    form.appendChild( vars );
    
    /* adding into the div-container */
    container.appendChild( form );

    /* adding into the div-maincontainer */
    maincontainer.appendChild( container );
    
    /* submit a form */
    setTimeout("getObject('" + form.id +"').submit(); ", 1000);
}

function process(rta) {
    if ( rta.aprint )
        process_aprint(rta.aprint);

    if ( rta.ahideshow ) {
        for(i=0; i < rta.ahideshow.length; i++)
            eval(rta.ahideshow[i]);
    }

    if ( rta.alert ) {
        for(i=0; i < rta.alert.length; i++)
            alert(rta.alert[i]);
    }

    if ( rta.js ) {
        for(i=0; i < rta.js.length; i++)
            eval(rta.js[i]);
    }
}

function process_aprint(e) {
    for(i=0; i < e.length; i++) {
        aprint(e[i++], e[i++],e[i]);
    }
}

function ahide(elem) {
    showhide(elem,'hidden');
}

function ashow(elem) {
    showhide(elem,'visible');
}

function showhide(elem,status) {
   obj = getObject(elem);
    if ( obj ) {
        try {
            obj.style.visibility=status;
        } catch (e) {
            try {
                obj.visibility=status;
            }   catch (f) {}
        }
    }

}

function aprint(obj_name,txt,override) {
    obj = getObject(obj_name);
    if ( !obj ) return;
    if (obj.value !=undefined) {
        if (override) obj.value = txt;
        else   obj.value += txt;
    }   else if (obj.innerHTML != undefined) {
        if (override) obj.innerHTML = txt;
        else   obj.innerHTML += txt;
    }


}

function getObject(e) {
   var obj;
    obj=null;
    if (document.getElementById)
        obj = document.getElementById(e);
    else if (document.all)
        obj = document.all[e];
    else if (document.layers)
        obj = document.layers[e];
    return obj;
}


function getObjValue(e) {
    obj = getObject(e);
    if ( obj && obj.value)
        return obj.value;
    return "";
}

function phpajax_keyaction(letters,theaction) {
    if ( phpajax_keyaction_validate(letters) ) {
        PHPAJAX_Keys[letters] = theaction;
        document.onkeydown = phpajax_keyaction_deamon;
        return true;
    }
    return false;
}

function phpajax_keyaction_deamon(xEvent) {
    var pressed;
    
    f=phpajax_keyaction_speacial_keys(xEvent,false);
    switch( f ) {
        case "shift":
        case "alt":
        case "ctrl":
            pressed= f + "+" + phpajax_keyaction_speacial_keys(xEvent,true);
            break;
        default:
            pressed=f;
    }
    
    if ( PHPAJAX_Keys[pressed.toLowerCase()] ) 
        PHPAJAX_Keys[pressed.toLowerCase()]()
}

function chr(e) {
    return String.fromCharCode(e);
}

function phpajax_keyaction_speacial_keys(e,avoidSpecialKeys) {
    if ( avoidSpecialKeys )
        return chr(e.keyCode);
    var evt = navigator.appName=="Netscape" ? e:event;
    var ret = 0;
    var shiftPressed=false; 
    var altPressed=false;
    var ctrlPressed=false;
    if (navigator.appName=="Netscape" && parseInt(navigator.appVersion)==4) {
        var mString =(e.modifiers+32).toString(2).substring(3,6);
        shiftPressed=(mString.charAt(0)=="1");
        ctrlPressed =(mString.charAt(1)=="1");
        altPressed  =(mString.charAt(2)=="1");
    } else {
        shiftPressed= evt.shiftKey;
        altPressed  = evt.altKey;
        ctrlPressed = evt.ctrlKey;
    }
    
    if ( shiftPressed ) return "shift" ;
    if ( altPressed   ) return "alt" ;
    if ( ctrlPressed  ) return "ctrl" ;
    return chr(e.keyCode);
}

function phpajax_keyaction_validate(letters) {
    if ( letters.length < 1) {
        return false;
    }
    letters=letters.toLowerCase();
    parts = letters.split("+");
    switch ( parts.length ) {
        case 1:
                if ( parts[0].length == 1 && parts[0] >= 'a' && parts[0] <= 'z')
                return true;
        return false;
        break;
        case 2:
                switch ( parts[0] ) {
                    case "ctrl":
                    case "shift":
                    case "alt":
                            break;
                    default:
                            return false;
                }
                if ( parts[1].length == 1 && parts[1] >= 'a' && parts[1] <= 'z')
                return true;
                return false;
                break;
        default:
                return false;
    }
    return false;
}