<?php

namespace Firefly\Setup;

class WordPress
{
    public function __construct()
    {
        // add_filter('get_search_form', [$this, 'searchform'], 100);
    }

    public function searchform($form) {
        return '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
        <div class="input-group">
        <input class="form-control" type="text" placeholder="Search" value="' . get_search_query() . '" name="s" id="s" />
        <label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
        <div class="input-group-append">
        <input class="btn btn-primary" type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
        </div>
        </div>
        </form>';
    }
}
