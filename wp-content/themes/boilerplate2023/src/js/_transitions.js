import AOS from "aos";

export function initTransitions($) {
	AOS.init({
        // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
        offset: 0, // offset (in px) from the original trigger point
        delay: 250, // values from 0 to 3000, with step 50ms
        duration: 1000, // values from 0 to 3000, with step 50ms
        easing: "ease", // default easing for AOS animations
        once: false, // whether animation should happen only once - while scrolling down
        mirror: false, // whether elements should animate out while scrolling past them
        anchorPlacement: "top-bottom", // defines which position of the element regarding to window should trigger the animation
    });


	// Individual transitions
	transitions($);

	// Refresh AOS
	AOS.refreshHard();

}

function transitions($) {
	$(".gb-grid-wrapper").each(function (n, wrap) {
        var kids = $("> .gb-grid-column", wrap).length;

        if (kids != 2) {
            $("> .gb-grid-column", wrap).each(function (n, item) {
                $(item)
                    .attr("data-aos", "fade-up")
                    .attr("data-aos-delay", 300 * n);
            });
        } else {
            $("> .gb-grid-column:nth-child(1)", wrap).attr("data-aos", "fade-right");
            $("> .gb-grid-column:nth-child(2)", wrap).attr("data-aos", "fade-left").attr("data-aos-delay", 400);
        }
    });

    $(".section-title").each(function (n, wrap) {
        $(wrap).attr("data-aos", "fade-up");
    });

	// news
    $(".latest-news").each(function (n, wrap) {
        $(".latest-news-image-wrapper", wrap)
			.attr("data-aos", "fade-right");
		$(".latest-news-entry-wrapper", wrap)
			.attr("data-aos", "fade-left");

		$('.col-12.mt-8 .news-entry-link', wrap).each(function (n, item) {
			$(item).attr("data-aos", "fade-up")
				 .attr("data-aos-delay", 400 * n);
		});
    });

	// cover block
    $(".wp-block-cover").each(function (n, wrap) {
        $("> .wp-block-cover__inner-container", wrap).each(function (n, item) {
            $(item).attr("data-aos", "fade-left");
        });
    });

	// quotes
    $(".wp-block-pullquote, .wp-block-quote").each(function (n, wrap) {
        $(wrap).attr("data-aos", "fade-up");
    });

	// blocks
    $(".wp-block-embed, .wp-block-table, .gform_wrapper").each(function (n, wrap) {
        $(wrap).attr("data-aos", "fade-up");
    });

	// buttons
    $(".wp-block-buttons").each(function (n, wrap) {
        $(".wp-block-button", wrap).each(function (n, item) {
			var transition = $(item).hasClass('is-style-arrow') ? 'fade-up' : 'flip-left';

            $(item)
                .attr("data-aos", transition)
                .attr("data-aos-delay", 400 * n);
        });
    });

	// page header
    $(".entry-header").each(function (n, wrap) {
        $(".entry-header-wrapper", wrap).each(function (n, item) {
            $(item)
                .attr("data-aos", "fade-left")
                .attr("data-aos-delay", 400 * n);
        });
    });

    $(".offset-heading-block").each(function (n, wrap) {
        $('.line1', wrap)
            .attr("data-aos", "fade-right");
        $('.line2', wrap)
            .attr("data-aos", "fade-left")
			.attr("data-aos-delay", 250 * n);
        $('.line3', wrap)
            .attr("data-aos", "fade-right")
			.attr("data-aos-delay", 250 * n);
    });

    $(".slider-offset-text").each(function (n, wrap) {
		$('.slide-content1', wrap)
			.attr("data-aos", "fade-right");

		$('.slide-title', wrap)
			.attr("data-aos", "fade-down")
			.attr("data-aos-delay", 400 * n);

		$('.slide-text', wrap)
			.attr("data-aos", "fade-up")
			.attr("data-aos-delay", 400 * n);
    });

    $(".related-content").each(function (n, wrap) {
        $(".col-md-6", wrap).each(function (n, item) {
            $(item)
                .attr("data-aos", "flip-left")
                .attr("data-aos-delay", 400 * n);
        });
    });

    $(".footer-ctas").each(function (n, wrap) {
        $(".footer-cta", wrap).each(function (n, item) {
            $(item)
                .attr("data-aos", "flip-left")
                .attr("data-aos-delay", 400 * n);
        });
    });
}