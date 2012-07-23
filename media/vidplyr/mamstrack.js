(function(jwplayer) {
	var plugin_id = "mamstrack";
	var plug_mamstrack = function(player, user_config, div) {
		var default_config = {
			itemid : 0,
			trackstarts : true,
			trackpercentage : true,
			tracktime : true,
			debug : false,
			currentItem : undefined,
			pageURL : undefined,
			trackid: 0
		};
		var playdata = jwplayer.utils.extend({}, default_config, user_config);
		for ( var item in playdata) {
			if (playdata[item] == "true") {
				playdata[item] = true
			} else {
				if (playdata[item] == "false") {
					playdata[item] = false
				}
			}
		}
		mamstrack_log("Initializing");
		function resetPlayData() {
			playdata.currentItem = jwplayer.utils.extend(playdata.currentItem, {
				started : false,
				secondsPlayed : 0,
				percentageMap : {},
				lastTime : 0,
				lastPercentage : 0
			});
		}
		function initListeners() {
			try {
				window.attachEvent("onbeforeunload", function(u) {
					loadPlayData();
				});
			} catch (t) {
			}
			try {
				window.addEventListener("beforeunload", function(u) {
					loadPlayData();
				}, false);
			} catch (t) {
			}
			player.onIdle(function(idle_data) {
				loadPlayData();
			});
			player.onPlaylistItem(function(item_data) {
				playdata.pageURL = window.top == window ? window.location.href
						: document.referrer;
				playdata.currentItem = {};
				resetPlayData();
				var play_item = player.getPlaylistItem(item_data.index);
				if (typeof play_item["gapro.hidden"] != "undefined") {
					playdata.currentItem.hidden = play_item["gapro.idstring"]
				} else {
					playdata.currentItem.hidden = false
				}
				startTrackData();
			});
			player.onTime(function(time_data) {
				if (!playdata.currentItem.started) {
					playdata.currentItem.started = true;
					return
				}
				var sec_played = time_data.position - playdata.currentItem.lastTime;
				var per_played = Math.ceil(time_data.position / time_data.duration * 100);
				if (per_played > 100) {
					per_played = 100
				}
				if (0 < sec_played && sec_played < 0.5) {
					playdata.currentItem.secondsPlayed += sec_played;
					if (playdata.currentItem.lastPercentage != per_played) {
						for ( var w = playdata.currentItem.lastPercentage; w <= per_played; w++) {
							playdata.currentItem.percentageMap[w] = true
						}
					}
				} else {
					mamstrack_log("Detected " + sec_played + " second seek - ignoring")
				}
				playdata.currentItem.lastTime = time_data.position;
				playdata.currentItem.lastPercentage = per_played

 			})
		}
		function loadPlayData() {
			var load_data = jwplayer.utils.extend({}, playdata.currentItem);
			resetPlayData();
			if (load_data.secondsPlayed > 0) {
				var per_played = 0;
				for ( var u = 0; u < 100; u++) {
					if (load_data.percentageMap[u]) {
						per_played++
					}
				}
				trackPlayData(Math.round(load_data.secondsPlayed),per_played);
				playdata.trackid=0;
			}
		}
		function trackPlayData(secs_played, per_played) {
			if (!playdata.currentItem.hidden) {
				MAMSTrackMedia(playdata.trackid,playdata.itemid,secs_played,per_played);
			} 
		}
		function startTrackData() {
			if (!playdata.currentItem.hidden) {
				var ajaxRequest;  
				try{
					ajaxRequest = new XMLHttpRequest();
				} catch (e){
					try{
						ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							alert("Your browser broke!");
							return false;
						}
					}
				}
				// Create a function that will receive data sent from the server
				ajaxRequest.onreadystatechange = function(){
					if(ajaxRequest.readyState == 4){
						playdata.trackid=ajaxRequest.responseText;
					}
				}
				var queryString = "?item_id=" + encodeURIComponent(playdata.itemid);
				ajaxRequest.open("GET", mamsuri+"/components/com_mams/mediatrack.php" + queryString, true);
				ajaxRequest.send(null); 
			} 
		}
		function mamstrack_log(log_item, log_data) {
			if (playdata.debug) {
				jwplayer.utils.log(plugin_id + ": " + log_item, log_data)
			}
		}
		initListeners()
	};
	jwplayer().registerPlugin(plugin_id, plug_mamstrack)
})(jwplayer);

