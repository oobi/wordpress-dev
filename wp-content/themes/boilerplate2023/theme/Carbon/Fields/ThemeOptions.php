<?php

namespace Firefly\Carbon\Fields;

use Carbon_Fields\Field;
use Carbon_Fields\Container;

class ThemeOptions
{
	protected $text_domain;

	public function __construct()
	{
        $this->text_domain = \wp_get_theme()->get('TextDomain');
        $this->register();
    }

	/**
	 * Register the theme options containers
	 */
	public function register()
	{
		$basic_options_container = Container::make('theme_options', __('Theme Options', $this->text_domain))
			->add_fields([
				Field::make_text('ff_gtm_id', 'Google Tag Manager ID'),
				Field::make_text('ff_search_placeholder', 'Search Placeholder Text'),
				Field::make('complex', 'ff_colophon_text', 'Colophon Text')
					->add_fields( [
						Field::make('text', 'text'),
					]),
				Field::make('checkbox', 'ff_hide_home_cta', 'Hide home page CTA')
					->set_option_value('yes')
					->set_help_text('Check this box to hide the CTA buttons on the home page footer.')
			]);

		// social media
		Container::make('theme_options', __('Social Links', $this->text_domain))
			->set_page_parent($basic_options_container) // reference to a top level container
			->add_fields([
				Field::make('complex', 'ff_social_links', 'Social Media Links')
					->set_layout('tabbed-vertical')
					->add_fields(array(
						Field::make('text', 'url', 'URL')->set_help_text('The link to the respective social media page.'),
						Field::make('text', 'title', 'Title')->set_help_text('The title of the social media service or account, this will be displayed next to the icon.'),
						Field::make('text', 'icon')->set_help_text('The icon that will be displayed, please consult the <a target="_blank" href="https://fontawesome.com/icons?d=gallery">font awesome gallery</a>.')
					))
					->set_header_template('
						<% if (title) { %>
							<%- title %>
						<% } else { %>
							empty
						<% } %>
					'),

					Field::make('complex', 'ff_share_links', 'Share Links')
						->add_fields('ff_news_share_link', 'Share Link', [
							Field::make('text', 'icon'),
							Field::make('text', 'link')->set_help_text('The {$} token will be replaced with the URL to the article.'),
						]),
			]);


		// Alert
		Container::make('theme_options', __('Alert', $this->text_domain))
			->set_page_parent($basic_options_container) // reference to a top level container
			->add_fields([
				Field::make('checkbox', 'alert_active', 'Active')->set_help_text('Check this box to show the alert.'),
				Field::make('text', 'alert_label', 'Label')->set_help_text('The alert text.'),
				Field::make('text', 'alert_url', 'URL')->set_help_text('The URL the alert will link to.'),
				Field::make('text', 'alert_link_text', 'Button Text')->set_help_text('Button label'),
				Field::make('checkbox', 'alert_target', 'Open in new tab')->set_help_text('Open the link in a new browser tab.')
					->set_option_value( '_blank' )
			]);
	}
}
