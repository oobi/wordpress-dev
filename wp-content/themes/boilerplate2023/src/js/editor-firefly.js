/**
 	==============================
	Core Blocks:
	==============================
	'core/paragraph',
	'core/image',
	'core/heading',
	'core/gallery',
	'core/list',
	'core/quote',
	'core/audio',
	'core/cover',
	'core/file',
	'core/video',
	'core/table',
	'core/verse',
	'core/code',
	'core/freeform',
	'core/html',
	'core/preformatted',
	'core/pullquote',
	'core/button',
	'core/columns',
	'core/media-text',
	'core/more',
	'core/nextpage',
	'core/separator',
	'core/spacer',
	'core/shortcode',
	'core/archives',
	'ore/categories',
	'core/latest-comments',
	'core/latest-posts',
	'core/calendar',
	'core/rss',
	'core/search',
	'core/tag-cloud',
	'core/embed',
	'core-embed/twitter',
	'core-embed/youtube',
	'core-embed/facebook',
	'core-embed/instagram',
	'core-embed/wordpress',
	'core-embed/soundcloud',
	'core-embed/spotify',
	'core-embed/flickr',
	'core-embed/vimeo',
	'core-embed/animoto',
	'core-embed/cloudup',
	'core-embed/collegehumor',
	'core-embed/dailymotion',
	'core-embed/funnyordie',
	'core-embed/hulu',
	'core-embed/imgur',
	'core-embed/issuu',
	'core-embed/kickstarter',
	'core-embed/meetup-com',
	'core-embed/mixcloud',
	'core-embed/photobucket',
	'core-embed/polldaddy',
	'core-embed/reddit',
	'core-embed/reverbnation',
	'core-embed/screencast',
	'core-embed/scribd',
	'core-embed/slideshare',
	'core-embed/smugmug',
	'core-embed/speaker',
	'core-embed/ted',
	'core-embed/tumblr',
	'core-embed/videopress',
	'core-embed/wordpress-tv',
 */

wp.domReady(() => {
    if (wp.blocks) {
        // console.log( wp.blocks.getBlockTypes() );

        // don't want this button block
        wp.blocks.unregisterBlockType("generateblocks/button", ["default", "squared", "fill"]);
        wp.blocks.unregisterBlockStyle("core/button", ["default", "squared", "fill"]);
        wp.blocks.unregisterBlockStyle("core/image", ["rounded"]);

		// BUTTON (single)
		wp.blocks.registerBlockStyle("core/button", [
            {
                name: "arrow",
                label: "Arrow",
            },
            {
                name: "text",
                label: "Text",
            },
            {
                name: "app-google",
                label: "Google Play Store",
            },
            {
                name: "app-apple",
                label: "App Store",
            }
        ]);

		// BUTTONS (collection)
        wp.blocks.registerBlockStyle("core/buttons", [
			{
				name: "large",
				label: "Large",
			},
			{
				name: "wide",
				label: "Wide",
			}
        ]);

		// HEADING
		wp.blocks.registerBlockStyle("core/heading", [
            {
                name: "serif",
                label: "Serif",
            },
        ]);

		// IMAGE
		wp.blocks.registerBlockStyle("core/image", [
            {
                name: "dropshadow",
                label: "Drop Shadow",
            },
        ]);

		// LIST
		wp.blocks.registerBlockStyle("core/list", [
            {
                name: "fancy",
                label: "Fancy",
            },
        ]);

		// MEDIA/TEXT
		wp.blocks.registerBlockStyle("core/media-text", [
            {
                name: "circle",
                label: "Circle",
            },
            {
                name: "tile",
                label: "Tile",
            },
        ]);

		// PARAGRAPH
        wp.blocks.registerBlockStyle("core/paragraph", [
            {
                name: "serif",
                label: "Serif",
            },
			{
                name: "highlight",
                label: "Highlight",
            },
			{
                name: "citation",
                label: "Citation",
            },
        ]);

		// PULLQUOTE
		wp.blocks.registerBlockStyle("core/pullquote", [
            {
                name: "fancy",
                label: "Fancy",
            },
        ]);
    }
});
