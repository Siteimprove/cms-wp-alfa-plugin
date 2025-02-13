<?php
/**
 * Siteimprove Alfa plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Siteimprove Alfa
 * Description:       The Siteimprove plugin bridges the gap between Drupal and the Siteimprove Intelligence Platform via open source Alfa Engine.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.0
 * Author:            Siteimprove
 * Author URI:        https://siteimprove.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       siteimprove-alfa
 * Domain Path:       /languages
 *
 * @package siteimprove-alfa
 */

namespace Siteimprove\Alfa;

use Siteimprove\Alfa\Core\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die; // If this file is called directly, abort.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'SITEIMPROVE_ALFA_VERSION', '1.0.0' );
define( 'SITEIMPROVE_ALFA_PLUGIN_NAME', 'siteimprove-alfa' );
define( 'SITEIMPROVE_ALFA_PLUGIN_ROOT_PATH', trailingslashit( __DIR__ ) );
define( 'SITEIMPROVE_ALFA_PLUGIN_ROOT_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin class.
 */
class Siteimprove_Alfa {

	/**
	 * Init plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {
		$hook_registry = new Hook_Registry();

		if ( is_admin() ) {
			$hook_registry
				->add( new Admin\Navigation() )
				->add( new Admin\Dashboard_Page() );
		}

		$hook_registry->register_hooks();
	}
}

$siteimprove_alfa_plugin = new Siteimprove_Alfa();
$siteimprove_alfa_plugin->init();
