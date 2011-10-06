//  $Id: lib.js,v 1.8 2011/05/30 17:38:02 gb2048 Exp $
/**
 * Collapsed Weeks Information
 *
 * @package    course/format
 * @subpackage weekcoll
 * @copyright  2009-2011 @ G J Barnard.
 * @link       http://docs.moodle.org/en/Collapsed_Weeks_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */

// Global variables 
var toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000"; // 53 possible toggles - current settings in Moodle for number of weeks - 52 + 1 for week 0.  Need 1 as Most Significant bit to allow toggle 1+ to be off.
var thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is because cookies need to be compact.
var thewwwroot;  // For the toggle graphic and extra files.
var thecookiesubid; // For the cookie sub name.
var numToggles;
var currentWeek;
var cookieExpires;

// Because I like the idea of private and public methods, public will have an underscore in the name.

// Initialise with the information supplied from the course format 'format.php' so we can operate.
// Args - wwwroot is the URL of the Moodle site, moodleid is the site short name (courseid 0) and courseid is the id of the current course to allow for settings for each course.
function weekcoll_init(wwwroot, moodleid, courseid, cookielifetime)
{
    // Init.
    thewwwroot = wwwroot;
    thecookiesubid = moodleid + courseid;
    cookieExpires = cookielifetime; // null indicates that it is a session cookie.
}

function set_current_week(theWeek)
{
    currentWeek = theWeek;
}

// Change the toggle binary global state as a toggle has been changed - toggle number 0 should never be switched as it is the most significant bit and represents the non-toggling week 0.
// Args - toggleNum is an integer and toggleVal is a string which will either be "1" or "0"
function togglebinary(toggleNum, toggleVal)
{
    // Toggle num should be between 1 and 52 - see definition of toggleBinaryGlobal above.
    if ((toggleNum >=1) && (toggleNum <= 52))
    {
        // Safe to use.  So recreate the string containing the state of the toggles.
        var start = toggleBinaryGlobal.substring(0,toggleNum); // Do not need to add one to toggleNum for indexing as we are ignoring index 0 for MSB purposes.
        var end = toggleBinaryGlobal.substring(toggleNum+1); // Get the rest of the string from the position after the toggle.
        var newval = start + toggleVal + end;
        
        toggleBinaryGlobal = newval;
        save_toggles();  // We have a change so save.
    }
}

// Toggle functions
// Args - target is the table row element in the DOM to be toggled.
//            image is the img tag element in the DOM to be changed.
//            toggleNum is the toggle number to change.
//            reloading is a boolean that states if the function is called from reload_toggles() so that we do not have to resave what we already know - ohh for default argument values.
function toggleexactweek(target,image,toggleNum,reloading)  // Toggle the target tr and change the image which is the a tag within the td of the tr above target
{
    if(document.getElementById)
    {
        if (navigator.userAgent.indexOf('IE')!= -1)
        {
            var displaySetting = "block";  // IE is always different from the rest!
        }
        else
        {
            var displaySetting = "table-row";
        }

        if (target.style.display == displaySetting)
        {
            target.style.display = "none";
            var visSetting = "hidden";
            image.style.backgroundImage = "url(" + thewwwroot + "/course/format/weekcoll/arrow_down.png)";
            // Save the toggle!
            if (reloading == false)    togglebinary(toggleNum,"0");
        }
        else
        {
            target.style.display = displaySetting;
            var visSetting = "visible";
            image.style.backgroundImage = "url(" + thewwwroot + "/course/format/weekcoll/arrow_up.png)";
            // Save the toggle!
            if (reloading == false) togglebinary(toggleNum,"1");
        }

        if (navigator.userAgent.indexOf('IE')!= -1)
        {
            var embeds = target.getElementsByTagName('embed');
            if (embeds[0] != null)
            {
                embeds[0].style.visibility = visSetting;
            }
        }
    }
}

// Called by the html code created by format.php on the actual course page.
// Args - toggler the tag that initiated the call, toggleNum the number of the toggle for which toggler is a part of - see format.php.
function toggle_week(toggler,toggleNum)
{
    if(document.getElementById)
    {
        imageSwitch = toggler;
        targetElement = toggler.parentNode.parentNode.nextSibling; // Called from a <td> inside a <tr> so find the next <tr>.

        toggleexactweek(targetElement,imageSwitch,toggleNum,false);
    }
}

