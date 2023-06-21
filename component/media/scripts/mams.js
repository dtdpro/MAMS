function MAMSTrackMedia(item_id) {
	//var request = jQuery.ajax({	url: mamsuri+"/components/com_mams/mediatrack.php",	type: "POST", data: { item_id : item_id }, dataType: "html"});
}

//MAMS Media Analytics

(function($) {

	$.extend(MediaElementPlayer.prototype, {
		buildmamsanalytics: function(player, controls, layers, media) {
				
			media.addEventListener('loadeddata', function() {
				MAMSTrackMedia(player.options.videoId);
			}, false);
		}
	});
		
})(mejs.$);

// MAMS Media Analytics for GoogleAnalytics

(function($) {

	$.extend(MediaElementPlayer.prototype, {
		buildmamsgoogleanalytics: function(player, controls, layers, media) {

			var i = 0;

			// Define at which percentages you want to fire an event
			var markers = [25,50,75,90,100];
			var playersMarkers = [];

			function findObjectIndexById(haystack, key, needle) {
				for (var i = 0; i < haystack.length; i++) {
					if (haystack[i][key] == needle) {
						return i;
					}
				}
				return null;
			}
				
			media.addEventListener('loadeddata', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event','MAMSMedia','Loaded',player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Loaded',player.options.videoExtTitle]);
				}
				if (typeof gtag === 'function') {
					gtag('event', 'Loaded', {
						'event_category': 'MAMSMedia',
						'event_label': player.options.videoExtTitle
					});
				}
			}, false);
				
			media.addEventListener('play', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event','MAMSMedia','Play',player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Play',player.options.videoExtTitle]);
				}
				if (typeof gtag === 'function') {
					gtag('event', 'Play', {
						'event_category': 'MAMSMedia',
						'event_label': player.options.videoExtTitle
					});
				}
			}, false);
			
			media.addEventListener('pause', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event', 'MAMSMedia', 'Pause', player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Pause',player.options.videoExtTitle]);
				}
				if (typeof gtag === 'function') {
					gtag('event', 'Pause', {
						'event_category': 'MAMSMedia',
						'event_label': player.options.videoExtTitle
					});
				}
			}, false);	
			
			media.addEventListener('ended', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event', 'MAMSMedia', 'Ended', player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Ended',player.options.videoExtTitle]);
				}
				if (typeof gtag === 'function') {
					gtag('event', 'Ended', {
						'event_category': 'MAMSMedia',
						'event_label': player.options.videoExtTitle
					});
				}

			}, false);

			media.addEventListener('timeupdate', function(e) {
				var percentPlayed = Math.floor(player.media.currentTime*100/player.media.duration);
				//var playerMarkerIndex = findObjectIndexById(playersMarkers,'id', player.id);

				if(markers.indexOf(percentPlayed)>-1 && playersMarkers.indexOf(percentPlayed)==-1)
				{
					playersMarkers.push(percentPlayed);

					if (typeof ga != 'undefined') {
						ga('send', 'event', 'MAMSMedia', percentPlayed.toString()+"%", player.options.videoExtTitle);
					}
					if (typeof _gaq != 'undefined') {
						_gaq.push(['_trackEvent','MAMSMedia',percentPlayed.toString()+"%",player.options.videoExtTitle]);
					}
					if (typeof gtag === 'function') {
						gtag('event', percentPlayed.toString()+"%", {
							'event_category': 'MAMSMedia',
							'event_label': player.options.videoExtTitle
						});
					}
				}
			},false);

			i++;

		}
	});
		
})(mejs.$);