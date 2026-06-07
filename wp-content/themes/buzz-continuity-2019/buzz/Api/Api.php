<?php 

namespace Firefly\Buzz\Api;

use Timber\Timber;
use Firefly\Buzz\Timber\BuzzPost;

class Api extends \WP_REST_controller
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'newsletterArticleController']);
    }

    public function newsletterArticleController()
    {
        register_rest_route('buzz/v1', '/newsletter/(?P<id>\d+)/articles', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'createResponse'],
            'args' => [
                'per_page' => [
                    'default' => 10,
                    'sanitize_callback' => [$this, 'sanitizeInteger']
                ],
                'page' => [
                    'default' => 1,
                    'sanitize_callback' => [$this, 'sanitizeInteger']
                ],
            ]
        ]);
    }

    /**
     * Create the request response object
     *
     * @param Object $request
     * @return Object 
     */
    public function createResponse($request)
    {
        return new \WP_REST_Response([
            'total_posts' => $this->getArticleCount($request),
            'posts' => $this->getArticles($request)
        ], 200);
    }

    /**
     * Get the articles for a given newsletter
     *
     * @param Object $request
     * @return Array
     */
    public function getArticles($request)
    {
        $posts =  Timber::get_posts([
            'posts_per_page'=> $request['per_page'],
            'offset'        => $request['per_page'] * ($request['page'] -1),
            'post_type' 	=> 'article',
            'post_status'	=> current_user_can('edit_others_posts') ? 'any' : 'publish',
            'orderby'		=> 'menu_order',
            'order'			=> 'ASC',
            'meta_query'    => [
                [
                    'key' 		=> 'ff_parent_id',
                    'value' 	=> $request['id'],
                    'compare'	=> '='
                ]
            ]
        ], 'Firefly\Buzz\Timber\BuzzPost');

        return array_map(function($post) {
            return [
                'author' => $post->author,
                'categories' => $post->terms('article_category'),
                'content' => $post->content,
                'id' => $post->id,
                'title' => $post->title,
                'thumbnail' => $post->thumbnail,
            ];
        }, $posts);
    }

    /**
     * Get the count of the articles that belong to the given newsletter
     *
     * @param Object $request
     * @return Integer
     */
    public function getArticleCount($request)
    {
        return count(get_posts([
            'posts_per_page'=> -1,
            'post_type' 	=> 'article',
            'post_status'	=> current_user_can('edit_others_posts') ? 'any' : 'publish',
            'orderby'		=> 'menu_order',
            'order'			=> 'ASC',
            'meta_query'    => [
                [
                    'key' 		=> 'ff_parent_id',
                    'value' 	=> $request['id'],
                    'compare'	=> '='
                ]
            ]
        ]));
    }

    /**
     * Ensure value is an integer
     *
     * @param Mixed $value
     * @return Integer
     */
    public function sanitizeInteger($value)
    {
        return intval($value);
    }
}
