;(function () {
	var templates = {
		static: {
			loading: {
				html: '<b>Loading...</b>',
				className: 'bg-clear'
			},

			error: {
				html: 'An error has occurred, please try again soon.',
				className: 'bg-danger'
			},

			disclaimer: {
				html: '<b>SummonerNames.com</b> is not endorsed by Riot Games and does not reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends &copy; Riot Games, Inc.',
				className: 'bg-warning'
			},

			privacyPolicy: {
				html: '<b>SummonerNames.com</b> will <i>never</i> share any personal information from any user with any third party for any purpose.',
				className: 'bg-warning'
			},

			cookiePolicy: {
				html: '<b>SummonerNames.com</b> uses cookies to save your most recent search. <a href="//support.google.com/adsense/answer/1348695">Additionally, Google AdSense may save some cookies.</a>',
				className: 'bg-warning'
			},

			feedbackSent: {
				html: 'Your feedback has been sent!',
				className: 'bg-success'
			},

			messageTooLong: {
				html: 'Please shorten your feedback!',
				className: 'bg-danger'
			}
		},

		dynamic: {
			available: {
				html: function (data) {
					return '<b>' + data.name + ' (' + data.server + ')</b> is currently available!';
				},
				className: 'bg-success'
			},

			unavailable: {
				html: function (data) {
					return '<b>' + data.name + ' (' + data.server + ')</b> will be available at <b>' + data.time + '</b> on <b>' + data.date + ' (' + data.timezone + ')</b>';
				},
				className: 'bg-info'
			},

			recentlySearched: {
				html: function (data) {
					var $html;
					var $tbody;

					if (!data.length) {
						return 'No summoner names have recently been searched for. Try searching now!';
					}

					$html = $('<div class="table-responsive"><table class="table table-condensed"><thead><tr><th>Server</th><th>Name</th></tr></thead><tbody></tbody></table></div>');

					$tbody = $html.find('tbody');

					data.forEach(function (value) {
						$tbody.append(
							$('<tr>').html([
								$('<td>').text(value.server),
								$('<td>').text(value.name)
							])
						);
					});

					return $html;
				},
				className: 'bg-warning'
			},

			feedback: {
				html: function (feedback) {
					return '<p>Your email will <b>only</b> be used for obtaining follow-up information.</p><form id="feedback-form"><textarea required maxlength="1000" placeholder="Message" id="message" class="form-control">' + feedback.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</textarea><div class="input-group"><input type="email" class="form-control" id="email" placeholder="Email (Optional)"><span class="input-group-btn"><input id="feedback-submit" class="btn btn-success" type="submit" value="Submit Feedback"></span></div></form>'
				},
				className: 'bg-warning'
			}
		}
	};

	function sample(className, templateName, html) {
		$(document.documentElement).removeClass('server');

		var $textSamplingBody = $('#text-sampling .modal-dialog');
		var $parent = $('<div>');
		var $child = $('<span>');

		$parent.addClass('modal-body ' + (className || '')).html($child.html(html));

		$textSamplingBody.html($parent);

		return $parent.height();
	}

	var ran;

	function animateHeight(height) {
		if (!ran) {
			ran = true;

			$('#form-container').addClass('modal-border ran');
			$('.modal-footer').addClass('ran');
		}

		$('#status, #status-container').height(height);
	}

	var old = {};

	function display(type, templateName, data) {
		var $status = $('#status');
		var colors = {
			'bg-clear': 'transparent',
			'bg-primary': '#337AB7',
			'bg-success': '#DFF0D8',
			'bg-info': '#D9EDF7',
			'bg-warning': '#FCF8E3',
			'bg-danger': '#F2DEDE'
		};

		var className = '';
		var template;
		var html;

		templateName ? (old.templateName = templateName) : (templateName = old.templateName);

		switch (type) {
			case 'static':
				data ? (old.data = data) : (data = old.data);

				template = templates.static[templateName];

				className = template.className;
				html = template.html;
				break;
			case 'dynamic':
				template = templates.dynamic[templateName];

				className = template.className;
				html = template.html(data);
				break;
		}

		html ? (old.html = html) : (html = old.html);

		animateHeight(sample(className, templateName, html));

		$('#status-container').html(html);
		$status
			.removeClass(template && 'remove-height bg-success bg-info bg-danger bg-warning bg-clear')
			.addClass(className);


		className && $('#css-hack').text('#form-container:after,.modal-footer:before{background-color:' + colors[className] + ';}');
	}

	function setCookies(cookies) {
		for (var cookie in cookies) {
			if (cookies.hasOwnProperty(cookie)) {
				document.cookie = encodeURIComponent(cookie) + '=' + cookies[cookie] + '; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/; domain=summonernames.com';
			}
		}
	}

	function getCookie(name) {
		var parts = ('; ' + document.cookie).split('; ' + name + '=');
		if (parts.length > 1) return decodeURIComponent(parts.pop().split(';').shift());
	}

	function updateURL(server, name) {
		window.history.replaceState(
			window.history.state,
			document.title,
			'/#!/' + server.toLowerCase() + '/' + name.replace(/%20/g, '+') + '/'
		);
	}

	function prepareData() {
		return ({
			server: $('#server').val(),
			name: encodeURIComponent(($('#name').val() || $('#name').attr('placeholder')).trim())
		});
	}

	function parseSummoner(data) {
		var datetime;
		var timezone;
		var date;
		var time;
		var now;

		if (!(data instanceof Object) || data.error) {
			fetchError();
			return;
		}

		data.name = decodeURIComponent(data.name);

		datetime = new Date(data.time * 1000);
		timezone = datetime.toTimeString().slice(19, -1);
		date = datetime.toLocaleDateString();
		time = datetime.toLocaleTimeString().replace(/^(\d{1,2}:\d{2}):\d{2}\s([AP]M).*$/, '$1 $2');

		if (timezone.length > 3) {
			timezone = timezone.match(/\b([A-Z]+)/g).join('');
		}

		now = Math.round(Date.now() / 1000) + 5000;

		if (datetime.getTime() / 1000 <= now) {
			display('dynamic', 'available', {
				server: data.server,
				name: data.name
			});
		} else {
			display('dynamic', 'unavailable', {
				timezone: timezone,
				server: data.server,
				name: data.name,
				date: date,
				time: time
			});
		}
	}

	function fetchError() {
		display('static', 'error');
	}

	function toggleDropdown(event) {
		if (event) {
			event.preventDefault();

			$('#server-list').toggleClass('shown');
			$('#backdrop').toggleClass('hidden');
		} else {
			$('#server-list').removeClass('shown');
			$('#backdrop').addClass('hidden');
		}
	}

	function parseTop(data) {
		if (!(data instanceof Object)) {
			fetchError();
			return;
		}

		display('dynamic', 'recentlySearched', data);
	}

	function matchURL() {
		var linked = [
			window.location.search.match(/^\?\/?(br|eune|euw|kr|lan|las|na|oce|ru|tr)\/([^\/]+)\/?$/i),
			window.location.pathname.match(/^\/(br|eune|euw|kr|lan|las|na|oce|ru|tr)\/([^\/]+)\/?$/i),
			window.location.hash.match(/^#!?\/?(br|eune|euw|kr|lan|las|na|oce|ru|tr)\/([^\/]+)\/?$/i)
		];

		linked.some(function (match) {
			if (match) {
				$('#server-list li[data-server="' + match[1].toUpperCase() + '"]').trigger('click');
				$('#name').attr('placeholder', decodeURIComponent(match[2]).replace(/\+/g, ' '));
				$('#main').trigger('submit');

				return true;
			}
		});
	}

	function setDefaults() {
		$('#server-list li[data-server="' + getCookie('server') + '"]').trigger('click');
		$('#name').attr('placeholder', (getCookie('name') || '').replace(/\+/g, ' ').trim());
	}

	function feedbackSent() {
		display('static', 'feedbackSent');
	}

	var feedback = '';
	$(function () {
		$('#server-list li').on('click', function () {
			var $this = $(this);

			$this.siblings().removeClass('selected');
			$this.addClass('selected');

			$('#server').val($this.attr('data-server'));

			toggleDropdown();
		});

		setDefaults();

		var width = window.innerWidth;
		$(window).on('resize', function () {
			if (window.innerWidth !== width) {
				width = window.innerWidth;

				ran && display();
			}
		});

		$('#main').on('submit', function (event) {
			event.preventDefault();

			var data = prepareData();

			// TODO: VALIDATE SUMMONER NAMES

			setCookies({
				server: data.server,
				name: data.name
			});

			updateURL(data.server, data.name);

			display('static', 'loading');
			$.getJSON('/api/summoner/' + data.server.toLowerCase() + '/' + data.name + '/')
				.done(parseSummoner)
				.fail(fetchError);
		});

		$('#server, #backdrop').on('click', toggleDropdown);

		$('#disclaimer, #privacy-policy, #cookie-policy').on('click', function (event) {
			event.preventDefault();

			display('static', $(this).attr('id').replace('-p', 'P'));
		});

		$('#feedback').on('click', function () {
			display('dynamic', 'feedback', feedback);
		});

		$('#recently-searched').on('click', function () {
			display('static', 'loading');
			$.getJSON('/api/recent/')
				.done(parseTop)
				.fail(fetchError);
		});

		$('.modal-content').on('click', '.btn', function () {
			$(this).blur();
		});

		$(window).on('error', function () {
			display('static', 'error');
		});

		matchURL();

		$('#status').on('submit', '#feedback-form', function (event) {
			event.preventDefault();

			var message = $('#message').val();
			var email = $('#email').val();

			if (message.length > 1000) {
				feedback = message;

				display('static', 'messageTooLong');
				return;
			}

			$.post('/api/feedback/', {
				message: message,
				email: email
			})
				.done(feedbackSent)
				.fail(fetchError);
		});
	});
})();
