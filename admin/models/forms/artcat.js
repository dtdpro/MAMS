window.addEvent('domready', function() {
	document.formvalidator.setHandler('artcat',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
});