<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants for module lmstosms
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the lmstosms specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    local
 * @subpackage lmstosms
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$dataToSend['StudentsInfo'] = 
    Array
        (
            'StudentCourseInfo' => Array
                (
                    'strClsCourseCode' => 'ORT300',
                    'strClsDivisionCode' => 'FP',
                    'lngClsClassSequenceNbr' => 3,
                    'lngClsStudentUID' => 1173270,
                     'strClsPostTestGrade' => 84,
                    'dblClsdHoursCompleted' => 0,
                    'dteClsdStartDate' => '10/12/2012',
                    'dteClsdEndDate' => '11/30/2012',
                    'strClsCompletionStatus' => 'P'
                )
 
        );
 


    $sms_soap_url = 'https://smslmsregagent.teex.tamus.edu/LMSReg.asmx?WSDL';
	// $sms_soap_url = "http://develsmslmsregagent.teex.tamus.edu/LMSReg.asmx?WSDL";

        $client = new \SoapClient($sms_soap_url, array('cache_wsdl' => 0, 'exceptions' => true, 'trace' => true));
        $response = $client->ReturnStudentInfo($dataToSend);
		var_dump($response);

