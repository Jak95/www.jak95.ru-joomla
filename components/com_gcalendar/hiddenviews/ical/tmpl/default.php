<?php

error_reporting(0);
@ini_set(‘display_errors’, 0);
include_once(JPATH_BASE.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'ical'.DS.'class.iCal.inc.php');

$days = (array) array (2,3);
$organizer = (array) array('ORGANIZER', 'ORgANIZER EMAIL ADRESS');
$categories = array('','');
$attendees = (array) array();
$fb_times = (array) array();
$alarm = (array) array();

$loc = JRequest::getVar('location');
$loc = utf8_decode($loc);
$ext_start = JRequest::getInt('start');
$ext_end = JRequest::getInt('end');
$title = JRequest::getVar('title');
$title = utf8_decode($title);

$ex_dates = (array) array(12345667,78643453);

$iCal = (object) new iCal('', 1, ''); // (ProgrammID, Method (1 = Publish | 0 = Request), Download Directory)

$iCal->addEvent(
	$organizer, // Organizer
	$ext_start, // Start Time (timestamp; for an allday event the startdate has to start at YYYY-mm-dd 00:00:00)
	$ext_end, // End Time (write 'allday' for an allday event instead of a timestamp)
	$loc, // Location
	0, // Transparancy (0 = OPAQUE | 1 = TRANSPARENT)
	$categories, // Array with Strings
					'', // Description
	$title, // Title
	1, // Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
	$attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
	0, // Priority = 0-9
	0, // frequency: 0 = once, secoundly - yearly = 1-7
	0, // recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
	0, // Interval for frequency (every 2,3,4 weeks...)
	$days, // Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
	0, // Startday of the Week ( 0 = Sunday - 6 = Saturday)
					'', // exeption dates: Array with timestamps of dates that should not be includes in the recurring event
	$alarm,  // Sets the time in minutes an alarm appears before the event in the programm. no alarm if empty string or 0
	1, // Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
					'', // optional URL for that event
					GCalendarUtil::getFrLanguage(), // Language of the Strings
	                '' // Optional UID for this event
);
$iCal->outputFile('ics'); // output file as ics (xcs and rdf possible)
?>