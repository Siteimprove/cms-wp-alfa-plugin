<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\Usage_Tracking_Trait;
use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Core\View_Trait;

class Reports_Page implements Hook_Interface {

	use View_Trait;
	use Usage_Tracking_Trait;

	const MENU_SLUG = 'siteimprove_accessibility_reports';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		global $pagenow;

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && static::MENU_SLUG === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_enqueue_script(
				'SiteimproveAccessibilityCmsComponents',
				SITEIMPROVE_CDN_URL . 'siteimprove-accessibility-cms-components-latest.js',
				array( 'react', 'react-dom' ),
				SITEIMPROVE_ACCESSIBILITY_VERSION,
				true
			);
			wp_enqueue_script(
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/reports.bundle.js',
				array( 'wp-api-fetch', 'react', 'react-dom', 'SiteimproveAccessibilityCmsComponents' ),
				SITEIMPROVE_ACCESSIBILITY_VERSION,
				true
			);

			$this->enqueue_usage_tracking_scripts();
		}
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/reports.php' );
	}
}
