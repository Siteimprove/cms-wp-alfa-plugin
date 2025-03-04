<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\View_Trait;
use Siteimprove\Alfa\Core\Hook_Interface;

class Dashboard implements Hook_Interface {

	use View_Trait;

	const SLUG_ISSUES = 'siteimprove_accessibility_issues';
	const SLUG_REPORTS = 'siteimprove_accessibility_reports';
	const SLUG_SETTINGS = 'siteimprove_accessibility_settings';

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

		wp_enqueue_style(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/siteimprove-alfa.css',
			array(),
			SITEIMPROVE_ALFA_VERSION,
		);

		$dashboard_slugs = array(
			static::SLUG_ISSUES,
			static::SLUG_REPORTS,
		);

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array( $_GET['page'], $dashboard_slugs ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$script = null;
			switch ( $_GET['page'] ) {
				case static::SLUG_ISSUES:
					$script = 'issues';
					break;
				case static::SLUG_REPORTS:
					$script = 'reports';
					break;
			}

			if ( $script ) {
				wp_enqueue_script(
					SITEIMPROVE_ALFA_PLUGIN_NAME,
					SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . sprintf('assets/%s.bundle.js', $script),
					array( 'wp-api-fetch', 'react', 'react-dom' ),
					SITEIMPROVE_ALFA_VERSION,
					true
				);
			}
		}
	}

	/**
	 * @return void
	 */
	public function render_issues(): void {
		$this->render( 'views/issues.php' );
	}

	/**
	 * @return void
	 */
	public function render_reports(): void {
		$this->render( 'views/reports.php' );
	}

	/**
	 * @return void
	 */
	public function render_settings(): void {
		$this->render( 'views/settings.php' );
	}
}
