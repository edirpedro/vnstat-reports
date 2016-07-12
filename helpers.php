<?php
	
/*
Average Range: I didn't see any usefull information here, it's is a calc (rx + tx / 86399) where 86399 is a timestamp mktime(23,59,59) - mktime(0,0,0)
*/

$config = require 'config.php';

// Configurating locale
setlocale(LC_TIME, $config->locale);
$translations = array();
$translation_file = 'lang/' . $config->locale . '.php';
if(file_exists($translation_file)) {
	$translations = require $translation_file;
}

// Setting default interface
if(empty($_GET['interface']))
	$_GET['interface'] = 'all';

// Echo translation
function _e($text) {
	global $translations;
	echo !empty($translations) && array_key_exists($text, $translations) ? $translations[$text] : $text;
}

// Get translation
function _g($text) {
	global $translations;
	return array_key_exists($text, $translations) ? $translations[$text] : $text;
}

// Return interface data, one or all of them
function get_interface($interface) {
	global $config;
	
	$interface = $interface == 'all' ? join('+', array_keys($config->interfaces)) : $interface;
	$result = exec($config->vnstat . " --json -i $interface");
	if(!is_json($result))
		die("Could not load JSON data from vnStat ($result)");
	$result = json_decode($result);

	// Traffic	
	$data = $result->interfaces[0];

	// Top 10 results
	$data->traffic->tops = build_tops($data->traffic->tops);
	
	// 24 Hours results
	$data->traffic->hours = build_hours($data->traffic->hours);
	
	// Averages
	$data->traffic->averages = build_averages($data->traffic);
	
	//print_r($data);	
	return $data;
}

// Rebuild Top list and join data when all interfaces
function build_tops($tops) {
	global $config;
	
	if($_GET['interface'] == 'all') {
		// Join Tops data
		$data = array();
		foreach($config->interfaces as $interface => $name) {
			$result = json_decode(exec($config->vnstat . " --json -i $interface"));	
			foreach($result->interfaces[0]->traffic->tops as $item) {
				$timestamp = mktime(0, 0, 0, $item->date->month . $item->date->day . $item->date->year);
				$rx = $item->rx;
				$tx = $item->tx;
				if(array_key_exists($timestamp, $data)) {
					$rx += $data[$timestamp]->rx;
					$tx += $data[$timestamp]->tx;
				}
				$data[$timestamp] = (object) array(
					'id' => $item->id,
					'date' => $item->date,
					'time' => $item->time,
					'rx' => $rx,
					'tx' => $tx
					);
			}
		}
		
		// Sorting by total
		usort($data, function ($a, $b) {
			$a = $a->rx + $a->tx;
			$b = $b->rx + $b->tx;
			if($a == $b) return 0;
			return $a > $b ? -1 : 1;
		});
		
		// Getting 10 firsts
		$tops = array_slice($data, 0, 10);
	}

	return $tops;
}

// Rebuild Hours list
function build_hours($hours) {

	// Build array with all hours
	$data = array();
	$now = (int) date('H');
	$h = range(0,23);
	rsort($h);
	foreach($h as $hour) {
		$timestamp = $hour > $now ? strtotime('-1 day') : time();
		$data[$hour] = (object) array(
			'id' => $hour,
			'date' => (object) array(
				'year' => date('Y', $timestamp),
				'month' => date('m', $timestamp),
				'day' => date('d', $timestamp)
			),
			'rx' => 0,
			'tx' => 0
			);
	}
	$a = array_slice($data, -$now - 1, null, true);
	$b = array_slice($data, 0, 23 - $now, true);
	$data = array_replace($a, $b);

	// Fill the array
	foreach($hours as $item) {
		$data[$item->id]->date = $item->date;
		$data[$item->id]->rx += $item->rx;
		$data[$item->id]->tx += $item->tx;
	}
	
	return $data;
}

// Build averages
// TODO: estimate is much better
function build_averages($data) {
	
	// Day
	$day_rx = 0;
	$day_tx = 0;
	foreach($data->days as $key => $day) {
		$day_rx += $day->rx;
		$day_tx += $day->tx;
	}
	$day_rx /= count($data->days);
	$day_tx /= count($data->days);
	
	// Month
	$month_rx = 0;
	$month_tx = 0;
	foreach($data->months as $key => $month) {
		$month_rx += $month->rx;
		$month_tx += $month->tx;
	}
	$month_rx /= count($data->months);
	$month_tx /= count($data->months);
	
	// Return values
	return (object) array(
		'day' => (object) array(
			'rx' => $day_rx,
			'tx' => $day_tx
			),
		'month' => (object) array(
			'rx' => $month_rx,
			'tx' => $month_tx
			)
		);
}

// Echo the day formatted
function the_day($date) {
	echo get_day($date);
}

