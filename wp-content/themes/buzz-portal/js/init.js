(function($){

$(document).ready(function($){

	///////////////////////////////////////////////////////////////
	// RESPONSIVE VIDEO EMBED
	///////////////////////////////////////////////////////////////

	/* responsive oembed videos */
	/* do not include videos added with 'featured video' plugin */
	var $all_oembed_videos = $("iframe[src*='youtube'], iframe[src*='vimeo']").not(".featured-video-plus IFRAME");
	$all_oembed_videos.each(function() {
		$(this).removeAttr('height').removeAttr('width').wrap( "<div class='embed-container'></div>" );
	});

	// wrap tables
	$(".table-standard,.table-banded").wrap('<div class="table-wrapper"/>');


	initHandsetMenu();
});



function initHandsetMenu() {
	$("#main-menu-hs").on('click', '.expand', function(event){
		event.preventDefault();
		event.stopPropagation();
		// toggle the submenu open/closed
		$li = $(this).closest('LI');
		$li.find('> UL').slideToggle(function(){
			$li.toggleClass('open');
		});
	});
}

})(jQuery);