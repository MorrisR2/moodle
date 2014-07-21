<?php

class block_helpform extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_helpform');
    }

    function get_content() {

        global $CFG, $OUTPUT;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);
        /*
        if (empty($currentcontext)) {
            $this->content = '';
            return $this->content;
        }
        */
        $courseid = $this->page->course->id ?: '';

        $icon = '<img src="'.$OUTPUT->pix_url('i/users') . '" class="icon" alt="" />';
        $this->content->items[] = '<p><a title="'.get_string('helpform','block_helpform').'" href="'.
                                  $CFG->wwwroot.'/mod/helpform/view.php?courseid='.$courseid.'">'.$icon.$this->title.'</a><br />' .
                                  '<strong>'.get_string('phone').':</strong> 1-877-929-2022<br /><strong>' . 
                                  get_string('email') . ':</strong> ' .
                                  '<a href="mailto:elearning@teex.tamu.edu">eLearning@teex.tamu.edu</a><br />Mon-Fri, 7 a.m. - 6 p.m. CST<br><br>' .
								  '<a href="'.  $CFG->wwwroot.'/teex/browsertest/DisablePopUpBlocker.pdf" target="_blank">' .
                                  get_string('disablepopupblock', 'block_helpform') .  '</p>';
                                  return $this->content;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('helpform', 'block_helpform'));
    }

}
