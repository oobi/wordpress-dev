<?php

namespace Firefly\Carbon\Fields;

use Carbon_Fields\Field;
use Carbon_Fields\Container;

class EventFields
{
    public function __construct()
    {
        add_action('carbon_fields_register_fields', [$this, 'registerFields']);
    }

    public function registerFields()
    {
        Container::make('post_meta', 'Event Information')
			->where('post_type', '=', 'event')
			->add_fields([
				Field::make('date', 'ff_event_date', 'Date')->set_required(true)->set_width(50),
				Field::make('date', 'ff_event_end', 'End Date (optional)')
					->help_text('If this event is held over multiple dates, this is the date of the last occurrence.')
					->set_width(50),
				Field::make('text', 'ff_event_location', 'Location')->set_width(100)->set_required(true),
				Field::make('time', 'ff_event_time', 'Start Time')->set_width(50)->set_required(true),
				Field::make('time', 'ff_event_time_end', 'End Time')->set_width(50)->set_required(true),
	
				Field::make('complex', 'ff_event_meta', 'Additional Information')
					->set_layout('tabbed-vertical')
					->add_fields('item', 'Item', [
						Field::make('text', 'label', 'Label')->set_width(50),
						Field::make('text', 'data', 'Data')->set_width(50),
					])
					->set_header_template('
						<% if (label) { %>
							<%- label %>
						<% } else { %>
							Item
						<% } %>
					')
			]);
    }
}