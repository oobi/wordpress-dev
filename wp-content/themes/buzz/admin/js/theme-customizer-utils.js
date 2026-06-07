/**
 * Utility methods for use in customizer
 */
/* global colorScheme, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( undefined ) {

	// define global namespace if not already defined
	if(!window.Buzz) window.Buzz = {};
	var Buzz = window.Buzz;

	/**
	 * Convert hex colour to RGB alpha CSS string
 	 * @param {String} hex  - e.g. '#F00123
 	 * @param {Float} alpha - float (0..1)
  	 * @return {String} colour string in RGBA format
	 */
	Buzz.hexToRgb = function(hex, alpha) {
        if(hex.substr(0,3) == 'rgb') {
            hex = Buzz.rgbToHex(hex);
        }
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        if(result) {
            var r = parseInt(result[1], 16);
            var g = parseInt(result[2], 16);
            var b = parseInt(result[3], 16);
            var a = alpha!==null ? alpha : 1;
            return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
        } else {
            return hex;
        }
    };

	/**
	 * Convert RGBA colour to hex colour
 	 * @param {String} rgb  - e.g. '#F00123
  	 * @return {String} colour string in hex format
	 */
    Buzz.rgbToHex = function(rgb) {
         rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
         return (rgb && rgb.length === 4) ? "#" +
          ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    };

	/**
	 * Convert hex colour to HSL object
 	 * @param {String} hex  - e.g. '#F00123
 	 * @return {Object} object with keys for hue, saturation, lightness (h, s, l)
	 */
	Buzz.hexToHSL = function(hex) {
		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

		if(!result) { return hex; }

	    var r = parseInt(result[1], 16) / 255;
        var g = parseInt(result[2], 16) / 255;
        var b = parseInt(result[3], 16) / 255;

		// generate HSL array from RGB

		var var_Min = Math.min(Math.min(r, g), b);
		var var_Max = Math.max(Math.max(r, g), b);
		var del_Max = var_Max - var_Min;

		var L = var_Max;
		var H, S, del_R, del_G, del_B;

		if (del_Max == 0)
		{
			H = 0;
			S = 0;
		}
		else
		{
			S = del_Max / var_Max;

			del_R = ( ( ( var_Max - r ) / 6 ) + ( del_Max / 2 ) ) / del_Max;
			del_G = ( ( ( var_Max - g ) / 6 ) + ( del_Max / 2 ) ) / del_Max;
			del_B = ( ( ( var_Max - b ) / 6 ) + ( del_Max / 2 ) ) / del_Max;

			if		(r == var_Max) H = del_B - del_G;
			else if (g == var_Max) H = ( 1 / 3 ) + del_R - del_B;
			else if (b == var_Max) H = ( 2 / 3 ) + del_G - del_R;

			if (H<0) H++;
			if (H>1) H--;
		}

		var hsl = {
			h : H,
			s : S,
			l : L
		};

		return hsl;
	};


	/**
	 * Get a contrasting colour given an RGB input
	 * @param {String} hex - e.g. #FF6600
	 * @param {String} light  - default 'light' value (for light HSL - defaults tto white)
	 * @param {String} dark   - defaukt 'dark' value (defaults to dark grey #111111)
	 * @return {String} - hex string suitable for CSS (e.g. #FF6600)
	 */
	Buzz.getContrastColor = function(hex, light, dark) {
		var hsl = Buzz.hexToHSL(hex);
		if(!light) light = '#FFFFFF';
		if(!dark) dark = '#111111';

		if('l' in hsl) {
			return (hsl.l < 0.8 || (hsl.h > 0.6 && hsl.h < 0.8)) ? light : dark;
		}
		return hex;
	};

} )( );
