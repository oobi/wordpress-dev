
import { initTransitions } from "./_transitions.js";
import { externalLinks } from "./_external-links";
import { scrollToTop } from "./_scroll-to-top";
import { initSlider } from "./_slider";
import { initPostSlider } from "./_post-slider";
import { initPrimaryNav } from "./_primary-nav";
import { initHeadroom } from "./_headroom";
import { initMediaTextTile } from "./_media-text-tile";
import { initVariableBg } from "./_variable-bg";
import { initAlert } from "./_alert";
import { initIframes } from "./_iframes";

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    require("bootstrap");
} catch (e) {}

// inits
jQuery(function($) {
    initHeadroom($);
	initPrimaryNav($);
	initAlert($);
	externalLinks($);
	initIframes($);

	scrollToTop('a.top-link');
    initSlider($);
	initVariableBg($)
	initPostSlider($);
	initMediaTextTile($);

	initTransitions($);

	// focus search field when dropdown opens
	$('.search-dropdown').on('shown.bs.dropdown', function () {
		$('[name="s"', this).focus();
	});

});