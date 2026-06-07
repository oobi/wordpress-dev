<?php

namespace Firefly\Onboarding;

/** ------------------------------------------------------
 *	 Merlin and TGMPA config
 *
 * 	http://tgmpluginactivation.com/installation/
 *  https://github.com/richtabor/MerlinWP
 *
 *	------------------------------------------------------ */

class Onboarding
{

	function __construct()
	{
		$dir = get_template_directory();

		// TGM Plugin Activation (this goes first)
		require_once($dir . '/merlin/class-tgm-plugin-activation.php');

		// Load Merlin components
		require_once($dir . '/merlin/vendor/autoload.php');
		require_once($dir .  '/merlin/class-merlin.php');
		require_once($dir . '/merlin/merlin-config.php');

		add_filter('merlin_import_files', [$this, 'merlin_import_files']);
		add_action('tgmpa_register', [$this, 'tgmpa_register']);
	}


	public function merlin_import_files()
	{
		return [
			// full theme demo
			[
				'import_file_name'           => 'Demo Content Import',
				'local_import_file'            => get_parent_theme_file_path('/demo_content/demo-content.xml'),
				'local_import_widget_file'     => get_parent_theme_file_path('/demo_content/demo-widgets.json'),
				// 'import_customizer_file_url' => 'http://www.your_domain.com/merlin/customizer.dat',
				'import_preview_image_url'   => get_template_directory_uri() . '/demo_content/homepage.png',
				'import_notice'              => __('A special note for this import.', 'firefly'),
				'preview_url'                => 'https://wordpress.fireflydigital.dev/education2023',
			],

			// more definitions here
		];
	}

	public function tgmpa_register()
	{
		$plugins = [
			// WordPress Plugin Repository.
			[
				'name'      => 'Crop Thumbnails',
				'slug'      => 'crop-thumbnails',
				'required'  => false,
			],
			[
				'name'      => 'Custom Block Patterns',
				'slug'      => 'custom-block-patterns',
				'required'  => false,
			],
			[
				'name'      => 'GenerateBlocks',
				'slug'      => 'generateblocks',
				'required'  => false,
			],
			[
				'name'      => 'Kirki Customizer Framework',
				'slug'      => 'kirki',
				'required'  => false,
			],
			[
				'name'      => 'Lightbox for Gallery & Image Block',
				'slug'      => 'gallery-block-lightbox',
				'required'  => false,
			],
			[
				'name'      => 'Nested Pages',
				'slug'      => 'wp-nested-pages',
				'required'  => false,
			],

			// External plugins
			array(
				'name'         => 'Gecka Submenu Pro', // The plugin name.
				'slug'         => 'gecka-submenu-pro', // The plugin slug (typically the folder name).
				'source'       => 'https://public.fireflydigital.dev/wordpress-files/plugins/gecka-submenu-pro.zip', // The plugin source.
				'required'     => false, // If false, the plugin is only 'recommended' instead of required.
				'external_url' => 'https://public.fireflydigital.dev/wordpress-files/plugins/gecka-submenu-pro.zip', // If set, overrides default API URL and points to an external URL.
			),
			array(
				'name'         => 'Gravity Forms', // The plugin name.
				'slug'         => 'gravityforms', // The plugin slug (typically the folder name).
				'source'       => 'https://public.fireflydigital.dev/wordpress-files/plugins/gravityforms.zip', // The plugin source.
				'required'     => false, // If false, the plugin is only 'recommended' instead of required.
				'external_url' => 'https://public.fireflydigital.dev/wordpress-files/plugins/gravityforms.zip', // If set, overrides default API URL and points to an external URL.
			),

		];

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			/*
			'strings'      => array(
				'page_title'                      => __( 'Install Required Plugins', 'theme-slug' ),
				'menu_title'                      => __( 'Install Plugins', 'theme-slug' ),
				// <snip>...</snip>
				'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
			*/
		);

		tgmpa($plugins, $config);
	}
}
