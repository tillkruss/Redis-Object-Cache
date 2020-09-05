<?php
/**
 * WordPress cache function definition
 *
 * @package Rhubarb\RedisCache
 */

defined( '\\ABSPATH' ) || exit;

/**
 * Adds a value to cache.
 *
 * If the specified key already exists, the value is not stored and the function
 * returns false.
 *
 * @param string $key        The key under which to store the value.
 * @param mixed  $value      The value to store.
 * @param string $group      The group value appended to the $key.
 * @param int    $expiration The expiration time, defaults to 0.
 *
 * @return bool              Returns TRUE on success or FALSE on failure.
 */
function wp_cache_add( $key, $value, $group = '', $expiration = 0 ) {
    global $wp_object_cache;

    return $wp_object_cache->add( $key, $value, $group, $expiration );
}

/**
 * Closes the cache.
 *
 * This function has ceased to do anything since WordPress 2.5. The
 * functionality was removed along with the rest of the persistent cache. This
 * does not mean that plugins can't implement this function when they need to
 * make sure that the cache is cleaned up after WordPress no longer needs it.
 *
 * @return  bool    Always returns True
 */
function wp_cache_close() {
    return true;
}

/**
 * Decrement a numeric item's value.
 *
 * @param string $key    The key under which to store the value.
 * @param int    $offset The amount by which to decrement the item's value.
 * @param string $group  The group value appended to the $key.
 *
 * @return int|bool      Returns item's new value on success or FALSE on failure.
 */
function wp_cache_decr( $key, $offset = 1, $group = '' ) {
    global $wp_object_cache;

    return $wp_object_cache->decrement( $key, $offset, $group );
}

/**
 * Remove the item from the cache.
 *
 * @param string $key    The key under which to store the value.
 * @param string $group  The group value appended to the $key.
 * @param int    $time   The amount of time the server will wait to delete the item in seconds.
 *
 * @return bool          Returns TRUE on success or FALSE on failure.
 */
function wp_cache_delete( $key, $group = '', $time = 0 ) {
    global $wp_object_cache;

    return $wp_object_cache->delete( $key, $group, $time );
}

/**
 * Invalidate all items in the cache. If `WP_REDIS_SELECTIVE_FLUSH` is `true`,
 * only keys prefixed with the `WP_REDIS_PREFIX` are flushed.
 *
 * @param int $delay  Number of seconds to wait before invalidating the items.
 *
 * @return bool       Returns TRUE on success or FALSE on failure.
 */
function wp_cache_flush( $delay = 0 ) {
    global $wp_object_cache;

    return $wp_object_cache->flush( $delay );
}

/**
 * Retrieve object from cache.
 *
 * Gets an object from cache based on $key and $group.
 *
 * @param string $key        The key under which to store the value.
 * @param string $group      The group value appended to the $key.
 * @param bool   $force      Optional. Whether to force an update of the local cache from the persistent
 *                           cache. Default false.
 * @param bool   $found      Optional. Whether the key was found in the cache. Disambiguates a return of false,
 *                           a storable value. Passed by reference. Default null.
 *
 * @return bool|mixed        Cached object value.
 */
function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
    global $wp_object_cache;

    return $wp_object_cache->get( $key, $group, $force, $found );
}

/**
 * Retrieves multiple values from the cache in one call.
 *
 * @param array  $keys  Array of keys under which the cache contents are stored.
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool   $force Optional. Whether to force an update of the local cache
 *                      from the persistent cache. Default false.
 * @return array Array of values organized into groups.
 */
function wp_cache_get_multiple( $keys, $group = '', $force = false ) {
    global $wp_object_cache;

    return $wp_object_cache->get_multiple( $keys, $group, $force );
}

/**
 * Increment a numeric item's value.
 *
 * @param string $key    The key under which to store the value.
 * @param int    $offset The amount by which to increment the item's value.
 * @param string $group  The group value appended to the $key.
 *
 * @return int|bool      Returns item's new value on success or FALSE on failure.
 */
function wp_cache_incr( $key, $offset = 1, $group = '' ) {
    global $wp_object_cache;

    return $wp_object_cache->increment( $key, $offset, $group );
}

/**
 * Sets up Object Cache Global and assigns it.
 *
 * @return  void
 */
function wp_cache_init() {
    global $wp_object_cache;

    // Backwards compatibility: map `WP_CACHE_KEY_SALT` constant to `WP_REDIS_PREFIX`.
    if ( defined( 'WP_CACHE_KEY_SALT' ) && ! defined( 'WP_REDIS_PREFIX' ) ) {
        define( 'WP_REDIS_PREFIX', WP_CACHE_KEY_SALT );
    }

    if ( ! ( $wp_object_cache instanceof WP_Object_Cache ) ) {
        $fail_gracefully = ! defined( 'WP_REDIS_GRACEFUL' ) || WP_REDIS_GRACEFUL;

        // We need to override this WordPress global in order to inject our cache.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        $wp_object_cache = new WP_Object_Cache( $fail_gracefully );
    }
}

/**
 * Replaces a value in cache.
 *
 * This method is similar to "add"; however, is does not successfully set a value if
 * the object's key is not already set in cache.
 *
 * @param string $key        The key under which to store the value.
 * @param mixed  $value      The value to store.
 * @param string $group      The group value appended to the $key.
 * @param int    $expiration The expiration time, defaults to 0.
 *
 * @return bool              Returns TRUE on success or FALSE on failure.
 */
function wp_cache_replace( $key, $value, $group = '', $expiration = 0 ) {
    global $wp_object_cache;

    return $wp_object_cache->replace( $key, $value, $group, $expiration );
}

/**
 * Sets a value in cache.
 *
 * The value is set whether or not this key already exists in Redis.
 *
 * @param string $key        The key under which to store the value.
 * @param mixed  $value      The value to store.
 * @param string $group      The group value appended to the $key.
 * @param int    $expiration The expiration time, defaults to 0.
 *
 * @return bool              Returns TRUE on success or FALSE on failure.
 */
function wp_cache_set( $key, $value, $group = '', $expiration = 0 ) {
    global $wp_object_cache;

    return $wp_object_cache->set( $key, $value, $group, $expiration );
}

/**
 * Switch the internal blog id.
 *
 * This changes the blog id used to create keys in blog specific groups.
 *
 * @param  int $_blog_id The blog ID.
 *
 * @return bool
 */
function wp_cache_switch_to_blog( $_blog_id ) {
    global $wp_object_cache;

    return $wp_object_cache->switch_to_blog( $_blog_id );
}

    /**
     * Adds a group or set of groups to the list of Redis groups.
     *
     * @param   string|array $groups     A group or an array of groups to add.
     *
     * @return  void
     */
function wp_cache_add_global_groups( $groups ) {
    global $wp_object_cache;

    $wp_object_cache->add_global_groups( $groups );
}

/**
 * Adds a group or set of groups to the list of non-Redis groups.
 *
 * @param   string|array $groups     A group or an array of groups to add.
 *
 * @return  void
 */
function wp_cache_add_non_persistent_groups( $groups ) {
    global $wp_object_cache;

    $wp_object_cache->add_non_persistent_groups( $groups );
}
