export function utilitysearch() {
    let searchform = $('.site-header .search-form');

    searchform.on('mouseover', function() {
        $(this).addClass('active');
        $('.form-control', this).focus();
    });

    $('.site-header .search-form .form-control').on('blur', function(e) {
        searchform.removeClass('active')
    });
}