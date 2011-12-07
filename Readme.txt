$Id: Readme.txt,v 1.1.4.16 2011/10/06 14:31:55 gb2048 Exp $

Introduction
------------
Week based course format with an individual 'toggle' for each week except 0.  The current week is always shown.

This version works with Moodle 1.9.

Documented on http://docs.moodle.org/en/Collapsed_Weeks_course_format

Installation
------------
1. Copy 'weekcoll' to /course/formats/
2. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it needs to be done.
3. If desired, edit the colours of the weeks_collapsed.css - which contains instructions on how to have per theme colours.
4. To change the arrow graphic you need to replace arrow_up.png and arrow_down.png.  Reuse the graphics
   if you want.  Created in Paint.Net.

Upgrade Instructions
--------------------
1. Put Moodle in Maintenance Mode so that there are no users using it bar you as the adminstrator.
2. In /course/formats/ move old 'weekcoll' directory to a backup folder outside of Moodle.
3. Follow installation instructions above.
4. Put Moodle out of Maintenance Mode.


Remembered Toggle State Instructions
------------------------------------
To have the state of the toggles be remembered beyond the session for a user (stored as a cookie in the user's 
web browser local storage area), edit format.php and find the following at the towards the top...

<script type="text/javascript">
//<![CDATA[
    weekcoll_init('<?php echo $CFG->wwwroot ?>',
                  '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                  '<?php echo $course->id ?>',
                  null); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

The word to change is 'null' which says to create a 'session cookie' for the toggle state.  There are several
predefined durations available: 'aSecond', 'aMinute', 'anHour', 'aDay', 'aWeek', 'aMonth' and 'aYear'.  For
example a remembered state of a week would be:

<script type="text/javascript">
//<![CDATA[
    weekcoll_init('<?php echo $CFG->wwwroot ?>',
                  '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                  '<?php echo $course->id ?>',
                  aWeek); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

You can combine the durations together and perform mathematical operations, for example, to have a
duration in the future of one day 38 minutes and 30 seconds you would have:

<script type="text/javascript">
//<![CDATA[
    weekcoll_init('<?php echo $CFG->wwwroot ?>',
                  '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                  '<?php echo $course->id ?>',
                  aDay + (aMinute * 38) + (aSecond * 30)); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

To revert back to session cookies, simply put back the word 'null'.

NOTE: The client's browser must support the persistent storage of cookies in the user's profile for this to work.  I realise that
      some configured systems do not allow this and therefore this mechanism will not work.  However, I anticipate that setting
      an expiring cookie will be fine as it will simply be deleted in environments where they are removed on log out, but will have
      use when the user is at home and remotely logs in.

Known Issues
------------

1.  If you get toggle text issues in languages other than English please ensure you have the latest version of Moodle installed.  More
    information on http://moodle.org/mod/forum/discuss.php?d=184150.
2.  AJAX drag and drop appears not to be working in IE 9 for me, but is in compatibility mode (IE 7) and same issue with the standard
    topics format too.  Hence I consider it to be either an issue with my system or Moodle Core.  If you experience it and wish to use
    the up and down arrows, edit ajax.php and remove "'MSIE' => 6.0," from:
    "$CFG->ajaxtestedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);"
    And if possible, please let me know, my Moodle.org profile is 'http://moodle.org/user/profile.php?id=442195'.

References
----------
.Net Magazine Issue 186 - Article on Collapsed Tables by Craig Grannell -
 http://www.netmag.co.uk/zine/latest-issue/issue-186

Craig Grannell - http://www.snubcommunications.com/

Accordion Format - Initiated the thought - http://moodle.org/mod/forum/discuss.php?d=44773 & 
                                           http://www.moodleman.net/archives/47

Paint.Net - http://www.getpaint.net/

JavaScript: The Definitive Guide - David Flanagan - O'Reilly - ISBN: 978-0-596-10199-2

Moodle Tracker - http://tracker.moodle.org/

Version Information
-------------------
14th September 2009 - Version 1 - Moodle Tracker CONTRIB-1562
  Based upon version 1.3.2 of topcoll.
  Please see the documentation on http://docs.moodle.org/en/Collapsed_Topics_course_format
  
23rd January 2010 - Version 1.1 - Moodle Tracker CONTRIB-1756
  1. Put instructions in the CSS file 'weeks_collapsed.css' on how you can define theme based toggle colours.
  2. Redesigned the arrow to be more 'modern'.  
  
16th February 2010 - Version 1.2 - Moodle Tracker CONTRIB-1825
  1. Removed the capability to 'Show week x' unless editing as confusing to users.
  2. Removed redundant 'aToggle' as existing $course->numsections already contained the correct figure
     and counting toggles that are displayed causes an issue when in 'Show week x' mode as the toggle
     number does not match the display number for the specific element.
  3. Removed redundant calls to 'get_context_instance(CONTEXT_COURSE, $course->id)' as result already
     stored in $context variable towards the top - so use in more places.

