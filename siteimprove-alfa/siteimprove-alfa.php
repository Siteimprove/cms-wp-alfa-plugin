<?php
/**
 * Siteimprove Alfa plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Siteimprove Alfa
 * Description:       The Siteimprove plugin bridges the gap between WordPress and the Siteimprove Intelligence Platform via open source Alfa Engine.
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

use Siteimprove\Alfa\Core\Database;
use Siteimprove\Alfa\Core\Hook_Registry;
use Siteimprove\Alfa\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Repository\Scan_Repository;

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
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		// TODO: refactor
		$cron = new Cron\Daily_Stats_Aggregation_Cron( new Scan_Repository(), new Daily_Stats_Repository() );
		add_action( 'siteimprove_alfa_daily_stats_aggregation', array( $cron, 'aggregate_daily_stats' ) );
		$cron->register_hooks();
	}

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$hook_registry = new Hook_Registry();

		if ( is_admin() ) {
			$hook_registry
				->add( new Admin\Navigation() )
				->add( new Admin\Dashboard_Page() )
				->add( new Admin\Gutenberg_Sidebar() );
		}

		$hook_registry
			->add( new Api\Get_Scan_Result_Api( new Scan_Repository() ) )
			->add( new Api\Get_Daily_Stats_Api( new Daily_Stats_Repository() ) )
			->add( new Admin\Admin_Bar( new Scan_Repository() ) );

		$hook_registry->register_hooks();
	}

	/**
	 * @return void
	 */
	public function activate(): void {
		$db = new Database();
		$db->install();
	}
}

$siteimprove_alfa_plugin = new Siteimprove_Alfa();
$siteimprove_alfa_plugin->init();
