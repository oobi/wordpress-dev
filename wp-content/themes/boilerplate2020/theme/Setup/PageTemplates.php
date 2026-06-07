<?php 

namespace Firefly\Setup;

class PageTemplates
{
    // Add templates to exclude the editor from here page-home.php etc
    private $templates = [];

    function __construct()
    {
        add_action( 'admin_init', [$this, 'remove_editor_from_pages']);
    }

    public function remove_editor_from_pages()
    {
        if(! is_admin()) {
            return;
        }

        if(in_array($this->get_slug(), $this->templates)) {
            remove_post_type_support('page', 'editor');
        }
    }

    private function get_slug()
    {
        $post_id = $_GET['post'] ?? 0;

        return basename(get_page_template_slug($post_id));
    }
}