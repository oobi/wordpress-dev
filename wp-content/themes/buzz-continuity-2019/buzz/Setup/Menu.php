<?php

namespace Firefly\Buzz\Setup;

use Firefly\Buzz\Core\Config;
use Timber\Menu as TimberMenu;

class Menu
{

    protected $menus;

    public function __construct()
    {
        $this->menus = Config::get('config')['menus'];

        add_action( 'after_setup_theme', array( $this, 'register_menus' ) );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
    }

    public function register_menus()
    {
        $menusToRegister = array();
        foreach ( $this->menus as $key => $value ) {
            $menusToRegister[$key] = esc_html__( $value , 'firefly' );
        }
        register_nav_menus( $menusToRegister );
    }

    public function add_to_context( $context )
    {
        foreach ( $this->menus as $key => $value ) {
            if( has_nav_menu( $key ) ) {
                $context['menus'][ $key ] = new TimberMenu( $key );
            }
        }
        return $context;
    }

}
