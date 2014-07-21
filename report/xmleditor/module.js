
M.report_xmleditor = {

xml: null,
include: function (filename)
{
    var head = document.getElementsByTagName('head')[0];

    var script = document.createElement('script');
    script.src = filename;
    script.type = 'text/javascript';

    head.appendChild(script)
},

// document.domain: "teex.tamus.edu";


    displayResult: function (Y, userid, course_id)
    {
      xml = loadXMLDoc(baseurl + '&req=courseinfo&user_un=' + userid+'&course_id='+course_id);
      xsl=loadXMLDoc("dl.xsl");
      document.getElementById('savexml').value = XMLToString(xml);
      xml = assignIDs(xml);
    // code for IE
    if (window.ActiveXObject)
      {
      ex=xml.transformNode(xsl);
      document.getElementById("xmltohtml").innerHTML=ex;
      }
    // code for Mozilla, Firefox, Opera, etc.
    else if (document.implementation && document.implementation.createDocument)
      {
    
      xsltProcessor=new XSLTProcessor();
      xsltProcessor.importStylesheet(xsl);
      resultDocument = xsltProcessor.transformToFragment(xml,document);
      document.getElementById("xmltohtml").appendChild(resultDocument);
      }
      setEvent();
      addChangeEvents(document.getElementById("xmltohtml"), xml);
      AttachEvent(document.getElementById('savexml'), "change", textareaToTree);
    }
    
    
}

