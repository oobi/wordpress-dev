/**
 * Manipulate the internal link to cover the entire tile
 * @param {*} $
 */
export function initMediaTextTile($) {
	$('.wp-block-media-text.is-style-tile,.wp-block-media-text.is-style-circle').each( (n, el) => {
		let $tile = $(el);
		let $link = $tile.find('.wp-block-media-text__media > a');

		// nothing to do - tile not linked
		if( $link.length === 0 ) {
			return;
		}

		// get the link
		let link = $link.attr('href');
		let target = $link.attr('target') ?? '_self';

		// remove the link
		$('.wp-block-media-text__media', $tile).html( $('.wp-block-media-text__media > a > img', $tile) );

		// change the outer wrapper tag to a link
		var $newElement = $(`<a href="${link}" target="${target}"></a>`).html($tile.html());

		// copy over the attributes
		$.each($tile[0].attributes, function() {
			// this.attributes is not a plain object, but an array
			// of attribute nodes, which contain both the name and value
			if(this.specified) {
				$newElement.attr(this.name, this.value);
			}
		});

		$newElement.addClass('has-link')
		$tile.replaceWith($newElement);
	})
}