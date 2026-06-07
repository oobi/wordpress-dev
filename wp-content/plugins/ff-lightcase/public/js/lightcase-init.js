jQuery(document).ready(function($) {
    $('a[data-rel^=lightcase]').lightcase({
        swipe: true,
        maxWidth: 1024,
        maxHeight: 768,
    });
});