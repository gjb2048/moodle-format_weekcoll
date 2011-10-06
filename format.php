<?php
// $Id: format.php,v 1.26 2011/10/06 15:45:14 gb2048 Exp $
/**
 * Collapsed Weeks Information
 *
 * @package    course/format
 * @subpackage weekcoll
 * @copyright  2009-2011 @ G J Barnard in respect to modifications of standard weeks format.
 * @link       http://docs.moodle.org/en/Collapsed_Weeks_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */

// Display the whole course as "weeks" made of of modules
// Included from "view.php"
// Initially modified from format.php in standard weeks format.

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Now get the css and JavaScript Lib.  The call to weekcoll_init sets things up for JavaScript to work by understanding the particulars of this course.
?>    
<style type="text/css" media="screen">
/* <![CDATA[ */
    @import url(<?php echo $CFG->wwwroot ?>/course/format/weekcoll/weeks_collapsed.css);
/* ]]> */
</style>
<!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/course/format/weekcoll/ie-7-hacks.css" media="screen" />
<![endif]-->

<?php
    $PAGE->requires->js('/course/format/weekcoll/lib_min.js');
    $PAGE->requires->js_function_call('weekcoll_init',
                                      array($CFG->wwwroot,
                                            preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                                            $course->id,
                                            null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration.
    if (ajaxenabled() && $PAGE->user_is_editing()) {
        // This overrides the 'swap_with_section' function in /lib/ajax/section_classes.js
        $PAGE->requires->js('/course/format/weekcoll/wc_section_classes_min.js');
    }

    $week = optional_param('cweeks', -1, PARAM_INT);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if ($week != -1) {
        $displaysection = course_set_display($course->id, $week);
    } else {
        $displaysection = course_get_display($course->id); // MDL-23939
    }

    $streditsummary  = get_string('editsummary');
    $stradd          = get_string('add');
    $stractivities   = get_string('activities');
    $strshowallweeks = get_string('showallweeks');
    $strweek         = get_string('week');
    $strgroups       = get_string('groups');
    $strgroupmy      = get_string('groupmy');
    $editing         = $PAGE->user_is_editing();

    if ($editing) {
        $strweekhide = get_string('hideweekfromothers');
        $strweekshow = get_string('showweekfromothers');
        $strmoveup   = get_string('moveup');
        $strmovedown = get_string('movedown');
    }

    //Print the Your progress icon if the track completion is enabled
    $completioninfo = new completion_info($course);
    echo $completioninfo->display_help_icon(); // MDL-23939

    echo $OUTPUT->heading(get_string('weeklyoutline'), 2, 'headingblock header outline');

    echo "<span id='maincontent'></span>";
    // Establish the table for the weeks with the colgroup and col tags to allow css to set the widths of the columns correctly and fix them in the browser so
    // that the columns do not magically resize when the toggle is used or we go into editing mode.
    echo '<table id="theweeks" summary="'.get_string('layouttable').'">';
    echo '<colgroup><col class="left" /><col class="content" /><col class="right" style="'.get_string('weekcolltogglewidth','format_weekcoll').'" /></colgroup>';
    
    // If currently moving a file then show the current clipboard
    if (ismoving($course->id)) {
        $stractivityclipboard = strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
        $strcancel= get_string('cancel');
        echo '<tr class="clipboard">';
        echo '<td colspan="3">';
        echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
        echo '</td>';
        echo '</tr>';
    }
    
    // Print Section 0 with general activities
    $section = 0;
    $thissection = $sections[$section];
    unset($sections[0]);

    if ($thissection->summary or $thissection->sequence or $PAGE->user_is_editing()) {
        echo '<tr id="section-0" class="section main">';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        
        if (!empty($thissection->name)) {
            echo $OUTPUT->heading($thissection->name, 3, 'sectionname');
        }
        
        echo '<div class="summary">';

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course','section', $thissection->id);
        $summaryformatoptions = new stdClass();
        $summaryformatoptions->noclean = true;
        $summaryformatoptions->overflowdiv = true;
        echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
            echo '<p><a title="'.$streditsummary.'" '.
                 ' href="editsection.php?id='.$thissection->id.'"><img src="'.$OUTPUT->pix_url('t/edit') . '" '.
                 ' class="icon edit" alt="'.$streditsummary.'" /></a></p>';
        }
        echo '</div>';

        print_section($course, $thissection, $mods, $modnamesused);

        if ($PAGE->user_is_editing()) {
            print_section_add_menus($course, $section, $modnames);
        }

        echo '</td>';
        echo '<td class="right side">&nbsp;</td>';
        echo '</tr>';
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }

    // Get the specific words from the language files.
    $weektext = get_string('sectionname','format_weekcoll'); 
    $toggletext = get_string('weekcolltoggle','format_weekcoll'); // The table row of the toggle.

    // Toggle all.
    echo '<tr id="toggle-all" class="section main">';
    echo '<td class="left side toggle-all" colspan="2">';
    echo '<h4><a class="on" href="#" onclick="all_opened(); return false;">'.get_string('weekcollopened','format_weekcoll').'</a><a class="off" href="#" onclick="all_closed(); return false;">'.get_string('weekcollclosed','format_weekcoll').'</a>'.get_string('weekcollall','format_weekcoll').'</h4>';
    echo '</td>';
    echo '<td class="right side">&nbsp;</td>';
    echo '</tr>';
    echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';

    // Now all the normal modules by week
    // Everything below uses "section" terminology - each "section" is a week.
    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $weekdate += 7200;                 // Add two hours to avoid possible DST problems
    $section = 1;
    $sectionmenu = array();
    
    $theweek = 0; // The section that will be the current week
    
    $weekofseconds = 604800;
    $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);

    $strftimedateshort = ' '.get_string('strftimedateshort');

    while ($weekdate < $course->enddate) {
        $nextweekdate = $weekdate + ($weekofseconds);
        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate+518400, $strftimedateshort);

        if (!empty($sections[$section])) {
            $thissection = $sections[$section];
        } else {
            unset($thissection);
            $thissection->course = $course->id;   // Create a new week structure
            $thissection->section = $section;
            $thissection->name    = null;
            $thissection->summary = '';
            $thissection->summaryformat = FORMAT_HTML;
            $thissection->visible = 1;
            $thissection->id = $DB->insert_record('course_sections', $thissection);
        }

        $showsection = (has_capability('moodle/course:viewhiddensections', $context) or $thissection->visible or !$course->hiddensections);

        if (!empty($displaysection) and $displaysection != $section) {  // Check this week is visible
            if ($showsection) {
                $sectionmenu[$section] = get_section_name($course, $thissection);
            }
            $section++;
            $weekdate = $nextweekdate;
            continue;
        }

        if ($showsection) {
            $currentweek = (($weekdate <= $timenow) && ($timenow < $nextweekdate));

            $currenttext = '';
            if (!$thissection->visible) {
                $sectionstyle = ' hidden';
            } else if ($currentweek) {
                $sectionstyle = ' current';
                $currenttext = get_accesshide(get_string('currentweek','access'));
                $theweek = $section;
            } else {
                $sectionstyle = '';
            }

            $weekperiod = $weekday.' - '.$endweekday;
            echo '<tr class="cps" id="sectionhead-'.$section.'">';
            // Have a different look depending on if the section name has been completed.
            if (is_null($thissection->name)) {
                echo '<td colspan="3"><a id="sectionatag-'.$section.'" class="cps_nosumm" href="#" onclick="toggle_week(this,'.$section.'); return false;"><span>';
                echo $weekperiod;
                echo '</span><br />'.$weektext.' '.$currenttext.$section.' - '.$toggletext.'</a></td>';
            } else {
                echo '<td colspan="2"><a id="sectionatag-'.$section.'" href="#" onclick="toggle_week(this,'.$section.'); return false;"><span>';
                echo $weekperiod;
                echo '<br />'.html_to_text($thissection->name).'</span> - '.$toggletext.'</a></td><td class="cps_centre">'.$weektext.'<br />'.$currenttext.$section.'</td>';
                  // Comment out the above three lines and uncomment the lines below if you do not want 'Week x' displayed on the right hand side of the toggle.
                //echo '<td colspan="3"><a id="sectionatag-'.$section.'" href="#" onclick="toggle_week(this,'.$section.'); return false;"><span>';
                //echo $weekperiod;
                //echo '<br />'.html_to_text($thissection->name).'</span> - '.$toggletext.'</a></td>';                
            }
            echo '</tr>';

            // Now the section itself.  The css class of 'hid' contains the display attribute that manipulated by the JavaScript to show and hide the section.  It is defined in js-override-topcoll.css which 
            // is loaded into the DOM by the JavaScript function weekcoll_init.  Therefore having a logical separation between static and JavaScript manipulated css.  Nothing else here differs from 
            // the standard Weeks format in the core distribution.  The next change is at the bottom.
            echo '<tr id="section-'.$section.'" class="section main'.$sectionstyle.'" style="display:none;">';
            echo '<td class="left side">'.$currenttext.$section.'</td>';
            // Comment out the above line and uncomment the line below if you do not want the section number displayed on the left hand side of the section.
            //echo '<td class="left side">&nbsp;</td>';
            
            echo '<td class="content">';
            if (!has_capability('moodle/course:viewhiddensections', $context) and !$thissection->visible) {   // Hidden for students
                echo $OUTPUT->heading($currenttext.$weekperiod.' ('.get_string('notavailable').')', 3, 'weekdates');
            } else {
                echo '<div class="summary">';
                if ($thissection->summary) {
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                    $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course','section', $thissection->id);
                    $summaryformatoptions = new stdClass();
                    $summaryformatoptions->noclean = true;
                    $summaryformatoptions->overflowdiv = true;
                    echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);
                }

                if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                    echo '<a title="'.$streditsummary.'" href="editsection.php?id='.$thissection->id.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/edit') . '" class="icon edit" alt="'.$streditsummary.'" /></a><br /><br />';
                }
                echo '</div>';

                print_section($course, $thissection, $mods, $modnamesused);

                if ($PAGE->user_is_editing()) {
                    print_section_add_menus($course, $section, $modnames);
                }
            }
            echo '</td>';
            echo '<td class="right side">';

            if ($displaysection == $section) {
                echo '<a href="view.php?id='.$course->id.'&amp;cweeks=0#section-'.$section.'" title="'.$strshowallweeks.'">'.
                     '<img src="'.$OUTPUT->pix_url('i/all') . '" class="icon wkall" alt="'.$strshowallweeks.'" /></a><br />';
            } else {
                $strshowonlyweek = get_string("showonlyweek", "", $section);
                echo '<a href="view.php?id='.$course->id.'&amp;cweeks='.$section.'" title="'.$strshowonlyweek.'">'.
                     '<img src="'.$OUTPUT->pix_url('i/one') . '" class="icon wkone" alt="'.$strshowonlyweek.'" /></a><br />';
            }

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                if ($thissection->visible) { // Show the hide/show eye
                    echo '<a href="view.php?id='.$course->id.'&amp;hide='.$section.'&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strweekhide.'">'.
                         '<img src="'.$OUTPUT->pix_url('i/hide') . '" class="icon hide" alt="'.$strweekhide.'" /></a><br />';
                } else {
                    echo '<a href="view.php?id='.$course->id.'&amp;show='.$section.'&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strweekshow.'">'.
                         '<img src="'.$OUTPUT->pix_url('i/show') . '" class="icon hide" alt="'.$strweekshow.'" /></a><br />';
                }
                if ($section > 1) { // Add a arrow to move section up
                    echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=-1&amp;sesskey='.sesskey().'#section-'.($section-1).'" title="'.$strmoveup.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/up') . '" class="icon up" alt="'.$strmoveup.'" /></a><br />';
                }

                if ($section < $course->numsections) { // Add a arrow to move section down
                    echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=1&amp;sesskey='.sesskey().'#section-'.($section+1).'" title="'.$strmovedown.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/down') . '" class="icon down" alt="'.$strmovedown.'" /></a><br />';
                }
            }
            echo '</td></tr>';
            echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
        }

        unset($sections[$section]);
        $section++;
        $weekdate = $nextweekdate;
    }
    
    if (!$displaysection and $PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
        // print stealth sections if present
        $modinfo = get_fast_modinfo($course);
        foreach ($sections as $section=>$thissection) {
            if (empty($modinfo->sections[$section])) {
                continue;
            }

            echo '<tr id="section-'.$section.'" class="section main clearfix stealth hidden">';
            echo '<td class="left side">';
            echo '</td>';
            echo '<td class="content">';
            echo $OUTPUT->heading(get_string('orphanedactivities'), 3, 'sectionname');
            print_section($course, $thissection, $mods, $modnamesused);
            echo '</td>';
            echo '<td class="right side">';
            echo '</td>';
            echo "</tr>\n";
        }
    }
    echo '</table>';

    if (!empty($sectionmenu)) {
        $select = new single_select(new moodle_url('/course/view.php', array('id'=>$course->id)), 'cweeks', $sectionmenu);
        $select->label = get_string('jumpto');
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        echo $OUTPUT->render($select);
    }
    
    // Establish persistance when  we have loaded!
    // Reload the state of the toggles from the data contained within the cookie.
    // Restore the state of the toggles from the cookie if not in 'Show week x' mode, otherwise show that topic.
    if ($displaysection == 0) {
        echo $PAGE->requires->js_function_call('reload_toggles',array($course->numsections));
        echo $PAGE->requires->js_function_call('set_current_week',array($theweek));
    } else {
        echo $PAGE->requires->js_function_call('show_week',array($displaysection));
    }