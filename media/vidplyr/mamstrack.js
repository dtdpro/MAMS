(function(jwplayer) {
	var plugin_id = "mamstrack";
	var plug_mamstrack = function(player, user_config, div) {
		var default_config = {
			trackstarts : true,
			trackpercentage : true,
			tracktime : true,
			debug : false,
			currentItem : undefined,
			pageURL : undefined
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
		function getPlayURL(play_item, play_idstring) {
			var play_url;
			if (typeof play_idstring != "undefined") {
				play_url = play_idstring;
				var play_strings = play_idstring.split("||");
				for ( var u = 0; u < play_strings.length; u++) {
					var play_string = play_strings[u];
					if (typeof play_item[play_string] != "undefined") {
						var regex = "\\|\\|" + play_string + "\\|\\|";
						var play_replacer = new RegExp(regex, "g");
						play_url = play_url.replace(play_replacer, play_item[play_string])
					}
				}
			} else {
				if (play_item.streamer) {
					var play_streamer = play_item.streamer;
					if (play_streamer.lastIndexOf("/") != play_streamer.length) {
						play_streamer += "/"
					}
					play_url = play_streamer + play_item.file
				} else {
					play_url = jwplayer.utils.getAbsolutePath(play_item.file)
				}
			}
			return play_url
		}
		function resetPlayData() {
			playdata.currentItem = jwplayer.utils.extend(playdata.currentItem, {
				started : false,
				secondsPlayed : 0,
				percentageMap : {},
				lastTime : 0,
				lastPercentage : 0
			})
		}
		function initListeners() {
			try {
				window.attachEvent("onbeforeunload", function(u) {
					loadPlayData()
				})
			} catch (t) {
			}
			try {
				window.addEventListener("beforeunload", function(u) {
					loadPlayData()
				}, false)
			} catch (t) {
			}
			player.onIdle(function(idle_data) {
				loadPlayData()
			});
			player.onPlaylistItem(function(item_data) {
				playdata.pageURL = window.top == window ? window.location.href
						: document.referrer;
				playdata.currentItem = {};
				resetPlayData();
				var play_item = player.getPlaylistItem(item_data.index);
				if (typeof play_item["gapro.idstring"] == "string") {
					playdata.currentItem.mediaID = getPlayURL(play_item, play_item["gapro.idstring"])
				} else {
					if (typeof n.idstring == "string") {
						playdata.currentItem.mediaID = getPlayURL(play_item, playdata.idstring)
					} else {
						playdata.currentItem.mediaID = getPlayURL(play_item)
					}
				}
				if (typeof play_item["gapro.hidden"] != "undefined") {
					playdata.currentItem.hidden = play_item["gapro.idstring"]
				} else {
					playdata.currentItem.hidden = false
				}
			});
			player.onTime(function(time_data) {
				if (!playdata.currentItem.started) {
					playdata.currentItem.started = true;
					trackPlayData("Video Plays");
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
				trackPlayData("Seconds Played", Math.round(load_data.secondsPlayed));
				var per_played = 0;
				for ( var u = 0; u < 100; u++) {
					if (load_data.percentageMap[u]) {
						per_played++
					}
				}
				trackPlayData("Percentage Played", per_played)
			}
		}
		function hasPlayData(item) {
			switch (item) {
			case "Video Plays":
				if (playdata.trackstarts) {
					return true
				}
				break;
			case "Seconds Played":
				if (playdata.tracktime) {
					return true
				}
				break;
			case "Percentage Played":
				if (playdata.trackpercentage) {
					return true
				}
				break
			}
			return false
		}
		function trackPlayData(tracked_item, tracked_value) {
			var log_item = "Not tracked";
			if (!playdata.currentItem.hidden && hasPlayData(tracked_item)) {
				trackItem(tracked_item, playdata.currentItem.mediaID, playdata.pageURL, tracked_value)
			} else {
				if (playdata.currentItem.hidden) {
					log_item += " - current item is hidden"
				} else {
					if (!hasPlayData(tracked_value)) {
						log_item += " - tracking of " + tracked_value + " is disabled"
					}
				}
			}
			mamstrack_log(log_item, {
				Category : tracked_item,
				Action : playdata.currentItem.mediaID,
				Label : playdata.pageURL,
				Value : tracked_value
			})
		}
		function trackItem(tracked_item, current_item, current_page, tracked_value) {
				MAMSTrackMedia(tracked_item, current_item, current_page, tracked_value);
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
// VARS
// a = jwplayer
// b = gapro
// c = plugin_id
// d = div
// f = user_config
// j = default_config
// n = playdata
// q = player
// s = item

//FUNCTIONS
// e = trackASync
// g = initListeners
// h = mamstrack_log
// i = loadPlayData
// l = trackSync
// m = resetPlayData
// o = getTrackingObject
// p = hasPlayData
// r = trackPlayData
// w = getPlayURL
