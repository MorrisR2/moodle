<?php

include_once("./completer_data.inc.php");

/*
$completers = array(
                array(
                    'strClsCourseCode' => 'FSA113',
                    'strClsDivisionCode' => 'LS',
                    'lngClsClassSequenceNbr' => '4',
                    'lngClsStudentUID' => '1180914',
                    'strClsPostTestGrade' => '76',
                    'dblClsdHoursCompleted' => '0',
                    'dteClsdStartDate' => '12/12/2012',
                    'dteClsdEndDate' => '06/6/2013',
                    'strClsCompletionStatus' => 'P',
                ),
                array(
                    'strClsCourseCode' => 'FSA113',
                    'strClsDivisionCode' => 'LS',
                    'lngClsClassSequenceNbr' => '4',
                    'lngClsStudentUID' => '529413',
                    'strClsPostTestGrade' => '88',
                    'dblClsdHoursCompleted' => '0',
                    'dteClsdStartDate' => '12/12/2012',
                    'dteClsdEndDate' => '06/6/2013',
                    'strClsCompletionStatus' => 'P',
                )
);
*/

foreach ($completers as $cm) {

    // $dataToSend = array("StudentsInfo"=>array( "StudentCourseInfo" => array( "strClsCourseCode"=>$coursetail,
	$dataToSend = array('StudentsInfo'=>Array('StudentCourseInfo' => $cm ));
    $sms_soap_url = 'https://smslmsregagent.teex.tamus.edu/LMSReg.asmx?WSDL';
	// print_r($dataToSend);
    $client = new \SoapClient($sms_soap_url, array('cache_wsdl' => 0, 'exceptions' => true, 'trace' => true));
    $response = $client->ReturnStudentInfo($dataToSend);
    var_dump($response);
}

echo "done\n";


