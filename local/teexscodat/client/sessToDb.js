

	// var token = '89701a1d0c7ad530082d5fa8fd094526';
    var token = 'b173b8dc29c3135709de0f39adb9caa5';
	var serverurl = '/webservice/xmlrpc/server.php' + '?wstoken=' + token;
	var course_session_id;

	function course_sess2db(session) {
		var service = new rpc.ServiceProxy(serverurl, {asynchronous:false, protocol: "XML-RPC"});
		try { session = service.local_teexscodat_course_session(session); }
			catch(e){ alert("Unable to update session because: " + e); }
		return session;
	}


    function module_sess2db(session) {
            var service = new rpc.ServiceProxy(serverurl, {asynchronous:false, protocol: "XML-RPC"});
            try { session = service.local_teexscodat_module_session(session); }
                    catch(e){ alert("Unable to update session because: " + e); }
            return session;
        }

/*
	function quiz_sessToDb(argh) {
	var inspect = argh;
	// var service = new rpc.ServiceProxy(serverurl, {asynchronous:false, protocol: "XML-RPC"});
	// return argh;
	// Error with empty function
}
*/

   function quiz_sessToDb(session, async) {
			if(typeof async == 'undefined') { async = false; }
			if ( empty(session['score']) ) {
				delete session['score'];
			}
            var service = new rpc.ServiceProxy(serverurl, {asynchronous:false, protocol: "XML-RPC"});
			if (async) {
				setTimeout(function(){ service.local_teexscodat_quiz_session(session); }, 100);
				return session;
			} else {
            	try { session = service.local_teexscodat_quiz_session(session); }
                	   catch(e){ alert("Unable to update session because: " + e); }
            	return session;
			}
    }

/*
function quiz_sessToDb(session, async) {
  var service = new rpc.ServiceProxy(serverurl, {
                          asynchronous: async,   //default: true
                          sanitize: true,       //default: true
                          methods: ['local_teexscodat_quiz_session'],   //default: null (synchronous introspection populates)
                          protocol: 'JSON-RPC', //default: JSON-RPC
  }); 
  service.local_teexscodat_quiz_session({
     params:{session:session},
     onSuccess:function(session){
         session(session);
     },
     onException:function(e){
         alert("error calling local_teexscodat_quiz_session: " + e);
         return true;
     }
  });
}
*/


	// Ray TODO - add a quiz ID and use that instead of module_id?
    function teex_quiz_session(quizNumber) {
			console.log("called teex_quiz_session(" + quizNumber + ")");
            var session = new Object;
			session.module_id = quizNumber;
            session.username = scorm.get("cmi.learner_id");
			console.log("retrieving scodat session for quiz module" + session.module_id + " student " + session.username);
            var service = new rpc.ServiceProxy(serverurl, {asynchronous:false, protocol: "XML-RPC"});
            try { session = service.local_teexscodat_quiz_session(session); }
                    catch(e){ alert("Unable to update session. Webservice says: " + e); }
            return session;
    }

