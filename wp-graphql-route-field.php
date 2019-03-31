<?php
/**
 * Plugin Name: WP GraphQL Route Field
 * Description: A WP GraphQL extension to get routes by URL path
 * Author: Tyler <tylerdbarnes@gmail.com>
 * Author URI: tylerbarnes.ca
 *
 * @author Tyler Barnes <tylerdbarnes@gmail.com>
 * @version 0.0.1
 *
 */

if (! defined('ABSPATH')) {
    exit;
}


use WPGraphQL\Types;
use WPGraphQL\TypeRegistry;

require_once __DIR__ . '/utils/get_route_object_by_path.php';


add_action(
    'graphql_register_types',
    function () {

        /**
         * Get all available GQL post types
         */
        $possible_types     = [];
        $allowed_post_types = \WPGraphQL::$allowed_post_types;

        if (! empty($allowed_post_types) && is_array($allowed_post_types)) {
            foreach ($allowed_post_types as $allowed_post_type) {
                if (empty($possible_types[ $allowed_post_type ])) {
                    $possible_types[ $allowed_post_type ]
                        = Types::post_object($allowed_post_type);
                }
            }
        }
        

        /**
         * From our $allowed_post_types, get GQL field types to make a
         * union type of all post types
         */
        $route_types = [];

        foreach ($allowed_post_types as $allowed_post_type) {
            $post_type_obj = get_post_type_object($allowed_post_type);
            $graphql_single_name
                = $post_type_obj->graphql_single_name ?? false;

            if ($graphql_single_name) {
                $route_types[] = $graphql_single_name;
            }
        }

        /**
         * Register Union type of all available GraphQL post types
         */
        register_graphql_union_type(
            'RouteUnion',
            [
            'name'        => 'RouteUnion',
            'typeNames'   => $route_types,
            'resolveType' => function ($source) {
                $post_type_obj = get_post_type_object($source->post_type);
                
                $graphql_single_name
                    = $post_type_obj->graphql_single_name ?? false;

                if ($graphql_single_name) {
                    $type = TypeRegistry::get_type($graphql_single_name);
                } else {
                    $type = null;
                }

                return $type;
            },
            ]
        );

        /**
         * Register our graphql field "route"
         * Use it like -> route(path: "/about-page/") { ... }
         * in GraphQL
         */
        register_graphql_field(
            'RootQuery',
            'route',
            [
            'type' => 'RouteUnion',
            'description' => __(
                'Returns post object for any page or post by path',
                'wp-graphql'
            ),
            'args'        => [
                'path' => [
                    'type' => [
                        'non_null' => 'String',
                    ],
                ],
            ],
            'resolve' => function ($source, array $args) use ($allowed_post_types) {
                $route_object = Wpgcu_get_route_object_by_path($args['path']);

                return $route_object;
            },
            ]
        );
    }
);