// Get the day formatted
function get_day($date) {
	global $config;
	return strftime($config->day_format, mktime(0, 0, 0, $date->month, $date->day, $date->year));		
}

// Echo the month formatted
function the_month($date) {
	echo get_month($date);
}

// Get the month formatted
function get_month($date) {
	global $config;
	return strftime($config->month_format, mktime(0, 0, 0, $date->month, 1, $date->year));	
}

// Echo the hour formatted
function the_hour($hour) {
	echo get_hour($hour);
}

// Get the hour formatted
function get_hour($hour) {
	global $config;
	return strftime($config->hour_format, mktime($hour->id, 0, 0, $hour->date->month, $hour->date->day, $hour->date->year));
}

// Echo the traffic formatted
function the_traffic($bytes, $precision = 2) {
	echo get_traffic($bytes, $precision);
}

// Get the traffic formatted
function get_traffic($bytes, $precision = 2) {
	$bytes *= 1024;
	$units = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];
	$pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow   = min($pow, count($units) - 1);
	$bytes /= pow(1024, $pow);
	return round($bytes, $precision) . ' ' . $units[$pow];
}

// Check witch interface is selected
function is_interface($id) {
	echo $_GET['interface'] == $id ? 'btn-primary' : 'btn-default';
}

// Check if is a JSON string
function is_json($string) {
	json_decode($string);
	return json_last_error() == JSON_ERROR_NONE;
}

// Write the Updates time for each interface
function the_interfaces_updates() {
	global $config;
	$result = get_json();
	$updates = array();
	foreach($result->interfaces as $interface) {
		if(in_array($interface->id, array_keys($config->interfaces))) {
			$date = $interface->updated->date;
			$time = $interface->updated->time;
			$update = strftime($config->day_format, mktime($time->hour, $time->minutes, 0, $date->month, $date->day, $date->year));
			$name = $config->interfaces[$interface->id];
			$updates[] = "$name in $update";
		}
	}	
	echo join(', ', $updates);
}

// Retrieve Summary
function get_summary($traffic) {
		
	$result = (object) array(
		'today' => (object) array(
			'tx' => 0,
			'rx' => 0,
			'cx' => 0
		),
		'yesterday' => (object) array(
			'tx' => 0,
			'rx' => 0,
			'cx' => 0
		),
		'month' => (object) array(
			'tx' => 0,
			'rx' => 0,
			'cx' => 0
		),
		'last_month' => (object) array(
			'tx' => 0,
			'rx' => 0,
			'cx' => 0
		),
		'total' => (object) array(
			'tx' => $traffic->total->tx,
			'rx' => $traffic->total->rx,
			'cx' => 0
		),
	);	
	// Today
	if(!empty($traffic->days[0])) {
		$result->today->rx = $traffic->days[0]->rx;
		$result->today->tx = $traffic->days[0]->tx;
	}
	
	// Yesterday
	if(!empty($traffic->days[1])) {
		$result->yesterday->rx = $traffic->days[1]->rx;
		$result->yesterday->tx = $traffic->days[1]->tx;
	}
	
	// Compare Today and Yesterday
	$today = $result->today->rx + $result->today->tx;
	$yesterday = $result->yesterday->rx + $result->yesterday->tx;
	if($today > $yesterday) {
		$result->today->cx = 0;
		$result->yesterday->cx = $today - $yesterday;
	} else {
		$result->today->cx = $yesterday - $today;
		$result->yesterday->cx = 0;
	}
	if($today == 0 && $yesterday == 0) {
		$result->today->cx = 100;
		$result->yesterday->cx = 100;
	}
	
	// This Month
	if(!empty($traffic->months[0])) {
		$result->month->rx = $traffic->months[0]->rx;
		$result->month->tx = $traffic->months[0]->tx;
	}
	
	// Last Month
	if(!empty($traffic->months[1])) {
		$result->last_month->rx = $traffic->months[1]->rx;
		$result->last_month->tx = $traffic->months[1]->tx;
	}

	// Compare Month and Last Month
	$month = $result->month->rx + $result->month->tx;
	$last_month = $result->last_month->rx + $result->last_month->tx;
	if($month > $last_month) {
		$result->month->cx = 0;
		$result->last_month->cx = $month - $last_month;
	} else {
		$result->month->cx = $last_month - $month;
		$result->last_month->cx = 0;
	}
	if($month == 0 && $last_month == 0) {
		$result->month->cx = 100;
		$result->last_month->cx = 100;
	}

	return $result;
}

// Echo the vnStat version
function the_vnstat_version() {
	global $config;
	$version = exec($config->vnstat . ' -v');
	echo '<a href="https://github.com/vergoh/vnstat" target="_blank">' . $version . '</a>';
}

?>