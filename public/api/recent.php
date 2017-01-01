<?php

require '../../private/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(dirname(__DIR__)));
$dotenv->load();

$db = new Database;

$db
	->select([
		'name',
		'server'
	])
	->from('history')
	->where('date_searched > DATE_ADD(NOW(), INTERVAL -1 MONTH)')
	->orderBy(['date_searched DESC'])
	->limit(10);

header('Content-Type: application/json');

exit(json_encode($db->execute(), JSON_NUMERIC_CHECK));
