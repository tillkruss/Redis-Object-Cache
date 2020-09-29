<?php
/**
 * Overview tab template
 *
 * @package Rhubarb\RedisCache
 */

use Rhubarb\RedisCache\Plugin;

defined( '\\ABSPATH' ) || exit;

$redis_client = Plugin::get_redis_client_name();
$redis_prefix = Plugin::get_redis_prefix();
$redis_maxttl = Plugin::get_redis_maxttl();
$redis_version = Plugin::get_redis_version();

$diagnostics = Plugin::get_diagnostics();

?>

<h2 class="title">
    <?php esc_html_e( 'Overview', 'redis-cache' ); ?>
</h2>

<table class="form-table">

    <?php if ( ! is_null( $redis_client ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Client:', 'redis-cache' ); ?></th>
            <td>
                <code><?php echo esc_html( $redis_client ); ?></code>
            </td>
        </tr>
    <?php endif; ?>

    <tr>
        <th><?php esc_html_e( 'Drop-in:', 'redis-cache' ); ?></th>
        <td>
            <code>
                <?php if ( ! Plugin::object_cache_dropin_exists() ) : ?>
                    <?php esc_html_e( 'Not installed', 'redis-cache' ); ?>
                <?php elseif ( Plugin::object_cache_dropin_outdated() ) : ?>
                    <?php esc_html_e( 'Outdated', 'redis-cache' ); ?>
                <?php else : ?>
                    <?php
                        Plugin::validate_object_cache_dropin()
                            ? esc_html_e( 'Valid', 'redis-cache' )
                            : esc_html_e( 'Invalid', 'redis-cache' );
                    ?>
                <?php endif; ?>
            </code>
        </td>
    </tr>

    <?php if ( defined( 'WP_REDIS_DISABLED' ) && WP_REDIS_DISABLED ) : ?>
        <tr>
            <th><?php esc_html_e( 'Disabled:', 'redis-cache' ); ?></th>
            <td>
                <code><?php esc_html_e( 'Yes', 'redis-cache' ); ?></code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( ! is_null( $redis_prefix ) && trim( $redis_prefix ) !== '' ) : ?>
        <tr>
            <th><?php esc_html_e( 'Key Prefix:', 'redis-cache' ); ?></th>
            <td>
                <code><?php echo esc_html( $redis_prefix ); ?></code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( ! is_null( $redis_maxttl ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Max. TTL:', 'redis-cache' ); ?></th>
            <td>
                <code><?php echo esc_html( $redis_maxttl ); ?></code>

                <?php if ( ! is_int( $redis_maxttl ) && ! ctype_digit( $redis_maxttl ) ) : ?>
                    <p class="description is-notice">
                        <?php esc_html_e( 'This doesn’t appear to be a valid number.', 'redis-cache' ); ?>
                    </p>
                <?php endif; ?>
            </td>
        </tr>
    <?php endif; ?>

</table>

<h2 class="title">
    <?php esc_html_e( 'Connection', 'redis-cache' ); ?>
</h2>

<table class="form-table">

    <tr>
        <th><?php esc_html_e( 'Status:', 'redis-cache' ); ?></th>
        <td><code><?php echo esc_html( Plugin::get_status() ); ?></code></td>
    </tr>

    <?php if ( ! empty( $diagnostics['host'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Host:', 'redis-cache' ); ?></th>
            <td><code><?php echo esc_html( $diagnostics['host'] ); ?></code></td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['cluster'] ) && is_array( $diagnostics['cluster'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Cluster:', 'redis-cache' ); ?></th>
            <td>
                <ul>
                    <?php foreach ( $diagnostics['cluster'] as $node ) : ?>
                        <li><code><?php echo esc_html( $node ); ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['shards'] ) && is_array( $diagnostics['shards'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Shards:', 'redis-cache' ); ?></th>
            <td>
                <ul>
                    <?php foreach ( $diagnostics['shards'] as $node ) : ?>
                        <li><code><?php echo esc_html( $node ); ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['servers'] ) && is_array( $diagnostics['servers'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Servers:', 'redis-cache' ); ?></th>
            <td>
                <ul>
                    <?php foreach ( $diagnostics['servers'] as $node ) : ?>
                        <li><code><?php echo esc_html( $node ); ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( ! empty( $diagnostics['port'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Port:', 'redis-cache' ); ?></th>
            <td><code><?php echo esc_html( $diagnostics['port'] ); ?></code></td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['password'][0] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Username:', 'redis-cache' ); ?></th>
            <td><code><?php echo esc_html( $diagnostics['password'][0] ); ?></code></td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['password'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Password:', 'redis-cache' ); ?></th>
            <td>
                <code>••••••••</code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['database'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Database:', 'redis-cache' ); ?></th>
            <td><code><?php echo esc_html( $diagnostics['database'] ); ?></code></td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['timeout'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Connection Timeout:', 'redis-cache' ); ?></th>
            <td>
                <code>
                    <?php
                        echo sprintf(
                            // translators: %s = Redis connection/read timeout in seconds.
                            esc_html__( '%ss', 'redis-cache' ),
                            esc_html( $diagnostics['timeout'] )
                        );
                    ?>
                </code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['read_timeout'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Read Timeout:', 'redis-cache' ); ?></th>
            <td>
                <code>
                    <?php
                        echo sprintf(
                            // translators: %s = Redis connection/read timeout in seconds.
                            esc_html__( '%ss', 'redis-cache' ),
                            esc_html( $diagnostics['read_timeout'] )
                        );
                    ?>
                </code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( isset( $diagnostics['retry_interval'] ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Retry Interval:', 'redis-cache' ); ?></th>
            <td>
                <code>
                    <?php
                        echo sprintf(
                            // translators: %s = Redis retry interval in milliseconds.
                            esc_html__( '%sms', 'redis-cache' ),
                            esc_html( $diagnostics['retry_interval'] )
                        );
                    ?>
                </code>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( ! is_null( $redis_version ) ) : ?>
        <tr>
            <th><?php esc_html_e( 'Redis Version:', 'redis-cache' ); ?></th>
            <td><code><?php echo esc_html( $redis_version ) ?: esc_html_e( 'Unknown', 'redis-cache' ); ?></code></td>
        </tr>
    <?php endif; ?>

</table>

<p class="submit">

    <?php if ( Plugin::get_redis_status() ) : ?>
        <a href="<?php echo esc_attr( Plugin::action_link( 'flush-cache' ) ); ?>" class="button button-primary button-large">
            <?php esc_html_e( 'Flush Cache', 'redis-cache' ); ?>
        </a> &nbsp;
    <?php endif; ?>

    <?php if ( Plugin::validate_object_cache_dropin() ) : ?>
        <a href="<?php echo esc_attr( Plugin::action_link( 'disable-cache' ) ); ?>" class="button button-secondary button-large">
            <?php esc_html_e( 'Disable Object Cache', 'redis-cache' ); ?>
        </a>
    <?php else : ?>
        <a href="<?php echo esc_attr( Plugin::action_link( 'enable-cache' ) ); ?>" class="button button-primary button-large">
            <?php esc_html_e( 'Enable Object Cache', 'redis-cache' ); ?>
        </a>
    <?php endif; ?>

</p>
