<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="<?= $content ? 'server ' : ''; ?>js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<script>
		document.documentElement.className += '-enabled';
	</script>

	<meta name="description" content="Find the perfect summoner name!">
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

		<div id="amazon" class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body text-center">
					<p>Support us by buying RP on Amazon!</p>
					<div>
						@php
							$links = [
								'B014X427WM' => '8b589d4e3847095cbf557cfd0681b113',
								'B0153XBEBM' => 'f8e5c3de48ace044f342e6cdcd05decc',
								'B0153XHRPY' => '2f721430c722a2a981eef908e17dcd40',
								'B0153X7HKY' => 'af8997b11b9d9c68c2140aeaee47bef9',
							];
						@endphp

						@foreach($links as $asin => $linkId)
							<a target="_blank" href="https://www.amazon.com/gp/product/{{ $asin }}/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN={{ $asin }}&linkCode=as2&tag=summonernam0c-20&linkId={{ $linkId }}"><img border="0" src="//ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&MarketPlace=US&ASIN={{ $asin }}&ServiceVersion=20070822&ID=AsinImage&WS=1&Format=_SL160_&tag=summonernam0c-20"></a>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="text-sampling" class="modal shown">
		<div class="modal-dialog"></div>
	</div>

	<script src="/js/jquery.min.js"></script>
	<script src="/js/main.js"></script>
</body>
</html>
