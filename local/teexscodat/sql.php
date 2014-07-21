<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 1);
	
	require_once("../../db/class.mssql.inc.php");
	

do_sql();

function do_sql(){
	$dbcon = new mssql();
	$sql = "UPDATE mdl_local_teexscodat_quiz_sess SET score=82 WHERE username='lmsadmin' AND attempt_seq=2";
	$dbcon->query($sql);
	# $sql = "SELECT idnumber AS teexid, MAX(firstname), MAX(lastname), MAX(attempt_seq) as attempts, MAX(score) as passingscore FROM mdl_local_teexscodat_quiz_sess, mdl_user WHERE mdl_user.username=mdl_local_teexscodat_quiz_sess.username AND module_id='final' GROUP BY mdl_local_teexscodat_quiz_sess.username, idnumber";

  $sql = "SELECT idnumber AS teexid, MAX(attempt_seq) as attempts, 
  MAX(score) as passingscore, MAX(firstname) as firstname, MAX(lastname) as lastname
FROM mdl_local_teexscodat_quiz_sess, mdl_user 
WHERE 
  mdl_user.username = 
  mdl_local_teexscodat_quiz_sess.username  AND 
  module_id='final' 
GROUP BY idnumber";


  $sql = "SELECT username, MAX(attempt_seq) as attempts, 
  MAX(score) as passingscore
FROM mdl_local_teexscodat_quiz_sess
WHERE 
  module_id='final' 
GROUP BY username";

/*
  $sql = "SELECT idnumber AS teexid, firstname, lastname, passingscore, attempts
			FROM mdl_user
			INNER JOIN
				(SELECT username, MAX(attempt_seq) as attempts, 
					 MAX(score) as passingscore
				FROM mdl_local_teexscodat_quiz_sess
				WHERE module_id='final' 
				GROUP BY username) final
			ON final.username=mdl_user.username";

*/

  $sql = "SELECT idnumber AS teexid, firstname, lastname, mdl_user.id AS mdluserid,
				passingscore, attempts, mdl_user.id AS mdluserid, timedate,
				course_seconds
            FROM mdl_user
            INNER JOIN
                (SELECT username, MAX(attempt_seq) AS attempts, 
                     MAX(score) AS passingscore, MAX(timedate) as timedate,
					MAX(course_teexid) AS course_teexid
                FROM mdl_local_teexscodat_quiz_sess
                WHERE module_id='final' 
                GROUP BY username) final
            ON final.username=mdl_user.username 
			INNER JOIN mdl_local_teexscodat_course_sess
			ON mdl_local_teexscodat_course_sess.username=mdl_user.username AND final.course_teexid=mdl_local_teexscodat_course_sess.course_teexid
			WHERE final.course_teexid='ISSAF600'
            ";

   # $sql = "UPDATE mdl_local_teexscodat_quiz_sess SET course_teexid='ISSAF600'";
	#	$sql = 'SELECT course_teexid FROM mdl_local_teexscodat_quiz_sess';
	if ($dbcon->query($sql))
	{	
		while ($row = $dbcon->row()) {
			var_dump($row);
		}		
	}
}
	
?>
