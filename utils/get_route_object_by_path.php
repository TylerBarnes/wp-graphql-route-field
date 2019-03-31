<?php

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Build url from strings and add normalized slashes.
 *
 * @return string
 */

function Wpgcu_build_url()
{
    $path = get_site_url();
    foreach (func_get_args() as $path_part) {
        $path .= '/' . ltrim($path_part, "/");
    }

    $path = rtrim($path, "/") . "/";
    
    return $path;
}

/**
 * Get WordPress route object by path
 *
 * @return object
 */
function Wpgcu_get_route_object_by_path($path)
{
    $url = Wpgcu_build_url($path);
    $route_id = url_to_postid($url);
    $route_object = get_post($route_id);

    return $route_object;
}
