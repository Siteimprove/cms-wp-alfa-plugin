<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Abstract_Page;
use Siteimprove\Alfa\Core\Hook_Interface;

class Dashboard_Page extends Abstract_Page implements Hook_Interface {

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/dashboard.bundle.js',
			array( 'wp-api-fetch', 'react', 'react-dom' ),
			SITEIMPROVE_ALFA_VERSION,
			false
		);
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render('views/dashboard.php');
	}
}
