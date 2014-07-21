
// document.domain: "teex.tamus.edu";

    function XMLToString(oXML) {
      if (window.ActiveXObject) {
        return oXML.xml;
      } else {
        return (new XMLSerializer()).serializeToString(oXML);
      }
    }
    
    function removeAutoIds(tree) {
        if (! tree) {
            return tree;
        }
        if(tree.hasChildNodes()) {
            var nodecount=tree.childNodes.length;
            for(var i=0; i<nodecount; i++) {
                removeAutoIds(tree.childNodes[i]);
            }
        }
        if ("getAttribute" in tree) {
            if ( /autoidTEEX/.test(tree.getAttribute("id")) ) {
    			tree.removeAttribute('id');
            }
        }
        return tree;
    }
    
    function saveXml(userid, course_id) {
    	document.querySelector("[id=" + 'saveuserid' + "]").value = userid;
        document.querySelector("[id=" + 'choosecourse' + "]").value = course_id;
    	document.forms["saveform"].submit();
		alert('Saved');
    }
    
    function updatexml(childid, value, xml) {
    	// console.log("finding element with id '" + childid + "'");
    	var parts = childid.split('-attr-');
    	console.dir(parts);
    	if (parts[1] ) {
			// console.log(xml.querySelector("[id=" + 'autoidTEEX62' + "]"));
    		// xml.querySelector("[id=" + parts[0] + "]").setAttribute(parts[1],value);
			// xml.querySelector("[id=" + parts[0]+ "]").setAttribute(parts[1],value);
			xml.querySelector("[id=" + parts[0] + "]").setAttribute(parts[1],value);
    	} else {
    		xml.querySelector("[id=" + childid + "]").textContent = value;
    	}
        // Round tripping through a string because cloneNode doesn't work
    	var clone = loadXMLString( XMLToString(xml) );
    	document.querySelector("[id=" + 'savexml' + "]").value = XMLToString(removeAutoIds(clone));
    }
    
    /*
     function findAncestor(el, comparisonFunc) {
        if (comparisonFunc(el) == true) return el;
        else if (el.parentNode) {
          return arguments.callee(el.parentNode, comparisonFunc);
        }
        else return false;
      }
    */
    
    function deleteXml(id, xml) {
    	var r=confirm("This node will be irretrievably deleted when you save your changes.");
    	if (r==true) {
    		xmlElem =  xml.querySelector("[id=" + id + "]");
    		xmlElem.parentNode.removeChild(xmlElem);
    		htmlElem = document.querySelector("[id=" + id + "]");
    		htmlElem.parentNode.removeChild(htmlElem);
        	var clone = loadXMLString( XMLToString(xml) );
        	document.querySelector("[id=" + 'savexml' + "]").value = XMLToString(removeAutoIds(clone));
    	}
    }
    
    
    function addChangeEvents(tree, xml){
        var elArray = document.getElementsByTagName('input');
        for(var i=0; i<elArray.length; i++){
            var relation = tree.compareDocumentPosition(elArray[i]);
            if ((relation & Node.DOCUMENT_POSITION_CONTAINED_BY) == 0) continue;
            // if(elArray[i].id == 'userid') continue;
            elArray[i].onchange = function(){
    			updatexml(this.id, this.value, xml);
            };
        }
    
        var elArray = document.getElementsByTagName('button');
        for(var i=0; i<elArray.length; i++){
    		// if (! elArray[i].className == 'delete') continue;
            var relation = tree.compareDocumentPosition(elArray[i]);
            if ((relation & Node.DOCUMENT_POSITION_CONTAINED_BY) == 0) continue;
            // if(elArray[i].id == 'userid') continue;
    		if (elArray[i].className == 'delete') {
            	elArray[i].onclick = function(){
    				var definitionlist = findAncestor(this, function(el) { return el.tagName == 'DT' } );
                	deleteXml(definitionlist.id, xml);
    				return false;
            	};
    		}
        }
    }
    
    
    function loadXMLDoc(dname)
    {
    if (window.XMLHttpRequest)
      {
      xhttp=new XMLHttpRequest();
      }
    else
      {
      xhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    xhttp.open("GET",dname,false);
    xhttp.send("");
    return xhttp.responseXML;
    }
    

    function loadXMLString(txt) 
    {
    if (window.DOMParser)
      {
      parser=new DOMParser();
      xmlDoc=parser.parseFromString(txt,"text/xml");
      }
    else // Internet Explorer
      {
      xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
      xmlDoc.async=false;
      xmlDoc.loadXML(txt); 
      }
    return xmlDoc;
    }
    
    function AttachEvent(element, type, handler) {  
        if (element.addEventListener) element.addEventListener(type, handler, false);  
        else element.attachEvent("on"+type, handler);  
    }  
    
    
    function textareaToTree() {
      xml = loadXMLString(this.value);
      var error = xml.getElementsByTagName('parsererror');
      if (error[0]) {
        var errStr=error[0].outerHTML;
    	var newwindow2=window.open('','name','height=300,width=350');
    	var tmp = newwindow2.document;
        tmp.write(errStr);
    	return false;
      }
    
      xsl=loadXMLDoc("dl.xsl");
      xml = assignIDs(xml);
    
       var clone = loadXMLString( XMLToString(xml) );
       document.querySelector("[id=" + 'savexml' + "]").value = XMLToString(removeAutoIds(clone));
    // code for IE
    if (window.ActiveXObject)
      {
      ex=xml.transformNode(xsl);
      document.querySelector("[id=" + "xmltohtml" + "]").innerHTML=ex;
      }
    // code for Mozilla, Firefox, Opera, etc.
    else if (document.implementation && document.implementation.createDocument)
      {
    
      xsltProcessor=new XSLTProcessor();
      xsltProcessor.importStylesheet(xsl);
      resultDocument = xsltProcessor.transformToFragment(xml,document);
      var xmltohtml = document.querySelector("[id=" + "xmltohtml" + "]");
      while (xmltohtml.firstChild) {
        xmltohtml.removeChild(xmltohtml.firstChild);
      }
      xmltohtml.appendChild(resultDocument);
      }
      setEvent();
      addChangeEvents(xmltohtml, xml);
    }
    
    
    
    var assignIDs = (function() {
    var idnum = 0;
    return function(tree) {
        if (! tree) {
            return tree;
        }
        if(tree.hasChildNodes()) {
            var nodecount=tree.childNodes.length;
            for(var i=0; i<nodecount; i++) {
                assignIDs(tree.childNodes[i]);
            }
        }
    	if ("getAttribute" in tree) {
    		if (! tree.getAttribute("id") ) {
    			try {
    				tree.setAttribute('id', 'autoidTEEX' + idnum++);
    				// console.log("setting ID " + 'autoidTEEX' + idnum);
    			}
    			catch(err) {
    				var thetype = typeof tree;
    				console.log("Error setting id of " + thetype + ' ' + err.name + ': ' + err.message);
    			}
    		}
    	}
    	return tree;
    }
    })();
    
    
