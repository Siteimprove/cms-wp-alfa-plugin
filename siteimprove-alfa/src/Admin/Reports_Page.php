<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\View_Trait;
use Siteimprove\Alfa\Core\Hook_Interface;

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
				SITEIMPROVE_ALFA_PLUGIN_NAME,
				SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/reports.bundle.js',
				array( 'wp-api-fetch', 'react', 'react-dom' ),
				SITEIMPROVE_ALFA_VERSION,
				true
			);
		}
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/reports.php' );
	}
}
