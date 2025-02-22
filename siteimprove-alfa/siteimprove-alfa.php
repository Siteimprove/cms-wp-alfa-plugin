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

use Siteimprove\Alfa\Admin\Admin_Bar;
use Siteimprove\Alfa\Admin\Dashboard_Page;
use Siteimprove\Alfa\Admin\Gutenberg_Sidebar;
use Siteimprove\Alfa\Admin\Navigation;
use Siteimprove\Alfa\Api\Get_Daily_Stats_Api;
use Siteimprove\Alfa\Api\Get_Scan_Result_Api;
use Siteimprove\Alfa\Core\Database;
use Siteimprove\Alfa\Core\Hook_Registry;
use Siteimprove\Alfa\Core\Service_Container;
use Siteimprove\Alfa\Cron\Daily_Stats_Aggregation_Cron;
use Siteimprove\Alfa\Service\Daily_Stats_Processor;
use Siteimprove\Alfa\Service\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;

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

	private Service_Container $container;

	public function __construct() {
		$this->container = ( new Service_Container() )
			->register( 'scan_repository', function() {
				return new Scan_Repository();
			})
			->register( 'daily_stats_repository', function() {
				return new Daily_Stats_Repository();
			})
			->register( 'daily_stats_processor', function() {
				return new Daily_Stats_Processor();
			});
	}

	/**
	 * @return void
	 */
	public function init(): void {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'register_hooks' ) );
		$this->schedule_cron();
	}

	/**
	 * @return void
	 */
	public function activate(): void {
		$db = new Database();
		$db->install();
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$hook_registry = new Hook_Registry();

		if ( is_admin() ) {
			$hook_registry
				->add( new Navigation() )
				->add( new Dashboard_Page() )
				->add( new Gutenberg_Sidebar() );
		}

		$hook_registry
			->add( new Get_Scan_Result_Api( $this->container->get('scan_repository') ) )
			->add( new Get_Daily_Stats_Api(
				$this->container->get('scan_repository'),
				$this->container->get('daily_stats_repository'),
				$this->container->get('daily_stats_processor')
			))
			->add( new Admin_Bar( $this->container->get('scan_repository') ) );

		$hook_registry->register_hooks();
	}

	/**
	 * @return void
	 */
	public function schedule_cron(): void {
		$daily_stats_aggregation_cron = new Daily_Stats_Aggregation_Cron(
			$this->container->get( 'scan_repository' ),
			$this->container->get( 'daily_stats_repository' ),
			$this->container->get( 'daily_stats_processor' )
		);

		$daily_stats_aggregation_cron->schedule();
	}
}

$siteimprove_alfa_plugin = new Siteimprove_Alfa();
$siteimprove_alfa_plugin->init();
