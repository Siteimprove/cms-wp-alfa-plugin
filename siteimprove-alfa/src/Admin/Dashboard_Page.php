<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\View_Trait;
use Siteimprove\Alfa\Core\Hook_Interface;

class Dashboard_Page implements Hook_Interface {

	use View_Trait;

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

		if ( 'admin.php' === $pagenow && 'siteimprove_alfa' === $_GET['page'] ?? null ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_enqueue_script(
				SITEIMPROVE_ALFA_PLUGIN_NAME,
				SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/dashboard.bundle.js',
				array( 'wp-api-fetch', 'react', 'react-dom' ),
				SITEIMPROVE_ALFA_VERSION,
				false
			);
		}
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/dashboard.php' );
	}
}
