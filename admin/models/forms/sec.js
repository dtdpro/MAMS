window.addEvent('domready', function() {
	document.formvalidator.setHandler('sec',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
});