<?php
/**
 * Siteimprove Accessibility plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Siteimprove Accessibility
 * Description:       The Siteimprove Accessibility plugin bridges the gap between WordPress and the Siteimprove Intelligence Platform via open source Alfa Engine.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.0
 * Author:            Siteimprove
 * Author URI:        https://siteimprove.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       siteimprove-accessibility
 */

namespace Siteimprove\Accessibility;

use Siteimprove\Accessibility\Admin\Reports_Page;
use Siteimprove\Accessibility\Admin\Scan_Panel;
use Siteimprove\Accessibility\Admin\Issues_Page;
use Siteimprove\Accessibility\Admin\Gutenberg_Sidebar;
use Siteimprove\Accessibility\Admin\Navigation;
use Siteimprove\Accessibility\Admin\Settings;
use Siteimprove\Accessibility\Api\Get_Daily_Stats_Api;
use Siteimprove\Accessibility\Api\Get_Issues_Api;
use Siteimprove\Accessibility\Api\Get_Pages_With_Issues_Api;
use Siteimprove\Accessibility\Api\Get_Scan_Result_Api;
use Siteimprove\Accessibility\Api\Post_Save_Scan_Api;
use Siteimprove\Accessibility\Core\Database;
use Siteimprove\Accessibility\Core\Hook_Registry;
use Siteimprove\Accessibility\Core\Service_Container;
use Siteimprove\Accessibility\Cron\Daily_Stats_Aggregation_Cron;
use Siteimprove\Accessibility\Service\Daily_Stats_Processor;
use Siteimprove\Accessibility\Service\Repository\Daily_Stats_Repository;
use Siteimprove\Accessibility\Service\Repository\Issue_Repository;
use Siteimprove\Accessibility\Service\Repository\Scan_Repository;

if ( ! defined( 'WPINC' ) ) {
	die; // If this file is called directly, abort.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'SITEIMPROVE_ACCESSIBILITY_VERSION', '1.0.0' );
define( 'SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME', 'siteimprove-accessibility' );
define( 'SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_PATH', trailingslashit( __DIR__ ) );
define( 'SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin class.
 */
class Siteimprove_Accessibility {

	const OPTION_IS_PAGE_CHECK_USED        = 'siteimprove_accessibility_is_page_check_used';
	const OPTION_IS_WIDGET_ENABLED         = 'siteimprove_accessibility_is_widget_enabled';
	const OPTION_WIDGET_POSITION           = 'siteimprove_accessibility_widget_position';
	const OPTION_ALLOWED_USER_ROLE         = 'siteimprove_accessibility_allowed_user_role';
	const OPTION_PREVIEW_AUTO_CHECK        = 'siteimprove_accessibility_preview_auto_scan';
	const OPTION_IS_USAGE_TRACKING_ENABLED = 'siteimprove_accessibility_is_usage_tracking_enabled';

	private Service_Container $container;

	public function __construct() {
		$this->container = ( new Service_Container() )
			->register(
				'scan_repository',
				function () {
					return new Scan_Repository();
				}
			)
			->register(
				'issue_repository',
				function () {
					return new Issue_Repository();
				}
			)
			->register(
				'daily_stats_repository',
				function () {
					return new Daily_Stats_Repository();
				}
			)
			->register(
				'daily_stats_processor',
				function () {
					return new Daily_Stats_Processor(
						$this->container->get( 'scan_repository' ),
						$this->container->get( 'issue_repository' )
					);
				}
			);
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

		$settings = new Settings();
		$settings->init_options();
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		$hook_registry = new Hook_Registry();
		$hook_registry
			->add( new Settings() )
			->add( new Navigation() )
			->add( new Issues_Page() )
			->add( new Reports_Page() )
			->add( new Gutenberg_Sidebar() )
			->add( new Scan_Panel() )
			->add(
				new Post_Save_Scan_Api(
					$this->container->get( 'scan_repository' ),
					$this->container->get( 'issue_repository' )
				)
			)
			->add( new Get_Scan_Result_Api( $this->container->get( 'scan_repository' ) ) )
			->add( new Get_Issues_Api( $this->container->get( 'issue_repository' ) ) )
			->add( new Get_Pages_With_Issues_Api( $this->container->get( 'scan_repository' ) ) )
			->add(
				new Get_Daily_Stats_Api(
					$this->container->get( 'daily_stats_repository' ),
					$this->container->get( 'daily_stats_processor' )
				)
			);

		$hook_registry->register_hooks();
	}

	/**
	 * @return void
	 */
	public function schedule_cron(): void {
		$daily_stats_aggregation_cron = new Daily_Stats_Aggregation_Cron(
			$this->container->get( 'daily_stats_repository' ),
			$this->container->get( 'daily_stats_processor' ),
			$this->container->get( 'scan_repository' ),
		);

		$daily_stats_aggregation_cron->schedule();
	}
}

$siteimprove_accessibility_plugin = new Siteimprove_Accessibility();
$siteimprove_accessibility_plugin->init();
