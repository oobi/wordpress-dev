import {utilitysearch} from './utility-search';
import {mobilemenu} from './mobilemenu';

(function($) {

    // Add the reference to jQuery to the window object
    window.$ = jQuery;

    utilitysearch();
    mobilemenu();

    initLightbox();
    initResponsiveEmbed();
    initResponsiveTables();

})(jQuery);

/**
 * Init Lightcase (lightbox)
 * http://cornel.bopp-art.com/lightcase/documentation/
 */
function initLightbox() {
	$('a[data-rel^=lightcase]').lightcase({
		maxWidth: 1024,
		maxHeight: 768
	});
}

function initResponsiveEmbed() {
	$('iframe[src*="youtube"], iframe[src*="vimeo"]').each(function(){
		$(this).addClass('embed-responsive-item')
			.wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
	});
}

function initResponsiveTables() {
	$('table').each(function(){
		$(this).addClass('table')
			.wrap('<div class="table-responsive"></div>');
	});
}