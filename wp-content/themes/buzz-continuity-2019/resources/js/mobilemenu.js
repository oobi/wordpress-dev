export function mobilemenu() {

    let $htmlBody	= $('html, body');
    // let $body 		= $('body');
    let $offcanvas 	= $('.offcanvas');

    // add open class when menu toggle clicked and disabled touch scrolling
    $('.navbar-toggler').on('click', function() {
		$htmlBody.addClass('offcanvas-menu-open');
    });

    // remove the open class when close clicked
    $offcanvas.on('click', '.close', function() {
        $htmlBody.removeClass('offcanvas-menu-open');
    });

    $('.offcanvas-overlay').on('click', function() {
        $htmlBody.removeClass('offcanvas-menu-open');
    })

    // this is compatible with simple and "mega menu"
    // hence the over-complicated parent selector
    $offcanvas.on('click', '.expander', function() {
        var $parent = $(this).parent();
        $('.sub-menu:first', $parent).toggleClass('open');
    });

    // add open class to the current page ancestor
    $('.current-page-ancestor', $offcanvas).each(function() {
        $('.sub-menu:first', this).addClass('open');
    });

}