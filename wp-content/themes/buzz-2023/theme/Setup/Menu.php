<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;
use Timber\Menu as TimberMenu;

class Menu
{
    protected $menus;

    public function __construct()
    {
        $this->menus = Config::get('theme')['menus'];

        add_action( 'after_setup_theme', [$this, 'register_menus']);
        add_filter( 'timber_context', [$this, 'add_to_context']);
    }

    public function register_menus()
    {
        $menusToRegister = [];
        foreach ( $this->menus as $key => $value ) {
            $menusToRegister[$key] = esc_html__( $value['label'] , 'firefly' );
        }
        register_nav_menus( $menusToRegister );
    }

    public function add_to_context( $context )
    {
        foreach ( $this->menus as $key => $value ) {
            if( has_nav_menu( $key ) ) {
                $context['menus'][ $key ] = new TimberMenu($key, $value['args']);
            }
        }
        return $context;
    }

}
