<?php
/**
 * Plugin Name: CookieFence – Cookie Consent Banner
 * Plugin URI: https://github.com/cookiefence/cookiefence-wordpress-plugin
 * Description: Connect your WordPress site to CookieFence to display a customizable cookie consent banner and manage visitor consent. Requires a CookieFence account.
 * Version: 1.0.1
 * Update URI: https://github.com/cookiefence/cookiefence-wordpress-plugin
 * Author: CookieFence
 * Author URI: https://cookiefence.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Text Domain: cookiefence
 *
 * @package CookieFence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const COOKIEFENCE_OPTION_SITE_UUID = 'cookiefence_site_uuid';
const COOKIEFENCE_HEAD_PRIORITY     = -999999;

if ( ! defined( 'COOKIEFENCE_TAG_BASE_URL' ) ) {
	define( 'COOKIEFENCE_TAG_BASE_URL', 'https://tag.cookiefence.com' );
}

require_once __DIR__ . '/includes/updater.php';
add_action( 'plugins_loaded', 'cookiefence_initialize_updater' );

register_activation_hook( __FILE__, 'cookiefence_schedule_setup_redirect' );
add_action( 'admin_init', 'cookiefence_maybe_redirect_to_setup' );
add_action( 'admin_menu', 'cookiefence_add_settings_page' );
add_action( 'admin_init', 'cookiefence_register_settings' );
add_action( 'wp_head', 'cookiefence_render_head_tags', COOKIEFENCE_HEAD_PRIORITY );

/**
 * Schedules setup for the activating administrator after WordPress finishes activation.
 *
 * @param bool $network_wide Whether this is a network activation.
 */
function cookiefence_schedule_setup_redirect( $network_wide ) {
	if ( $network_wide || ! is_admin() || wp_doing_ajax()
		|| ( defined( 'WP_CLI' ) && WP_CLI )
		|| ! current_user_can( 'manage_options' )
		|| isset( $_REQUEST['activate-multi'] )
		|| ( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] )
		|| ( isset( $_REQUEST['action2'] ) && 'activate-selected' === $_REQUEST['action2'] ) ) {
		return;
	}

	set_transient( 'cookiefence_setup_redirect_' . get_current_user_id(), true, MINUTE_IN_SECONDS );
}

/**
 * Sends the activating administrator to the UUID field once, on the next admin request.
 */
function cookiefence_maybe_redirect_to_setup() {
	if ( wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	$key = 'cookiefence_setup_redirect_' . get_current_user_id();
	if ( ! get_transient( $key ) ) {
		return;
	}

	delete_transient( $key );
	if ( is_network_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	wp_safe_redirect( admin_url( 'options-general.php?page=cookiefence' ) );
	exit;
}

/**
 * Adds the CookieFence settings page under the WordPress Settings menu.
 */
function cookiefence_add_settings_page() {
	add_options_page(
		__( 'CookieFence', 'cookiefence' ),
		__( 'CookieFence', 'cookiefence' ),
		'manage_options',
		'cookiefence',
		'cookiefence_render_settings_page'
	);
}

/**
 * Registers the CookieFence option and settings field.
 */
function cookiefence_register_settings() {
	register_setting(
		'cookiefence',
		COOKIEFENCE_OPTION_SITE_UUID,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'cookiefence_sanitize_site_uuid',
			'default'           => '',
		)
	);

	add_settings_section(
		'cookiefence_main',
		__( 'CookieFence installation', 'cookiefence' ),
		'cookiefence_render_settings_section',
		'cookiefence'
	);

	add_settings_field(
		COOKIEFENCE_OPTION_SITE_UUID,
		__( 'Site UUID', 'cookiefence' ),
		'cookiefence_render_site_uuid_field',
		'cookiefence',
		'cookiefence_main'
	);
}

/**
 * Sanitizes and validates the CookieFence site UUID.
 *
 * @param string $value Submitted option value.
 * @return string Sanitized UUID, empty string, or previous valid option value.
 */
function cookiefence_sanitize_site_uuid( $value ) {
	$uuid = strtolower( trim( (string) $value ) );

	if ( '' === $uuid ) {
		return '';
	}

	if ( cookiefence_is_valid_uuid( $uuid ) ) {
		return $uuid;
	}

	add_settings_error(
		COOKIEFENCE_OPTION_SITE_UUID,
		'cookiefence_invalid_site_uuid',
		__( 'Enter a valid CookieFence site UUID.', 'cookiefence' ),
		'error'
	);

	return (string) get_option( COOKIEFENCE_OPTION_SITE_UUID, '' );
}

/**
 * Checks whether a value is a standard UUID.
 *
 * @param string $value Value to validate.
 * @return bool True when value is a valid UUID.
 */
function cookiefence_is_valid_uuid( $value ) {
	return 1 === preg_match(
		'/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
		$value
	);
}

/**
 * Renders the settings section description.
 */
function cookiefence_render_settings_section() {
	echo '<p>' . esc_html__( 'Paste the site UUID from the CookieFence admin UI.', 'cookiefence' ) . '</p>';
}

/**
 * Renders the site UUID input field.
 */
function cookiefence_render_site_uuid_field() {
	$site_uuid = (string) get_option( COOKIEFENCE_OPTION_SITE_UUID, '' );

	printf(
		'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" autocomplete="off" placeholder="3bbc9d39-4f04-4e11-b09f-c0bc4c4e9d38" />',
		esc_attr( COOKIEFENCE_OPTION_SITE_UUID ),
		esc_attr( $site_uuid )
	);
}

/**
 * Renders the CookieFence settings page.
 */
function cookiefence_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'CookieFence', 'cookiefence' ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'cookiefence' );
			do_settings_sections( 'cookiefence' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Outputs CookieFence tags in the public document head.
 */
function cookiefence_render_head_tags() {
	if ( is_admin() || cookiefence_is_login_request() ) {
		return;
	}

	$site_uuid = (string) get_option( COOKIEFENCE_OPTION_SITE_UUID, '' );

	if ( ! cookiefence_is_valid_uuid( $site_uuid ) ) {
		return;
	}

	$tag_base_url = cookiefence_get_tag_base_url();

	if ( '' === $tag_base_url ) {
		return;
	}

	$script_url   = $tag_base_url . '/tags/' . rawurlencode( $site_uuid ) . '.js';

	printf(
		'<link rel="preconnect" href="%s" crossorigin>' . "\n",
		esc_url( $tag_base_url )
	);
	printf(
		'<script id="CookieFence" src="%s"></script>' . "\n",
		esc_url( $script_url )
	);
}

/**
 * Gets the CookieFence tag base URL.
 *
 * The default can be overridden in wp-config.php before plugins load:
 * define( 'COOKIEFENCE_TAG_BASE_URL', 'https://tag.dev.cookiefence.com' );
 *
 * @return string Normalized base URL without a trailing slash.
 */
function cookiefence_get_tag_base_url() {
	return untrailingslashit( trim( (string) COOKIEFENCE_TAG_BASE_URL ) );
}

/**
 * Detects WordPress login and registration requests.
 *
 * @return bool True when the current request is for wp-login.php.
 */
function cookiefence_is_login_request() {
	global $pagenow;

	return 'wp-login.php' === $pagenow;
}
