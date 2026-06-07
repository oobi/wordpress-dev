/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

	// header background
	// ff_newsletter_menu_bgcolor
	wp.customize( 'ff_newsletter_header_bgcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#banner' ).css( 'background-color', newval );
		} );
	} );

	// header text
	// ff_newsletter_header_txtcolor
	wp.customize( 'ff_newsletter_header_txtcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#banner,'+
				'#banner A,'+
				'#banner .site-text H1,'+
				'#banner .site-text P' ).css( 'color', newval );
		} );
	} );

	// menu background
	// ff_newsletter_menu_bgcolor
	wp.customize( 'ff_newsletter_menu_bgcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#nav,#nav .dropdown-menu' ).css( 'background-color', newval );
			$( '.alt-issue-date	' ).css( 'background-color', Buzz.hexToRgb(newval, 0.3) );
			// fallback for old IE - not supported here
			// $( '#nav #searchform INPUT,#nav A:hover,#nav A:focus' ).css( 'background-color', newval );
		} );
	} );

	// menu text
	// ff_newsletter_menu_txtcolor
	wp.customize( 'ff_newsletter_menu_txtcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#nav,#nav > DIV,#nav BUTTON,#nav A,#nav #searchform INPUT,#nav .navbar-text' ).css( 'color', newval );
			$( '#nav .navbar-toggle' ).css( 'border-color', newval );
			$( '#nav .navbar-toggle .icon-bar' ).css( 'background-color', newval );
			$( '#nav #searchform INPUT,#nav A:hover,#nav A:focus' ).css( 'background-color', Buzz.hexToRgb(newval, 0.1) );
		} );
	} );

	// primary
	// ff_newsletter_primary_color
	wp.customize( 'ff_newsletter_primary_color', function( value ) {
		value.bind( function( newval ) {
			$( '#content H1' ).css( 'color', newval );
		} );
	} );

	// secondary
	// ff_newsletter_secondary_color
	wp.customize( 'ff_newsletter_secondary_color', function( value ) {
		value.bind( function( newval ) {
			$( '.table-banded,' +
				'.table-standard,' +
				'.table-banded TD,' +
				'.table-standard TD,' +
				'HR,' +
				'#main .no-thumb-article-table').css( 'border-color', newval );

			$( '.table-banded THEAD TD,'+
				'.table-standard THEAD TD,'+
				'.table-banded TH,'+
				'.table-standard TH,'+
				'#featured-articles .feature-text,'+
				'TABLE.feature-table .feature-excerpt,'+
				'#sidebar .this-issue H3' ).css( 'background-color', newval );

			$( '.table-banded TR:nth-of-type(2n) TD' ).css( 'background-color', Buzz.hexToRgb(newval, 0.2) );

			$( '#sidebar .this-issue A SPAN,'+
			   '.pullquote P:before,'+
			   '.pullquote P:after,'+
			   '#article-prevnext .direction I' ).css( 'color', newval );

			// calculated contrast colour
			var contrast = Buzz.getContrastColor(newval);
			$( '#sidebar .this-issue H3,'+
			   '#featured-articles .feature-text,'+
			   '#featured-articles .feature-text H3,'+
			   '#featured-articles .feature-text A,'+
			   '#featured-articles .feature-text .tags' ).css( 'color', contrast );

			// add a dummy stylesheet to access :before and :after selectors
			var $sheet = $("#__ff_newsletter_secondary_color");
			if(!$sheet.length) {
				$sheet = $('<style id="__ff_newsletter_secondary_color"/>').appendTo('head');
			}
			$sheet.text('#sidebar .this-issue H3:before,'+
						 '#sidebar .this-issue H3:after {'+
						 'border-color:' + newval +
						 '}');
		} );
	} );

	// links
	// ff_newsletter_link_color
	wp.customize( 'ff_newsletter_link_color', function( value ) {
		value.bind( function( newval ) {
			$( '.home .article-container A.more,'+
			   '.home .article-container H3 A,'+
			   '.single-article .content-txt A,'+
			   '#article-prevnext A,'+
			   '#sidebar A' ).css( 'color', newval );

			$( '#sidebar .nav-links A,'+
			   '.gallery .gallery-icon,'+
			   '#sidebar .this-issue A .icon' ).css( 'background-color', newval );
		} );
	} );

	// link decoration
	// ff_newsletter_link_decoration
	wp.customize( 'ff_newsletter_link_decoration', function( value ) {
		value.bind( function( newval ) {
			$( '#content A, A.more' ).css( 'text-decoration', newval ? 'underline' : 'none');
		} );
	} );

	// social icon background
	// ff_social_icon_bgcolor
	wp.customize( 'ff_social_icon_bgcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#social .social-link' ).css( 'background-color', newval );
		} );
	} );

	// social icon text
	// ff_social_icon_txtcolor
	wp.customize( 'ff_social_icon_txtcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#social .social-link' ).css( 'color', newval );
		} );
	} );

	// widgets background
	// ff_newsletter_widgets_bgcolor
	wp.customize( 'ff_newsletter_widgets_bgcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#widgets' ).css( 'background-color', newval );
		} );
	} );

	// widgets text
	// ff_newsletter_widgets_bgcolor
	wp.customize( 'ff_newsletter_widgets_txtcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#widgets,#widgets A' ).css( 'color', newval );
		} );
	} );

	// footer background
	// ff_newsletter_widgets_bgcolor
	wp.customize( 'ff_newsletter_footer_bgcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#footer' ).css( 'background-color', newval );
		} );
	} );

	// footer text
	// ff_newsletter_widgets_bgcolor
	wp.customize( 'ff_newsletter_footer_txtcolor', function( value ) {
		value.bind( function( newval ) {
			$( '#footer,#footer A,#footer TD' ).css( 'color', newval );
		} );
	} );

} )( jQuery );