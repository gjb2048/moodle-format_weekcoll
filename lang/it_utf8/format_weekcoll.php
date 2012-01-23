<?php
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

// Italian Translation of Collapsed Weeks Course Format
// Traduzione italiana di Collapsed Corso Format Settimane

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
// Utilizzato dal Moodle per identificare i il formato e la visualizzazione nella lista dei formati per un corso di sue impostazioni.
$string['nameweekcoll']='Compresso Settimane';
$string['formatweekcoll']='Compresso Settimane';
$string['pluginname'] = 'Compresso Settimane';

// Used in format.php
// Utilizzato in format.php
$string['weekcolltoggle']='Olivetta';
$string['weekcolltogglewidth']='width: 52px;';

// Toggle all - Moodle Tracker CONTRIB-3190
$string['weekcollall']='';
$string['weekcollopened']="Aperto tutto l'alterna.";
$string['weekcollclosed']='Chiudere tutte le alterna.';
?>