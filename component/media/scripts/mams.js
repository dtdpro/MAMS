function MAMSTrackMedia(item_id) {
	var request = jQuery.ajax({	url: mamsuri+"/components/com_mams/mediatrack.php",	type: "POST", data: { item_id : item_id }, dataType: "html"});
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
				
			media.addEventListener('loadeddata', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event','MAMSMedia','Loaded',player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Loaded',player.options.videoExtTitle]);
				}
			}, false);
				
			media.addEventListener('play', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event','MAMSMedia','Play',player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Play',player.options.videoExtTitle]);
				}
			}, false);
			
			media.addEventListener('pause', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event', 'MAMSMedia', 'Pause', player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Pause',player.options.videoExtTitle]);
				}
			}, false);	
			
			media.addEventListener('ended', function() {
				if (typeof ga != 'undefined') {
					ga('send', 'event', 'MAMSMedia', 'Ended', player.options.videoExtTitle);
				}
				if (typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent','MAMSMedia','Ended',player.options.videoExtTitle]);
				}
			}, false);
		}
	});
		
})(mejs.$);