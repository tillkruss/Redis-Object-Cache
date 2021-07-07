<?php
/**
 * REST API class
 *
 * @package Rhubarb\RedisCache
 */

namespace Rhubarb\RedisCache;

defined( '\\ABSPATH' ) || exit;

/**
 * REST_API class definition
 */
class REST_API {

    /**
     * Controller storage
     *
     * @var array
     */
    private static $controllers = [];

    /**
     * Initialization method
     *
     * @return void
     */
    public static function init() {
        add_action( 'rest_api_init', [ self::class, 'register_routes' ] );
    }

    /**
     * Registers all controllers and their routes
     *
     * @return void
     */
    public static function register_routes() {
        foreach ( self::namespaces() as $namespace => $controllers ) {
            foreach ( $controllers as $controller_name => $controller_class ) {
                $controller = new $controller_class();
                $controller->register_routes();
                self::$controllers[ $namespace ][ $controller_name ] = $controller;
            }
        }
    }

    /**
     * Retrieves a specific registered controller
     *
     * @param string $namespace The namespace of the controller.
     * @param string $name      The controller name.
     * @return Rhubarb\RedisCache\API\Controller|null
     */
    public static function get_controller( $namespace, $name ) {
        if ( isset( self::$controllers[ $namespace ][ $name ] ) ) {
            return self::$controllers[ $namespace ][ $name ];
        }
        return null;
    }

    /**
     * Lists all namespaces and their controllers
     *
     * @return array
     */
    protected static function namespaces() {
        return apply_filters(
            'redis_object_cache_api_namespaces',
            [
                'redis-cache/v1' => self::v1_controllers(),
            ]
        );
    }

    /**
     * Version 1 controllers
     *
     * @return array
     */
    protected static function v1_controllers() {
        $namespace = __NAMESPACE__ . '\\REST_API\\Controllers\\Version1';
        return [
            'metrics' => "{$namespace}\\Metrics",
        ];
    }

}
