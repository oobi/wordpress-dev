(function( $ ) {
	'use strict';

	if(!MIDGARD_WP_URLS ) {
		console.error('Must inject token URLs via MIDGARD_WP_URLS variable!');
	}

	var tokenURL = MIDGARD_WP_URLS.token;
	var tokenValidateURL = MIDGARD_WP_URLS.validate;

	/**
	 * "add new wordpress" functionality
	 */
	$(document).ready(function($){
		var $btnToken = $("#midgard-wordpress-btn-token");
		var $btnAdd = $("#midgard-wordpress-btn-add");

		// disable add button unless token, URL and label is filled in
		//$btnAdd.prop('disabled','disabled');

		// add wordpress action button
		$btnToken.on('click', function(event){
			event.preventDefault();
			getToken();
		});

		$btnAdd.on('click', function(event){
			event.preventDefault();
			var input = getInputs();
			if(!input.token || !input.token.length) {
				alert('You must enter an auth token first');
				return;
			}
			addRow(input);
		});

		$('.midgard-wordpress-table').on('click', '.midgard-wordpress-delete', function(event){
			event.preventDefault();
			deleteRow( this );
		});

	});

	//////////////////////////////////////////////////////
	// Login Validation
	//////////////////////////////////////////////////////

	function getToken() {
		var input = getInputs();

		// result field
		var $result = $('#midgard-wordpress-validation-result');
		// token field
		var $token = $('#new-wp-token');

		// empty result
		$result.addClass('spinner is-active')
			   .text('... working ... ')

		// get token
		$.ajax( {
			url 	: ajaxurl,
			method  : 'POST',
			data 	: {
				action   : 'midgard-wordpress-get-token',
				endpoint : input.endpoint_token,
				username : input.username,
				password : input.password
			}
		}).done( function(data, textStatus, jqXHR){
			// set token to field
			if( data.success && data.body && data.body.token) {
				$('#new-wp-token').val(data.body.token);
				$result.css('color','green')
						.text('Success! Click the "Add" button below to add this auth token to your collection. Remember to save your changes!');
			} else {
				var message = data.message ? data.message : 'Unable to retrieve token';
				$result.css('color','red')
						.text('Error - ' + message);
			}

		}).fail( function(jqXHR, textStatus, errorThrown){
			$result.css('color','red')
					.text('Error - ' + errorThrown);
		}).always( function() {
			$result.removeClass('spinner is-active');
		});
	}

	/**
	 * retrieve input values to validate/add
	 */
	function getInputs() {
		var url 	 = $('[name=new-wp-url]').val();
		var username = $('[name=new-wp-login]').val();
		var password = $('[name=new-wp-password]').val();
		var label 	 = $('[name=new-wp-label]').val();
		var token 	 = $('[name=new-wp-token]').val();

		// auth endpoint
		var endpoint_token = url.replace(/^(.*?)\/?$/, '$1') + tokenURL;
		var endpoint_validate = url.replace(/^(.*?)\/?$/, '$1') + tokenValidateURL;

		return {
			url 				: url,
			username		 	: username,
			password			: password,
			label 				: label,
			endpoint_token 		: endpoint_token,
			endpoint_validate 	: endpoint_validate,
			token 				: token
		};
	}

	//////////////////////////////////////////////////////
	// TABLE OPERATIONS
	//////////////////////////////////////////////////////

	/**
	 * Remove row
	 */
	function deleteRow( element ) {
		// remove current row
		var $tr = $(element).closest('tr');

		$tr.remove();

		remapIndex();
	}

	/**
	 * Add a new row to the table
	 */
	function addRow(input) {

		var $tbody = $('.midgard-wordpress-table > TBODY');

		// create a new row
		var $tr    = $('<tr/>');

		// hidden fields
		$tr.append('<input class="wp-label" type="hidden" name="" value="' + input.label  + '"/>');
		$tr.append('<input class="wp-url" type="hidden" name="" value="' + input.url  + '"/>');
		$tr.append('<input class="wp-token" type="hidden" name="" value="' + input.token  + '"/>');

		// visible columns
		$tr.append('<td>' + input.label + '</td>');
		$tr.append('<td>' + input.url  + '</td>');
		$tr.append('<td>' + input.token  + '</td>');
		$tr.append('<td><a class="midgard-wordpress-delete dashicons dashicons-trash" href="#" ></a></td>');

		// append to the table
		$tbody.append($tr);

		// remap index
		remapIndex();
	}

	/**
	 * Remap array indicies
	 */
	function remapIndex() {
		$('.midgard-wordpress-table > TBODY > TR').removeClass('alternate');

		$('.midgard-wordpress-table > TBODY > TR').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.wp-label', tr).prop('name', 'midgard_wordpress_settings[wp_auth_tokens][' + n + '][label]');
			$('.wp-url', tr).prop('name', 'midgard_wordpress_settings[wp_auth_tokens][' + n + '][url]');
			$('.wp-token', tr).prop('name', 'midgard_wordpress_settings[wp_auth_tokens][' + n + '][token]');
		});
	}

})( jQuery );