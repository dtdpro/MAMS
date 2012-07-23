function MAMSTrackMedia(track_id,secs_played,per_played) {
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
	/*// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			document.myForm.time.value = ajaxRequest.responseText;
		}
	}*/
	var queryString = "?track_id=" + encodeURIComponent(track_id) + "&secs_played=" + encodeURIComponent(secs_played) + "&per_played=" + encodeURIComponent(per_played);
	ajaxRequest.open("GET", mamsuri+"/components/com_mams/mediatrack.php" + queryString, true);
	ajaxRequest.send(null); 
	
}
