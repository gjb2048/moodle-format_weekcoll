<?php // $Id: lib.php,v 1.8 2011/10/06 15:45:14 gb2048 Exp $
/**
 * Collapsed Weeks Information
 *
 * A week based format that solves the issue of the 'Scroll of Death' when a course has many weeks. All
 * weeks have a toggle that displays that week. The current week is displayed by default. One or more
 * weeks can be displayed at any given time. Toggles are persistent on a per browser session per course
 * basis but can be made to perist longer by a small code change. Full installation instructions, code
 * adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage weekcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard weeks format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Weeks_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_weekcoll_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=Collapsed Weeks
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Collapsed Weeks.
 *
 * @param navigation_node $navigation The course node
 * @param array $path An array of keys to the course node
 * @param stdClass $course The course we are loading the section for
 */
function callback_weekcoll_load_content(&$navigation, $course, $coursenode) {
	return $navigation->load_generic_course_sections($course, $coursenode, 'weekcoll');
}

/**
 * The string that is used to describe a section of the course
 *
 * @return string
 */
function callback_weekcoll_definition() {
    return get_string('sectionname','format_weekcoll');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_weekcoll_request_key() {
    return 'weekcoll';
}

/**
 * Gets the name for the provided section.
 *
 * @param stdClass $course
 * @param stdClass $section
 * @return string
 */
function callback_weekcoll_get_section_name($course, $section) {
    // We can't add a node without text
    if (!empty($section->name)) {
        // Return the name the user set
        return format_string($section->name, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));  // MDL-29188
    } else if ($section->section == 0) {
        // Return the section0name
        return get_string('section0name', 'format_weekcoll');
    } else {
        // Got to work out the date of the week so that we can show it
        $sections = get_all_sections($course->id);
        $weekdate = $course->startdate+7200;
        foreach ($sections as $sec) {
            if ($sec->id == $section->id) {
                break;
            } else if ($sec->section != 0) {
                $weekdate += 604800;
            }
        }
        $strftimedateshort = ' '.get_string('strftimedateshort');
        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate+518400, $strftimedateshort);
        return $weekday.' - '.$endweekday;
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_weekcoll_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;  // See CONTRIB-2975 for information on how fixed.
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0); 
    return $ajaxsupport;
}
