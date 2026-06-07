<?php

namespace Firefly\Carbon\Fields;

use Carbon_Fields\Field;
use Carbon_Fields\Container;

class HomePageFields
{
    public function __construct()
    {
        add_action('carbon_fields_register_fields', [$this, 'registerFields']);
    }

    public function registerFields()
    {
        $this->slider();        
    }

    public function slider()
    {
        Container::make( 'post_meta', 'Slider' )
            ->where('post_template', '=', 'controllers/page-home.php')
            ->add_fields([
                Field::make( 'complex', 'ff_slider', 'Slides' )
                    ->set_collapsed(true)
                    ->set_layout( 'tabbed-horizontal' )
                    ->add_fields( 'slide', 'Slide', [
                        Field::make( 'image', 'image', 'Image' )
                            ->set_required( TRUE ),

                        Field::make("text", "heading", "Heading"),

                        Field::make("text", "description", "Description"),

                        Field::make("text", "url", "Button URL")
                            ->set_width(33),

                        Field::make("text", "label", "Button Label")
                            ->set_width(33)
                            ->set_default_value( 'Read More' ),

                        Field::make( 'select', 'target', 'Button Target' )
                            ->set_width(33)
                            ->add_options([
                                '_self'     => 'Open in current tab',
                                '_blank'    => 'Open in new tab'
                            ])
                    ])->set_header_template('
                    <% if (heading) { %>
                        <%- heading %>
                    <% } else { %>
                        Slide
                    <% } %>
                ')
        ]);
    }
}