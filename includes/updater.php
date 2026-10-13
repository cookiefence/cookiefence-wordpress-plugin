<?php
/** GitHub release updates for CookieFence. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the bundled updater after all plugins have loaded. */
function cookiefence_initialize_updater() {
	require_once dirname( __DIR__ ) . '/plugin-update-checker/plugin-update-checker.php';
	$checker = \YahnisElsts\PluginUpdateChecker\v5p7\PucFactory::buildUpdateChecker(
		'https://github.com/cookiefence/cookiefence-wordpress-plugin/',
		dirname( __DIR__ ) . '/cookiefence.php',
		'cookiefence'
	);
	cookiefence_configure_update_api( $checker->getVcsApi() );
}

/** Only install complete, published release assets; never source snapshots. */
function cookiefence_configure_update_api( $api ) {
	$api->enableReleaseAssets(
		'/^cookiefence\.zip$/',
		\YahnisElsts\PluginUpdateChecker\v5p7\Vcs\Api::REQUIRE_RELEASE_ASSETS
	);
	$api->setStrategyFilterName( 'cookiefence_update_strategies' );
	add_filter( 'cookiefence_update_strategies', 'cookiefence_release_update_strategy' );
}

/** Remove the upstream library's tag and branch fallbacks. */
function cookiefence_release_update_strategy( $strategies ) {
	return array_intersect_key(
		$strategies,
		array( \YahnisElsts\PluginUpdateChecker\v5p7\Vcs\Api::STRATEGY_LATEST_RELEASE => true )
	);
}
