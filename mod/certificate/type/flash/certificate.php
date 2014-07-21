<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}


$flashvars = array(
                  'name'      => fullname($USER),
                  'date'      => certificate_get_date($certificate, $certrecord, $course),
                  'course'    => $course->fullname,
                  'div'       => substr($course->idnumber, 0, 2),
                  'hours'     =>  $certificate->printhours,
                  'thours'    =>  $certificate->tcleosehours,
                  'courseid'  =>  $course->id,
                  'template'  => $certificate->template,
                  'teexid'    => $USER->idnumber,
                  'coursenum' => $course->idnumber
                  );


// $flashvars_encoded = htmlspecialcharacters( http_build_query($flashvars) );
$flashvars_encoded = http_build_query($flashvars);

$certhtml = <<<HERE
               <div id="flashContent" style="overflow:hidden">
                        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="97%" height="97%" id="certificate" align="middle">
                                <param name="FlashVars" value="$flashvars_encoded" />
                                <param name="movie" value="certificate.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="play" value="true" />
                                <param name="loop" value="true" />
                                <param name="wmode" value="window" />
                                <param name="scale" value="showall" />
                                <param name="menu" value="true" />
                                <param name="devicefont" value="false" />
                                <param name="salign" value="" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="certificate.swf" width="97%" height="97%">
                                        <param name="movie" value="certificate.swf" />
                                        <param name="quality" value="high" />
                                        <param name="bgcolor" value="#ffffff" />
                                        <param name="play" value="true" />
                                        <param name="loop" value="true" />
                                        <param name="wmode" value="window" />
                                        <param name="scale" value="showall" />
                                        <param name="menu" value="true" />
                                        <param name="devicefont" value="false" />
                                        <param name="salign" value="" />
										<param name="FlashVars" value="$flashvars_encoded" />
                                        <param name="allowScriptAccess" value="sameDomain" />
                                <!--<![endif]-->
                                        <a href="http://www.adobe.com/go/getflash">
                                                <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                        </a>
                                <!--[if !IE]>-->
                                </object>
                                <!--<![endif]-->
                        </object>
                </div>
HERE;

?>
