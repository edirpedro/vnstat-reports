<?php


return (object) array(
	
	// Location of vnStat, like /usr/bin/vnstat
	'vnstat' => 'vnstat',
	
	// Your Locale, like en_US
	'locale' => 'en_US',
	
	// Date formats, for references visit http://php.net/strftime
	'day_format' => '%a, %b %d %Y',
	'month_format' => '%B %Y',
	'hour_format' => '%kh',
	
	// Interfaces and their names, like eth0 => Cable
	'interfaces' => array(
		'en0' => 'Cable',
		'en1' => 'Wi-Fi'
	)

);
	
?>