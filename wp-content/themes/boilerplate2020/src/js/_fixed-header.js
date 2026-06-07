export function initFixedHeader($) {
	var isMobile = $(window).width() < 992;

	var shrinkHeader = isMobile ? 56 : 90; // pixels before shrinking the header
    $(window).scroll(function() {
        var scroll = getCurrentScroll();
        if( scroll >= shrinkHeader ) {
            $('.site-header').addClass('scroll');
        } else {
            $('.site-header').removeClass('scroll');
        }
    });

}

// get the current vertical scroll offset
function getCurrentScroll() {
    return window.pageYOffset || document.documentElement.scrollTop;
}
