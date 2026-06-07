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

    }
}