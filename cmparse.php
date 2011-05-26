<?php

/**
* Campaignmonitor Template Parser
* Version 1.0
* By: Toby Evans - @t0bz
*
* This function is a *very* basic parser for CM template tags.
*
*
* Supported tags:
* title, description, unsubscribe, webversion, forwardtoafriend, preferences, fblike, tweet, repeatertitle
*
* Supported variables:
* imagesrc, firstname, lastname, fullname, email
*
* Semi-supported tags:
* repeater
*
*/

class cmparse {

	private $source = '';
	private $output = '';

	function __construct($source='') {
		$this->source = $source;
		return $this;
	}

	function run($repeater_count=3) {

		$this->output = $this->imagesrc($this->source);
		$this->output = $this->title($this->output);
		$this->output = $this->description($this->output);
		$this->output = $this->dates($this->output);
		$this->output = $this->variables($this->output);
		$this->output = $this->links($this->output);
		$this->output = $this->repeater_titles($this->output);
		$this->output = $this->repeaters($this->output, $repeater_count);

		return $this->output;
	}

	/**
	*
	*/
	static function imagesrc($source) {
		$search = array(
			'/(<img src=\")\<\$imagesrc(?: default=\')?(.+?)\'(?:\')?(?: link=\'true\')\$>(".{0,} \/>)/', // imagesrc with default & link
			'/(<img src=")<\$imagesrc(?: link=\'true\')+\$>"(?:.)+?(width=")([0-9]{1,})(") *(height=")([0-9]{1,})"*(.+?\/>)/u', // imagesrc with link only
			'/"\<\$imagesrc(?: link=\'true\')*\$\>(?:[a-zA-Z0-9\s,&;="])*width="([0-9]{1,})"(?: height="([0-9]{1,})")*/', // imagesrc
			'/([0-9{1,}])x\.png/',
			'/\<\$imagesrc(?: default=\')?([^\'\$]{1,})?(?:\')?\$\>/', // imagesrc with default
		);
		$replace = array(
			'<a href="#">$1$2$3</a>', // image src with link
			'<a href="#">$1http://placehold.it/$3x$6.png$4 $2$3$4 $5$6$4$7</a>', // use placehold.it with link
			'"http://placehold.it/$1x$2.png" width="$1" height="$2"', // use placehold.it
			'$1x150.png', // placehold.it with no height on image
			'$1', // image src without link
		);

		return preg_replace($search, $replace, $source);
	}

	/**
	*
	*/
	static function title($source) {
		$search = array(
			'(\<\$title(?: default=\')?([^\'\$]{1,})+(?:\')?(?: link=\'true\')\$\>)', // title with default & link
			'(\<\$title(?: default=\')?([^\'\$]+)+(?:\')\$\>)', // title with default
			'(\<\$title(?: link=\'true\')\$\>)', // title with link
			'(\<\$title\$\>)', // title
		);
		$replace = array(
			'<a href="#">$1</a>', // title
			'$1', // title
			'<a href="#">This is a title</a>', // title
			'This Is A Title', // title
		);

		return preg_replace($search, $replace, $source);
	}

	/**
	*
	*/
	static function description($source, $default=false) {
		if (!$default) $default = '<p>Pellentesque habitant morbi tristique senectus et malesuada ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>';

		$search = array(
			'/\<\$description (?:default=\')?([^\'\$]{1,})*(?:\')?\$\>/', // description with default
			'/\<\$description\$\>/', // description without default
		);
		$replace = array(
			'$1', // description with default
			$default, // description
		);

		return preg_replace($search, $replace, $source);
	}

	/**
	*
	*/
	static function dates($source) {
		$search = array(
			'/\<\$currentday\$\>/', // day number
			'/\<\$currentdayname\$\>/', // day name
			'/\<\$currentmonth\$\>/', // month number
			'/\<\$currentmonthname\$\>/', // month name
			'/\<\$currentyear\$\>/', // year
		);
		$replace = array(
			date('d'), // day (number)
			date('l'), // day (text)
			date('m'), // month (number)
			date('F'), // month (text)
			date('Y'), // year
		);

		return preg_replace($search, $replace, $source);
	}

	/**
	*
	*/
	static function links($source) {
		return preg_replace('/\<(unsubscribe|webversion|forwardtoafriend|preferences|fblike|tweet)\>(.{1,})\<\/(:?unsubscribe|webversion|forwardtoafriend|preferences|fblike|tweet)\>/', '<a href="#$1">$2</a>', $source);
	}

	/**
	*
	*/
	static function variables($source) {
		return preg_replace('/\[(?:firstname|lastname|fullname|email)(?:\,[\s]*fallback=)*(.{0,})\]/', '$1', $source);
	}

	/**
	*
	*/
	static function repeater_titles($source, $text='Repeater Title') {
		return preg_replace('/\<(?:\/)?(?:\$repeatertitle\$)\>/', $text, $source);
	}


	/**
	*
	*/
	static function repeaters($source, $count=3) {

		$search = array(
			'/\<repeater(?: toc=\'false\')*\>(.+?)\<\/repeater\>/us' // repeater, tableofcontents
		);
		$replace = array(
			str_repeat('$1', $count) // repeater
		);

		return preg_replace($search, $replace, $source);

	}

}

$template = (isset($_GET['template']) ? $_GET['template'] : 'template.html');
if ($template && file_exists($template)) {
	$parser = new cmparse(file_get_contents($template));
	echo $parser->run(2);
}
else {
	echo 'No Template Found!';
}


/* EOF */