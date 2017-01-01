<?php

require '../private/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$pages = [
	'privacy-policy' => [ 'content' => '<b>SummonerNames.com</b> will <i>never</i> share any personal information from any user for any purpose.', 'class' => 'bg-warning' ],
	'disclaimer' => [ 'content' => '<b>SummonerNames.com</b> is not endorsed by Riot Games and does not reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends &copy; Riot Games, Inc.', 'class' => 'bg-warning' ],
	'cookie-policy' => [ 'content' => '<b>SummonerNames.com</b> uses cookies to save the most recent form input submitted. <a href="//support.google.com/adsense/answer/1348695">Additionally, Google Adsense may save some cookies.</a>', 'class' => 'bg-warning' ],
	'error' => [ 'content' => 'error', 'class' => 'bg-clear' ]
];

$colors = [
	'bg-clear' => 'transparent',
	'bg-primary' => '#337AB7',
	'bg-success' => '#DFF0D8',
	'bg-info' => '#D9EDF7',
	'bg-warning' => '#FCF8E3',
	'bg-danger' => '#F2DEDE'
];

$content = null;
if(isset($_SERVER['REDIRECT_URL']) && substr($_SERVER['REDIRECT_URL'], 0, 6) == '/page/') {
	$page = substr($_SERVER['REDIRECT_URL'], 6, -1);

	$valid = !empty($pages[$page]);

	$content = $pages[$valid ? $page : 'error']['content'];
	$class = $pages[$valid ? $page : 'error']['class'];
	$color = $colors[$class];
}

?><!DOCTYPE html>
<html lang="en" class="<?= $content ? 'server ' : ''; ?>js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">

	<script>
		document.documentElement.className += '-enabled';
	</script>

	<meta name="description" content="Find when your desired summoner name becomes available!">
	<meta name="author" content="Michael Grinspan">
	<meta name="keywords" content="lol inactive,lol,inactive,lolinactive,summoner names,summoner name,cleanup,checker">

	<title>SummonerNames.com<?= isset($page) ? ' | ' . ucwords(str_replace('-', ' ', $page)) : ''; ?></title>

	<link rel="stylesheet" href="/assets/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/main.css">

	<style id="css-hack"><?= $content ? '#form-container:after,.modal-footer:before{background-color:' . $color . ';}' : ''; ?></style>
</head>
<body>
	<div class="modal shown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">SummonerNames.com</h4>
					<span class="text-muted">Find the perfect summoner name!</span>
				</div>
				<div id="notice" class="modal-body">
					<p>We're out of beta!</p>
					<button id="feedback" class="btn btn-default">Submit Feedback</button>
				</div>
				<div id="js-alert" class="modal-body bg-danger">
					<p>This site is not functional without JavaScript enabled.<br>We are working on non-JavaSciprt support, but it may be a while.<br><a href="http://enable-javascript.com/">To learn how to enable JavaScript, click here.</a></p>
				</div>
				<div id="form-container" class="modal-body<?= $content ? ' modal-border ran' : ''; ?>">
					<form id="main">
						<div class="input-group">
							<span class="input-group-btn">
								<input id="server" type="button" name="server" value="<?= Servers::GetDefault(); ?>" class="btn btn-info">
								<div id="backdrop" class="dropdown-backdrop hidden"></div>
								<ul id="server-list" class="dropdown-menu">
									<?php
									
									foreach(Servers::GetList() as $short => $full) {
										echo '<li data-server="', $short, '"', (Servers::GetDefault() == $short ? ' class="selected"' : ''), '><a class="pointer">', $short, '&nbsp;<span class="small text-muted">', $full, '</span></a></li>';
									}
									
									?>
								</ul>
							</span>
							<input id="name" name="name" type="text" class="form-control" autocomplete="off" required>
							<span class="input-group-btn">
								<input type="submit" id="submit" value="Submit" class="btn btn-info">
							</span>
						</div>
					</form>
				</div>
				<div id="status" class="modal-body<?= $content ? ' ' . $class : ' remove-height'; ?>">
					<div id="status-container"><?= $content; ?></div>
				</div>
				<div class="modal-footer<?= $content ? ' ran' : ''; ?>">
					<table id="footer">
						<tr>
							<td><button id="recently-searched" class="btn btn-default pull-left">Recently Searched</button></td>
							<td><a href="/page/privacy-policy/"><button id="privacy-policy" class="btn btn-default">Privacy Policy</button></a></td>
						</tr>
						<tr>
							<td><a href="/page/disclaimer/"><button id="disclaimer" class="btn btn-default pull-left">Disclaimer</button></a></td>
							<td><a href="/page/cookie-policy/"><button id="cookie-policy" class="btn btn-default">Cookie Policy</button></a></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="text-sampling" class="modal shown">
		<div class="modal-dialog"></div>
	</div>
	
	<script src="/assets/jquery.min.js"></script>
	<script src="/assets/main.js"></script>
</body>
</html>
