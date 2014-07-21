<?php

function local_hide_unenrolled_courses_extends_navigation($navigation) {
	global $PAGE;
	if ( ! has_capability('moodle/category:viewhiddencategories', $PAGE->context ) ) {
		$navigation->children->remove('courses');
		$navigation->children->remove('home', 70);
		$navigation->children->remove('sitehome');
		$navigation->children->remove('sitehome', 70);
   }
}

