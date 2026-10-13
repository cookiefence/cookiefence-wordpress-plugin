<?php
/** Offline regression tests against the bundled GitHub API implementation. */
define( 'ABSPATH', __DIR__ . '/' );
$filters = array();
function add_filter( $name, $callback, ...$unused ) {
	$GLOBALS['filters'][ $name ] = $callback;
}
function apply_filters( $name, $value, ...$unused ) {
	return isset( $GLOBALS['filters'][ $name ] ) ? call_user_func( $GLOBALS['filters'][ $name ], $value ) : $value;
}
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
class WP_Error {}

require dirname( __DIR__ ) . '/plugin-update-checker/plugin-update-checker.php';
require dirname( __DIR__ ) . '/includes/updater.php';

class FixtureGitHubApi extends \YahnisElsts\PluginUpdateChecker\v5p7\Vcs\GitHubApi {
	public $response;
	protected function api( $url, $queryParams = array() ) {
		if ( '/repos/:user/:repo/releases/latest' !== $url ) {
			throw new RuntimeException( 'Unexpected fallback request: ' . $url );
		}
		return $this->response;
	}
}

function check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	echo 'PASS: ' . $message . "\n";
}

$api = new FixtureGitHubApi( 'https://github.com/cookiefence/cookiefence-wordpress-plugin/' );
cookiefence_configure_update_api( $api );
$asset = (object) array(
	'name' => 'cookiefence.zip',
	'browser_download_url' => 'https://github.com/cookiefence/cookiefence-wordpress-plugin/releases/download/v1.1.0/cookiefence.zip',
	'download_count' => 1,
);
$release = (object) array(
	'tag_name' => 'v1.1.0', 'draft' => false, 'prerelease' => false,
	'zipball_url' => 'https://example.invalid/source.zip', 'created_at' => '2026-10-13',
	'assets' => array( (object) array( 'name' => 'unrelated.zip', 'download_count' => 0 ), $asset ),
);
$api->response = $release;
$reference = $api->chooseReference( 'master' );
check( $reference && '1.1.0' === $reference->version && $asset->browser_download_url === $reference->downloadUrl, 'Select the named installable ZIP even when it is not the first asset' );
foreach ( array( 'draft', 'prerelease' ) as $flag ) {
	$api->response = clone $release;
	$api->response->$flag = true;
	check( null === $api->chooseReference( 'master' ), 'Ignore ' . $flag . ' without tag or branch fallback' );
}
$api->response = clone $release;
$api->response->assets = array( (object) array( 'name' => 'source.zip', 'download_count' => 0 ) );
check( null === $api->chooseReference( 'master' ), 'Reject releases without cookiefence.zip' );
$api->response->assets = array();
check( null === $api->chooseReference( 'master' ), 'Reject releases with no assets' );
$api->response = new WP_Error();
check( null === $api->chooseReference( 'master' ), 'API failure never falls back to source snapshots' );
