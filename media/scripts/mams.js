
function MAMSTrackMedia(tracked_item, current_item, current_page, tracked_value) {
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
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
	var queryString = "?tracked_item=" + encodeURIComponent(tracked_item) + "&current_item=" + encodeURIComponent(current_item) + "&current_page=" + encodeURIComponent(current_page) + "&tracked_value=" + encodeURIComponent(tracked_value);
	ajaxRequest.open("GET", mamsuri+"/components/com_mams/mediatrack.php" + queryString, true);
	ajaxRequest.send(null); 
	
}