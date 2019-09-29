<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="<?= $content ? 'server ' : ''; ?>js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<script>
		document.documentElement.className += '-enabled';
	</script>

	<meta name="description" content="Find when your desired summoner name becomes available!">
	<meta name="author" content="Michael Grinspan">
	<meta name="keywords" content="summonernames,summoner names,lol inactive,lol,inactive,lolinactive,summoner names,summoner name,cleanup,checker">

	<title>SummonerNames.com<?= isset($page) ? ' | ' . ucwords(str_replace('-', ' ', $page)) : ''; ?></title>

	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/main.css">

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
				<div id="js-alert" class="modal-body bg-danger">
					<p>This site is not functional without JavaScript enabled.<br><a href="https://enable-javascript.com/">To learn how to enable JavaScript, click here.</a></p>
				</div>
				<div id="form-container" class="modal-body<?= $content ? ' modal-border ran' : ''; ?>">
					<form id="main">
						<div class="input-group">
							<div class="input-group-btn">
								<input id="server" type="button" name="server" value="<?= $defaultServer; ?>" class="btn btn-info">
								<div id="backdrop" class="dropdown-backdrop hidden"></div>
								<ul id="server-list" class="dropdown-menu">
									<?php

									foreach($servers as $short => $full) {
										echo '<li data-server="', $short, '"', ($defaultServer == $short ? ' class="selected"' : ''), '><a class="pointer">', $short, '&nbsp;<span class="small text-muted">', $full, '</span></a></li>';
									}

									?>
								</ul>
							</div>
							<input id="name" name="name" type="text" class="form-control" autocomplete="off" title="name">
							<span class="input-group-btn">
                                <input type="submit" id="submit" value="Search" class="btn btn-info">
                            </span>
						</div>
					</form>
				</div>
				<div id="status" class="modal-body<?= $content ? ' ' . $class : ' remove-height'; ?>">
					<div id="status-container"><?= $content; ?></div>
				</div>
				<div class="modal-footer<?= $content ? ' ran' : ''; ?>">
					<div id="footer">
						<div>
							<button id="recently-searched" class="btn btn-default pull-left">Recently Searched</button>
							<button id="feedback" class="btn btn-default">Submit Feedback</button>
						</div>
						<div id="footer-second-row">
							<a href="/page/disclaimer/">
								<button id="disclaimer" class="btn btn-default pull-left">Disclaimer</button>
							</a>
							<a href="/page/privacy-policy/">
								<button id="privacy-policy" class="btn btn-default">Privacy Policy</button>
							</a>
							<a href="/page/cookie-policy/">
								<button id="cookie-policy" class="btn btn-default pull-right">Cookie Policy</button>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="amzn-assoc-ad-00c3b93e-6285-4565-b86c-b2d18cf83447"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="text-sampling" class="modal shown">
		<div class="modal-dialog"></div>
	</div>

	<script src="/js/jquery.min.js"></script>
	<script src="/js/main.js"></script>

	<script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=00c3b93e-6285-4565-b86c-b2d18cf83447"></script>
</body>
</html>
