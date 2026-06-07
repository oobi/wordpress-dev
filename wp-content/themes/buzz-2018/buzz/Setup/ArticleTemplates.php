<?php
namespace Firefly\Buzz\Setup;


/**
 * This class keeps track of all the article templates that are useable in the theme.
 * Each template has certain characteristics that the Twig needs to be aware of in order to display properly
 * (eg. maximum number of articles in supports)
 *
 * Templates defined here will automatically show up in the corresponding Customizer select fields.
 *
 * Templates defined here should also have a corresponding twig template that is used to display it, named after the slug
 * (eg. An article template with slug "template-one" should have a twig named "template-one.twig")
 */
class ArticleTemplates {

    private $templates;

    function __construct() {

		/* // Templates should be defined like this
		[
			'slug'		=> 'slug',		// Unique slug that identifies the template
			'name'		=> 'Name',		// Display-friendly name
			'max'		=> 2,			// The maximum number of articles this template allows. FALSE for no limit.
		],
		*/

		// FEATURED ARTICLE TEMPLATES
		$this->templates['featured'] = [

			[
				'slug'		=> 'single-2col',
				'name'		=> 'One feature per row',
				'max'		=> -1,
			],
			[
				'slug'		=> 'double-2col',
				'name'		=> 'Two features per row',
				'max'		=> -1,
				'fill'		=> 2,
			],
			// [
			// 	'slug'		=> 'auto',
			// 	'name'		=> 'Auto Layout',
			// 	'max'		=> false,
			// ],

		];

		// INDEX ARTICLE TEMPLATES
		$this->templates['index'] = [

			[
				'slug'		=> 'grid',
				'name'		=> 'Grid',
				'max'		=> -1,
			],
			[
				'slug'		=> 'list',
				'name'		=> 'List',
				'max'		=> -1,
			],

		];


		// EMAIL FEATURED ARTICLE TEMPLATES
		$this->templates['email_featured'] = [

			[
				'slug'		=> 'image-left',
				'name'		=> 'Image left',
				'max'		=> -1,
				'alt'		=> 'image-right', // TODO: support alternate rows (use a different twig every x articles)
			],
			[
				'slug'		=> 'image-right',
				'name'		=> 'Image right',
				'max'		=> -1,
				'alt'		=> 'image-left',
			],
			[
				'slug'		=> 'image-above',
				'name'		=> 'Image above',
				'max'		=> -1,
				'alt'		=> false,
			],
			// [
			// 	'slug'		=> 'auto',
			// 	'name'		=> 'Auto Layout',
			// 	'max'		=> false,
			// ],

		];

		// EMAIL INDEX ARTICLE TEMPLATES
		$this->templates['email_index'] = [

			[
				'slug'		=> 'list',
				'name'		=> 'List',
				'max'		=> -1,
			],
			[
				'slug'		=> 'grid',
				'name'		=> 'Grid (2 columns)',
				'max'		=> -1,
			],

		];

	}

	/**
	 * Get the template data for an article category
	 *
	 * @param 	String	$slug			The slug of the individual template to get. Leave blank to return all templates.
	 * @return 	Array 					An array of template data
	 */
	public function get( $category='', $slug='' ) {

		// the template array to search
		if( ! empty( $category ) ) {
			$templates = $this->templates[$category];
		} else {
			$templates = array_merge( $this->templates['index'], $this->templates['featured'] );
		}

		if( ! empty( $slug ) ) {
			// search the templates for matching slug and return
			$result = array_search( $slug, array_column( $templates, 'slug' ) );

			if( $result !== false ) { // $result can be 0
				return $templates[$result];
			} else {
				return false;
			}
		}

		// otherwise return all templates
		return $templates;

	}

	/**
	 * Outputs an array suitable for the Kirki select field choices paramater
	 *
	 * @param 	String		$type 		The article template type to get choices for
	 * @return 	Array 					A Kirki-compatible select field choices array
	 */
	public function choices( $type ) {

		// if the key exists, map array to Kirki compatible format and return
		if( array_key_exists( $type, $this->templates ) ) {
			$choices = [];
			foreach( $this->templates[$type] as $t ) {
				$choices[$t['slug']] = $t['name'];
			}

			return $choices;
		}

		// otherwise return blank array
		return [];

	}

}