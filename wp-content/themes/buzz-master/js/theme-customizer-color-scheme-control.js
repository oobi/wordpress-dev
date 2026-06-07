/* global colorScheme, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api, undefined ) {

	/**
	 * On Base Colour Scheme drop-down change, fill the colour selectors with new default values
	 */
	api.controlConstructor.select = api.Control.extend( {
		ready: function() {
			if ( 'ff_color_scheme' === this.id ) {
				this.setting.bind( 'change', function( value ) {

					var colors = colorScheme[value].colors;
					for(var key in colors) {

						// check if key exists so we don't have an error
						var exists = api(key) !== undefined;

						if(exists) {
							var color = colors[key]; // we check 'original' because this is what our colours are keyed against

							// set colour values according to defaults array
							api(key).set( color );
							if(api.control(key) !== undefined) {
								api.control(key).container.find( '.color-picker-hex' )
									.data( 'data-default-color', color )
									.wpColorPicker( 'defaultColor', color );
							}
						}
					}

				} );
			}
		}
	} );

} )( wp.customize );
