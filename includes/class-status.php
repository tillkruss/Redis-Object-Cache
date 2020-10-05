<?php
/**
 * Redis status
 *
 * @package Rhubarb\RedisCache
 */

namespace Rhubarb\RedisCache;

defined( '\\ABSPATH' ) || exit;

/**
 * Status class
 */
class Status {

    const ENABLED = 0;
    const CONNECTED = 1;
    const DROPIN_DETECTED = 2;
    const DROPIN_READABLE = 3;
    const DROPIN_INSTALLED = 4;
    const DROPIN_VALID = 5;
    const DROPIN_UP_TO_DATE = 6;

    /**
     * Retrieves the current status
     *
     * @return int
     */
    public static function get() {
        static $sum;
        if ( ! isset( $sum ) ) {
            $sum = 0;
            foreach ( self::constants() as $test_name => $exp ) {
                $cb = [ self::class, strtolower( "is_{$test_name}" ) ];
                if ( is_callable( $cb ) && call_user_func( $cb ) ) {
                    $sum += pow( 2, $exp );
                }
            }
        }
        return $sum;
    }

    /**
     * Retrieves a human readable status array
     *
     * @param bool $invert    Inverts the status only showing failures. Optional. Default `false`.
     * @param bool $translate Wheather to translate the constants. Optional. Default `false`.
     * @return array
     */
    public static function get_readable(
        $invert = false,
        $translate = false
    ) {
        $sum = self::get();
        $constants = self::constants();
        arsort( $constants );
        $status = [];
        foreach ( $constants as $name => $exp ) {
            $pow = pow( 2, $exp );
            $result = $sum >= $pow;
            $status[ $name ] = $result;
            if ( $result ) {
                $sum -= $pow;
            }
        }
        if ( $translate ) {
            foreach ( $status as $key => $value ) {
                unset( $status[ $key ] );
                $key = self::translate( $constants[ $key ], $value );
                $status[ $key ] = $value;
            }
        }
        $status = array_filter(
            $status,
            function( $value ) use ( $invert ) {
                return $invert !== $value;
            }
        );
        return array_keys( $status );
    }

    /**
     * Translates a status according to its state
     *
     * @param int  $constant One of the status class constants.
     * @param bool $state    The specific state to translate.
     * @return string
     */
    public static function translate( $constant, $state ) {
        $translations = [
            self::ENABLED => [
                0 => __( 'Disabled', 'redis-cache' ),
                1 => __( 'Enabled', 'redis-cache' ),
            ],
            self::CONNECTED => [
                0 => __( 'Not conncted', 'redis-cache' ),
                1 => __( 'Connected', 'redis-cache' ),
            ],
            self::DROPIN_DETECTED => [
                0 => __( 'No Drop-in detected', 'redis-cache' ),
                1 => __( 'Drop-in detected', 'redis-cache' ),
            ],
            self::DROPIN_READABLE => [
                0 => __( 'Drop-in not readable', 'redis-cache' ),
                1 => __( 'Drop-in readable', 'redis-cache' ),
            ],
            self::DROPIN_INSTALLED => [
                0 => __( 'A foreign object cache drop-in was found.', 'redis-cache' ),
                1 => __( 'Drop-in installed', 'redis-cache' ),
            ],
            self::DROPIN_VALID => [
                0 => __( 'Drop-in is invalid', 'redis-cache' ),
                1 => __( 'Drop-in is valid', 'redis-cache' ),
            ],
            self::DROPIN_UP_TO_DATE => [
                0 => __( 'Disconnected', 'redis-cache' ),
                1 => __( 'Connected', 'redis-cache' ),
            ],
        ];

        if ( isset( $translations[ $constant ][ (int) $state ] ) ) {
            return $translations[ $constant ][ (int) $state ];
        }

        return __( 'Unknown', 'redis-cache' );
    }

    /**
     * Retrives all class constants
     *
     * @return array
     */
    private static function constants() {
        $rc = new \ReflectionClass( self::class );
        return $rc->getConstants();
    }

    /**
     * Returns the drop-in path
     *
     * @return string
     */
    private static function dropin_path() {
        return WP_CONTENT_DIR . '/object-cache.php';
    }

    /**
     * Utility method to store `get_plugin_data` return
     *
     * @param string $path Path of the file to retrieve the plugin data from.
     * @return array
     */
    private static function file_plugin_data( $path ) {
        static $data = [];
        if ( ! isset( $data[ $path ] ) ) {
            $data[ $path ] = get_plugin_data( $path );
        }
        return $data[ $path ];
    }

    /**
     * Checks if the object cache is enabled
     */
    public static function is_enabled() {
        if ( defined( 'WP_REDIS_DISABLED' ) && WP_REDIS_DISABLED ) {
            return false;
        }

        return true;
    }

    /**
     * Checks if redis is connected
     *
     * @return bool
     */
    public static function is_connected() {
        global $wp_object_cache;

        if ( ! self::is_enabled() || ! self::is_dropin_valid() ) {
            return false;
        }

        if ( ! method_exists( $wp_object_cache, 'redis_status' ) ) {
            return false;
        }

        return $wp_object_cache->redis_status();
    }

    /**
     * Detects if an `object-cache.php` drop-in exists
     *
     * @return bool
     */
    public static function is_dropin_detected() {
        return file_exists( self::dropin_path() );
    }

    /**
     * Detects if the `object-cache.php` drop-in is readable
     *
     * @return bool
     */
    public static function is_dropin_readable() {
        return is_readable( self::dropin_path() );
    }

    /**
     * Detects if our `object-cache.php` drop-in is installed
     *
     * @return bool
     */
    public static function is_dropin_installed() {
        if ( ! self::is_dropin_readable() ) {
            return false;
        }

        $dropin = self::file_plugin_data( self::dropin_path() );
        $plugin = self::file_plugin_data( WP_REDIS_PLUGIN_PATH . '/includes/object-cache.php' );

        return $dropin['PluginURI'] === $plugin['PluginURI'];
    }

    /**
     * Checks if the `object-cache.php` drop-in is up to date
     *
     * @return bool
     */
    public static function is_dropin_up_to_date() {
        if ( ! self::is_dropin_readable() ) {
            return false;
        }

        $dropin = self::file_plugin_data( self::dropin_path() );
        $plugin = self::file_plugin_data( WP_REDIS_PLUGIN_PATH . '/includes/object-cache.php' );

        if ( $dropin['PluginURI'] === $plugin['PluginURI'] ) {
            return $dropin['Version'] === $plugin['Version'];
        }

        return false;
    }

    /**
     * Checks if the `object-cache.php` drop-in is valid
     *
     * @return bool
     */
    public static function is_dropin_valid() {
        return self::is_dropin_detected()
            && self::is_dropin_readable()
            && self::is_dropin_installed();
    }

}
