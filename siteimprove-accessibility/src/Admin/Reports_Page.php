<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\View_Trait;
use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

class Reports_Page implements Hook_Interface {

	use View_Trait;

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
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/reports.bundle.js',
				array( 'wp-api-fetch', 'react', 'react-dom' ),
				SITEIMPROVE_ACCESSIBILITY_VERSION,
				true
			);

			if ( get_option( Siteimprove_Accessibility::OPTION_PREVIEW_IS_USAGE_TRACKING_ENABLED, 0 ) ) {
				wp_enqueue_script(
					'siteimprove-accessibility-pendo',
					SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/pendo.js',
					array(),
					SITEIMPROVE_ACCESSIBILITY_VERSION,
					false
				);
			}
		}
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/reports.php' );
	}
}
