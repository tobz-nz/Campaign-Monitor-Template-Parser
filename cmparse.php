<?php

/**
* Campaignmonitor Template Parser
* Version 1.0
*
* This function is a *very* basic parser for CM template tags.
*
*
* Supported tags:
* title, description, unsubscribe, webversion, forwardtoafriend, preferences, fblike, tweet
*
* Supported variables:
* imgsrc, firstname, lastname, fullname, email
*
*
*/
function cm_parse($html) {

	$searches = array(
		'(\<\$title(?: default=\')?([^\'\$]{1,})+(?:\')?(?: link=\'true\')\$\>)', // title with default & link
		'(\<\$title(?: default=\')?([^\'\$]+)+(?:\')\$\>)', // title with default
		'(\<\$title(?: link=\'true\')\$\>)', // title with link
		'(\<\$title\$\>)', // title
		'/\<\$description (?:default=\')?([^\'\$]{1,})*(?:\')?\$\>/', // description with default
		'/\<\$description\$\>/', // description without default
		'/(<img src=\")\<\$imagesrc(?: default=\')?([^\'\$]{1,})?(?:\')?(?: link=\'true\')\$>(".{0,} \/>)/', // imagesrc with default & link
		'/"\<\$imagesrc(?: link\=\'true\')*\$\>(?:[a-zA-Z0-9\s,&;\="])*width\="([0-9]{1,})"(?: height\="([0-9]{1,})")*/', // imagesrc
		'/\<\$imagesrc(?: default=\')?([^\'\$]{1,})?(?:\')?\$\>/', // imagesrc with default
		'/\<(?:\/)?(?:repeater|repeatertitle|tableofcontents)\>/', // repeater, tableofcontents
		'/\<(?:\/)?(?:\$repeatertitle\$)\>/', // repeater, tableofcontents
		'/\<\$currentday\$\>/', // day number
		'/\<\$currentdayname\$\>/', // day name
		'/\<\$currentmonth\$\>/', // month number
		'/\<\$currentmonthname\$\>/', // month name
		'/\<\$currentyear\$\>/', // year
		'/\<(unsubscribe|webversion|forwardtoafriend|preferences|fblike|tweet)\>(.{1,})\<\/(:?unsubscribe|webversion|forwardtoafriend|preferences|fblike|tweet)\>/', // special links
		'/\<repeater\>(.{1,})\<\/repeater\>/', // repeater
		'/\[(?:firstname|lastname|fullname|email)(?:\,[\s]*fallback=)*(.{0,})\]/' // some simple variables
	);

	$replaces = array(
		'<a href="#">$1</a>', // title
		'$1', // title
		'<a href="#">This is a title</a>', // title
		'This Is A Title', // title
		'$1', // description with default
		'<p>Pellentesque habitant morbi tristique senectus et malesuada ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>', // description
		'<a href="#">$1$2$3</a>', // image src with link
		'"http://placehold.it/$1x$2.png" width="$1" height="$2"', // image src without link
		'$1', // image src without link
		'', // repeater, tableofcontents (just remove them)
		'Repeater Title', // repeatertitle
		date('d'), // day (number)
		date('l'), // day (text)
		date('m'), // month (number)
		date('F'), // month (text)
		date('Y'), // year
		'<a href="#$1">$2</a>', // webversion, forwardtoafriend, preferences, fblike, tweet
		'$1', // repeater, tableofcontents
		'$1' // firstname, lastname, fullname, email
	);

	// $output = $html;
	$output = preg_replace($searches, $replaces, $html);

	return $output;
}

$template = (isset($_GET['template']) ? $_GET['template'] :'template.html');
$output = '';
if (file_exists($template)) {
	$output = cm_parse(file_get_contents($template));
}
else {
	$output = 'No Template Found!';
}

echo  ($output?$output:'No Output');


/* EOF */