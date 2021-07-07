<?php
/**
 * API metrics
 *
 * @package Rhubarb\RedisCache
 */

namespace Rhubarb\RedisCache\REST_API\Controllers\Version1;

use Rhubarb\RedisCache\REST_API\REST_Controller;
use Rhubarb\RedisCache\Metrics as PluginMetrics;
use WP_REST_Server;
use WP_Error;
use Exception;

defined( '\\ABSPATH' ) || exit;

/**
 * REST API Metrics controller class.
 */
class Metrics extends REST_Controller {

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'metrics';

    /**
     * Retrieves the item's schema, conforming to JSON Schema.
     *
     * @return array Item schema data.
     */
    public function get_item_schema() {
        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'metrics',
            'description' => __( 'Metrics data objects', 'redis-cache' ),
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'timestamp' => [
                    'description' => __( 'Timestamp', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
                'hits' => [
                    'description' => __( 'Hits', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
                'misses' => [
                    'description' => __( 'Misses', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
                'ratio' => [
                    'description' => __( 'Hit/miss ratio', 'redis-cache' ),
                    'type' => 'number',
                    'context' => [ 'view' ],
                ],
                'bytes' => [
                    'description' => __( 'Bytes fetched', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
                'time' => [
                    'description' => __( 'Data retrieval time', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
                'calls' => [
                    'description' => __( 'Calls', 'redis-cache' ),
                    'type' => 'integer',
                    'context' => [ 'view' ],
                ],
            ],
        ];

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items( $request ) {
        $data = [];
        $metrics = PluginMetrics::get( (int) $request['seconds'] );

        foreach ( $metrics as $metric ) {
            $metric = $this->prepare_item_for_response( $metric, $request );
            $metric = $this->prepare_response_for_collection( $metric );
            $data[] = $metric;
        }

        return rest_ensure_response( $data );
    }

    /**
     * Prepare a metric array for response.
     *
     * @param array           $metric  Metrics object.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response( $metric, $request ) {
        // Wrap the data in a response object.
        $response = rest_ensure_response( $metric );

        /**
         * Filter metric object retrieved from redis
         *
         * @param WP_REST_Response           $response The response object.
         * @param Rhubarb\RedisCache\Metrics $metric   Metrics object.
         * @param WP_REST_Request            $request  Request object.
         */
        return apply_filters( 'redis_object_cache_rest_prepare_metric', $response, $metric, $request );
    }

    /**
     * Checks if a given request has access to get items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {

        if ( ! PluginMetrics::is_enabled() ) {
            return new WP_Error(
                'redis_cache_rest_metrics_disabled',
                __( 'Redis object cache metrics were disabled', 'redis-cache' ),
                [ 'status' => 404 ]
            );
        }

        return parent::get_items_permissions_check( $request );
    }

    /**
     * Register the routes for metrics.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                'schema' => [ $this, 'get_public_item_schema' ],
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_items' ],
                'permission_callback' => [ $this, 'get_items_permissions_check' ],
                'args' => [
                    'seconds' => [
                        'description' => __( 'Seconds of metrics to retrieve.', 'redis-cache' ),
                        'type' => 'integer',
                        'default' => PluginMetrics::max_time(),
                        'validate_callback' => function( $param, $request, $key ) {
                            return is_numeric( $param );
                        },
                        'sanitize_callback' => function( $param, $request, $key ) {
                            return (int) $param;
                        },
                    ],
                ],
            ]
        );
    }

}
