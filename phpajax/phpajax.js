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

VERSION="PHPAJAX_1_0"
var formIdCnt = 0;

function phpajax_execute(url,fnc, params, callback) {
    var rta = {"fnc":fnc, "version":VERSION };

    /* Read variables */
    for(i=0; i < params.length; i++) {
		if ( getObject(params[i]).type   && getObject(params[i]).type == "file") 
			return phpajax_iframe_execute(url,fnc,params,callback);
			
		variable = getObjValue(params[i]);
		
        eval("rta." +params[i]+ "= variable; " );
    }

    var success  = function(t) {
        try {
            
            elem = eval ( "(" + t.responseText  +")");

            if ( callback != '') {
                setTimeout ( callback + "()", 100 );
            }

            process(elem);
        } catch(e) {
            alert(e);
        }
    }
    var failure  = function(t){ alert(t.responseText); }
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
		tmp = getObject( params[i] );
		
		/* adding the information */
		eval( "accInputs." + params[i] + " = getObjValue(params[i]) ");
        if (tmp.type == "file") {
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
}

function process_aprint(e) {
    for(i=0; i < e.length; i++) aprint(e[i++], e[i]);
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
        if ( obj.style.visibility )
            obj.style.visibility=status;
        else if (  obj.visibility )
            obj.visibility=status;
    }
}

function aprint(obj_name,txt) {
    obj = getObject(obj_name);
    if ( !obj ) return;
    if (obj.value !=undefined) {
        obj.value  = txt;
    }   else if (obj.innerHTML != undefined) {
        obj.innerHTML = txt;
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


