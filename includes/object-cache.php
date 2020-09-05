<?php
/**
 * Plugin Name: Redis Object Cache Drop-In
 * Plugin URI: http://wordpress.org/plugins/redis-cache/
 * Description: A persistent object cache backend powered by Redis. Supports Predis, PhpRedis, Credis, HHVM, replication, clustering and WP-CLI.
 * Version: 2.0.13
 * Author: Till Krüss
 * Author URI: https://objectcache.pro
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Rhubarb\RedisCache
 */

defined( '\\ABSPATH' ) || exit;

if ( defined( 'WP_REDIS_DISABLED' ) && WP_REDIS_DISABLED ) {
    return;
}

$settings_path = WP_CONTENT_DIR . '/redis_object_cache/settings.php';
if ( is_readable( $settings_path ) ) {
    require_once $settings_path;
    require_once WP_REDIS_PLUGIN_PATH . '/includes/cache-functions.php';
    require_once WP_REDIS_PLUGIN_PATH . '/includes/class-wp-object-cache.php';
}
