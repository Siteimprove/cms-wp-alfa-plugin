<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Siteimprove Alfa
 * Description:       Lorem ipsum dolor sit amet
 * Version:           1.0.0
 * Author:            Siteimprove
 * Author URI:        https://siteimprove.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       siteimprove-alfa
 * Domain Path:       /languages
 */

namespace Siteimprove\Alfa;

if (!defined( 'WPINC')) {
	die; // If this file is called directly, abort.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'STIM_ALFA_VERSION', '1.0.0' );
define( 'STIM_ALFA_PLUGIN_NAME', 'siteimprove-alfa' );
define( 'STIM_ALFA_PLUGIN_ROOT_PATH', trailingslashit(__DIR__) );
define( 'STIM_ALFA_PLUGIN_ROOT_URL', trailingslashit(plugin_dir_url(__FILE__)) );

class Siteimprove_Alfa {

	public function init(): void {
		register_activation_hook( __FILE__, array($this, 'activate') );
		register_deactivation_hook( __FILE__, array($this, 'deactivate') );

		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
	}

	public function plugins_loaded(): void {

	}

	public function activate(): void {

	}

	public function deactivate(): void {

	}
}

$plugin = new Siteimprove_Alfa();
$plugin->init();
