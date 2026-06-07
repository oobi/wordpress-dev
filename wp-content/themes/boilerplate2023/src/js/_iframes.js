import iframeResizer from "iframe-resizer/js/iframeResizer.js";

/**
 * Initialize iframe resizer
 * @param {object} $ - jQuery object
 *
 * Note - this ONLY works with iframes from the same domain
 * OR ones with appropriate CORS headers to allow cross-domain access.
 *
 * Don't wrestle with it -- if it doesn't work the issue is on the other side.
 */
export function initIframes($) {
   $("iframe.autoresize").each(function (n, iframe) {
        iframe.onload = function () {
            iframeResizer({
				// log: true,
				checkOrigin: false
			}, iframe);
        };
    });
}
