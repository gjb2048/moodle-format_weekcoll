<?php
/**
 * Collapsed Weeks Information
 *
 * @package    course/format
 * @subpackage weekcoll
 * @copyright  2009-2011 @ G J Barnard in respect to modifications of standard weeks format.
 * @link       http://docs.moodle.org/en/Collapsed_Weeks_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */
 
 /**
 * This file is required if the course format is to support AJAX.
 */
$CFG->ajaxcapable = true;  // Please see CONTRIB-2975 for more information.
$CFG->ajaxtestedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0); // Used with ajaxenabled() in /lib/ajax/ajaxlib.php which uses check_browser_version in /lib/moodlelib.php which checks against $_SERVER['HTTP_USER_AGENT'];
?>
