<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\View_Trait;
use Siteimprove\Accessibility\Core\Hook_Interface;

class Issues_Page implements Hook_Interface {

	use View_Trait;

	const MENU_SLUG = 'siteimprove_accessibility_issues';

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
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/issues.bundle.js',
				array( 'wp-api-fetch', 'react', 'react-dom', 'jquery' ),
				SITEIMPROVE_ACCESSIBILITY_VERSION,
				true
			);
		}
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/issues.php' );
	}
}
