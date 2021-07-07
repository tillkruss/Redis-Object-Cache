<?php
/**
 * REST Controller
 *
 * @package Rhubarb\RedisCache
 */

namespace Rhubarb\RedisCache\REST_API;

use Rhubarb\RedisCache\Status;
use WP_REST_Controller;

defined( '\\ABSPATH' ) || exit;

/**
 * REST Controller class
 */
abstract class REST_Controller extends WP_REST_Controller {

    /**
     * Default route namespace
     *
     * @var string
     */
    protected $namespace = 'redis-cache/v1';

    /**
     * Checks if a given request has access to get items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {

        if ( ! Status::is_dropin_valid() ) {
            return new WP_Error(
                'redis_cache_rest_not_initialized',
                __( 'Redis object cache is not initialized', 'redis-cache' ),
                [ 'status' => 404 ]
            );
        }

        if ( ! Status::is_connected() ) {
            return new WP_Error(
                'redis_cache_rest_not_connected',
                __( 'Redis object cache is not connected', 'redis-cache' ),
                [ 'status' => 404 ]
            );
        }

        return true;
    }

}
