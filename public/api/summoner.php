<?php

require '../../private/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(dirname(__DIR__)));
$dotenv->load();

header('Content-Type: application/json');

if(isset($_GET['name'], $_GET['server'])) {
	$api = new ApiResponse($_GET['name'], $_GET['server']);

	exit(json_encode($api->getResponse()));
}

exit('{"error":true}');

?>