// Current maximum number of weeks is 52, but as the converstion utilises integers which are 32 bit signed, this must be broken into two string segments for the
// process to work.  Therefore each 6 character base 36 string will represent 26 characters for part 1 and 27 for part 2 in base 2.
// This is all required to save cookie space, so instead of using 53 bytes (characters) per course, only 12 are used.
// Convert from a base 36 string to a base 2 string - effectively a private function.
// Args - thirtysix - a 12 character string representing a base 36 number.
function to2baseString(thirtysix)
{
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 53 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(thirtysix.substring(0,6),36);
    var secondpart = parseInt(thirtysix.substring(6,12),36);
    var fps = firstpart.toString(2);
    var sps = secondpart.toString(2);
    
    // Add in preceding 0's if base 2 sub strings are not long enough
    if (fps.length < 26)
    {
        // Need to PAD.
        fps = thesparezeros.substring(0,(26 - fps.length)) + fps;
    }
    if (sps.length < 27)
    {
        // Need to PAD.
        sps = thesparezeros.substring(0,(27 - sps.length)) + sps;
    }
    
    return fps + sps;
}

// Convert from a base 2 string to a base 36 string - effectively a private function.
// Args - two - a 52 character string representing a base 2 number.
function to36baseString(two)
{
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 52 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(two.substring(0,26),2);
    var secondpart = parseInt(two.substring(26,53),2);
    var fps = firstpart.toString(36);
    var sps = secondpart.toString(36);

    // Add in preceding 0's if base 36 sub strings are not long enough
    if (fps.length < 6)
    {
        // Need to PAD.
        fps = thesparezeros.substring(0,(6 - fps.length)) + fps;
    }
    if (sps.length < 6)
    {
        // Need to PAD.
        sps = thesparezeros.substring(0,(6 - sps.length)) + sps;
    }

    return fps + sps;
}

// Cookie Monster
// Args - value to save to the cookie
function saveweekcollcookie(value)
{
    //create a YUI instance and use the cookie module. 
    if (cookieExpires == null)
    {
        // Session Cookie...
        YUI().use('cookie', function(Y){ 
           Y.Cookie.setSub("mdl_cf_weekcoll",thecookiesubid,value); 
           //alert("Bongo After " + thecookiesubid + " " + value);
        });
        // Using Sub cookies, so, name, moodleid/courseid, value.
        // This is not a Moodle table but in fact the cookies name.
    }
    else
    {
        // Expiring Cookie...
        YUI().use('cookie', function(Y){ 
            var newDate = new Date();
            newDate.setTime(newDate.getTime() + cookieExpires);
            Y.Cookie.setSub("mdl_cf_weekcoll",thecookiesubid,value, { expires: newDate }); 
           //alert("Bongo After " + thecookiesubid + " " + value);
        });
        // This is not a Moodle table but in fact the cookies name.
    }
}

// 'Private' version of reload_toggles
function reloadToggles()
{
    YUI().use('cookie', function(daYUI){ 
        // Get the cookie if there!
        var storedval = daYUI.Cookie.getSub("mdl_cf_weekcoll",thecookiesubid);
        if (storedval != null)
        {
            toggleBinaryGlobal = to2baseString(storedval);
        }
    
        for (var theToggle = 1; theToggle <= numToggles; theToggle++)
        {
            if ((theToggle <= numToggles) && ((toggleBinaryGlobal.charAt(theToggle) == "1") || (theToggle == currentWeek))) // Array index 0 is never tested - MSB thing.
            {
                toggleexactweek(document.getElementById("section-"+theToggle),document.getElementById("sectionatag-" + theToggle),theToggle,true);
            }
        }    
    });
}

// Toggle persistence functions
// Reload the toggles - called from an onload event handler setup at the bottom of format.php
// aToggle sets the number of toggles we have on this course so that when restoring the state we do not attempt to set something that
// no longer exists.  This can happen when the number of sections is reduced and we return to the course and reload the page
// using the data from the cookie.
function reload_toggles(aToggle)
{
    numToggles = aToggle;
    
    YUI().use('node-base', function(daYUI) {
     daYUI.on("domready", reloadToggles);
    });
}

// Show a specific week - used when in 'Show week x' mode.
function show_week(theWeek)
{
    toggleexactweek(document.getElementById("section-"+theWeek),document.getElementById("sectionatag-" + theWeek),theWeek,true);
}

// Save the toggles - called from togglebinary and an the unload event handler at the bottom of format.php which does not work for a refresh even though it should!
function save_toggles()
{
    saveweekcollcookie(to36baseString(toggleBinaryGlobal));
}