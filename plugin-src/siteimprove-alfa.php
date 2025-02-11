<?php
/**
 * Siteimprove Alfa plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Siteimprove Alfa
 * Description:       The Siteimprove plugin bridges the gap between Drupal and the Siteimprove Intelligence Platform via open source Alfa Engine.
 * Version:           1.0.0
 * Requires at least: 5.9 // TODO: clarify min WP version
 * Requires PHP:      8.0 // TODO: clarify min php version
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

if ( ! defined( 'WPINC' ) ) {
	die; // If this file is called directly, abort.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'STIM_ALFA_VERSION', '1.0.0' );
define( 'STIM_ALFA_PLUGIN_NAME', 'siteimprove-alfa' );
define( 'STIM_ALFA_PLUGIN_ROOT_PATH', trailingslashit( __DIR__ ) );
define( 'STIM_ALFA_PLUGIN_ROOT_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin class.
 */
class Siteimprove_Alfa {

	/**
	 * Init plugin.
	 *
	 * @return void
	 */
	public function init():void{
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * Set up plugin.
	 *
	 * @return void
	 */
	public function plugins_loaded(): void {
	}

	/**
	 * Execute plugin activation process.
	 *
	 * @return void
	 */
	public function activate(): void {
	}

	/**
	 * Execute plugin deactivation process.
	 *
	 * @return void
	 */
	public function deactivate(): void {
	}
}

$siteimprove_alfa_plugin = new Siteimprove_Alfa();
$siteimprove_alfa_plugin->init();
