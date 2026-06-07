export function initAlert($) {
	if ($('.global-alert').length) {
		// read the alert cookie
		let cookieValue = getCookie('global-alert');

		if( cookieValue == 'closed' ) {
			// if the cookie is set, close the alert
			$('.global-alert').remove();
			return;
		}

		// if there is an alert, listen for close and set a cookie
		$('.global-alert').on('closed.bs.alert', function () {
			// set a cookie
			setCookie('global-alert', 'closed', 1);
			// trigger resize to fix the headroom
			window.dispatchEvent(new Event('resize'));
		})
		.addClass('active');

	}
}

/**
 * set a cookie that expires when the browser is closed
 */
export function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


/**
 * get a cookie's value
 * @param  {string} cname the name of the cookie
 * @return {string}       the value of the cookie
 */
export function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}