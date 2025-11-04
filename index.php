<?php

const CACHE = true;

if(!isset($_GET['id'])) {
	header("HTTP/1.0 404 Avez-vous perdu votre poisson ?");
	readfile(__DIR__ . '/404.html');
	exit;
}

$CONFIG = json_decode(file_get_contents(__DIR__ . '/config.json'));


$cachefile = __DIR__ . '/temp/' . $_GET['id'] . '.dat';
if(CACHE && is_file($cachefile)) {
	$results = file_get_contents($cachefile);
} else {
	$chnd = curl_init('https://www.googleapis.com/calendar/v3/calendars/' . urlencode($CONFIG->CALENDAR_ID) . '/events/' . urlencode($_GET['id']) . '?key=' . urlencode($CONFIG->GOOGLE_API_KEY));
	curl_setopt_array($chnd,[
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
	]);

	$results = curl_exec($chnd);
	$code = curl_getinfo($chnd, CURLINFO_HTTP_CODE);
	curl_close($chnd);

	if($code != 200) {
		header("HTTP/1.0 404 Avez-vous perdu votre poisson ?");
		readfile(__DIR__ . '/404.html');
		exit;
	}

	if(CACHE && !is_dir(pathinfo($cachefile, PATHINFO_DIRNAME))) mkdir(pathinfo($cachefile, PATHINFO_DIRNAME), 0777, true);
	file_put_contents($cachefile, $results);


}











// echo '<pre>' . print_r($info, true) . '<pre>';
echo '<pre>' . print_r($results, true) . '<pre>';


// print_r($CONFIG);


// https://www.googleapis.com/calendar/v3/calendars/{calendarId}/events/{eventId}?key=YOUR_API_KEY



// echo "<pre>";
// print_r($_SERVER);
// echo "</pre>";