5th April 2010 - Version 1.2.1 - Moodle Tracker CONTRIB-1952 & CONTRIB-1954
  1. CONTRIB-1952 - Having an apostrophy in the site shortname causes the format to fail.
  2. CONTRIB-1954 - Reloading of the toggles by using JavaScript DOM events not working for the function reload_toggles,
     but instead the function was being called at the end of the page regardless of the readiness state of the DOM.  	 

9th April 2010 - Version 1.2.2 - Moodle Tracker CONTRIB-1973
  1. Tidied up format.php, made the fetching of week and toggle names more efficient and sorted a missing echo statement.
  2. Tidied up this file.
  
11th September 2010 - Version 1.2.3 - Moodle Tracker CONTRIB-2355
  1. Added the ability to remove 'topic x' and the section number from being displayed.  To do this, open up
     format.php in a text editor - preferably with line numbers displayed - such as Notepad++ - and read the 
     instructions on lines 239 and 252.  

7th November 2010 - Version 1.2.4 - Moodle Tracker CONTRIB-2497
  1. Added Dutch language.  Thanks to Pieter Wolters - http://moodle.org/user/profile.php?id=537037 - for this.
  2. Added German, French, Spanish (Spain, Mexico and International), Italian, Polish, Portuguese (Brazil too) 
     and Welsh.  I used Google Translate! If inaccurate, please let me know!
  3. Added the string 'weekcolltogglewidth' to the relevant language file and amended format.php so that
     the word 'Week' when translated fits within the toggle.

12th November 2010 - Version 1.2.4.1 - Moodle Tracker CONTRIB-2497
  1. Fixed issue with missing semi-colon in language file that appeared not to affect Moodle 1.9 as it
     did with the Moodle 2.0 version, but corrected anyway as a PHP syntax bug.

12th March 2011 - Version 1.5 - Moodle Tracker CONTRIB-2747
  1. Make the toggle state last beyond the user session if desired.
  2. Added id of "sectionblock-0" / "sectionblock-'.$section.'" for the left side, see MDL-18232.

16th March 2011 - Version 1.5.1 - Moodle Tracker CONTRIB-2747
  1. Quick fix for Internet Explorer as it does not understand the Javascript 'const' keyword!

30th May 2011 - Version 1.5.2 - Moodle Tracker CONTRIB-2963
  1. Added in copyright and contact information.

9th June 2011 - Version 1.5.3 - Moodle Tracker CONTRIB-2975 - Unfinished.
  1. AJAX support temporarily withdrawn due to issue with moving sections and the toggle title not following.
     Complex to resolve.

6th October 2011 - Version 1.6 - Moodle Tracker CONTRIB-2975, CONTRIB-3189 and CONTRIB-3190.
  1. CONTRIB-2975 - AJAX support reinstated after working out a way of swapping the content as well as the toggle.  Solution sparked off by
                    Amanda Doughty (http://tracker.moodle.org/secure/ViewProfile.jspa?name=amanda.doughty).
  2. CONTRIB-3189 - Reported by Benn Cass that text in IE8- does not hide when the toggle is closed, solution suggested
                    by Mark Ward (http://moodle.org/user/profile.php?id=489101) - please see http://moodle.org/mod/forum/discuss.php?d=183875.
  3. CONTRIB-3190 - In realising that to make CONTRIB-2975 easier to use I suggested 'Toggle all' functionality and the
                    community said it was a good idea with no negative comments, please see (http://moodle.org/mod/forum/discuss.php?d=176806).

11th October 2011 - Updated version.php to be fully populated.

7th December 2011 - Version 1.6.1 - Moodle Tracker CONTRIB-2497
  1. Updated Brazilian translation thanks to Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633).

Thanks
------
I would like to thank Anthony Borrow - arborrow@jesuits.net & anthony@moodle.org - for his invaluable input.

For the Peristence upgrade I would like to thank all those who contributed to the developer forum -
http://moodle.org/mod/forum/discuss.php?d=124264 - Frank Ralf, Matt Gibson, Howard Miller and Tim Hunt.  And
indeed all those who have worked on the developer documentation - http://docs.moodle.org/en/Javascript_FAQ.

Michael de Raadt for CONTRIB-1945 & 1946 which sparked fixes in CONTRIB-1952 & CONTRIB-1954

Amanda Doughty (http://moodle.org/user/profile.php?id=1062329) for her contribution in solving the AJAX move problem.

Mark Ward (http://moodle.org/user/profile.php?id=489101) for his contribution solving the IE8- display problem.

Pieter Wolters (http://moodle.org/user/profile.php?id=537037) - for the Dutch translation.

Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633) - for the Brazilian translation.

Desired Enhancements
--------------------

1. Smoother animated toggle action.
2. Moving 'window' date range functionality where the course shows only the weeks within a number of weeks before and
   after the current.
3. Use ordered lists / divs instead of tables to fall in line with current web design theory.  Older versions of
   'certain' browsers causing issues in making this happen.

G J Barnard - MSc, BSc(Hons)(Sndw), MBCS, CEng, CITP, PGCE - 7th December 2011