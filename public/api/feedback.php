<?php

if(!isset($_POST['message']) || !is_string($_POST['message']) || strlen($_POST['message']) > 1000) {
	http_response_code(400);
	exit;
}

require '../../private/vendor/autoload.php';

$db = new Database;

$db
	->insert([
		'message',
		'email',
		'ip'
	])
	->into('feedback')
	->bindValues([
		'message' => $_POST['message'],
		'email' => (isset($_POST['email']) ? substr($_POST['email'], 0, 100) : null),
		'ip' => $_SERVER['REMOTE_ADDR']
	]);

$db->execute();

?>
