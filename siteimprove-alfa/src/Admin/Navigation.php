<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;

class Navigation implements Hook_Interface {

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_menu', array( $this, 'init_menu' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style( STIM_ALFA_PLUGIN_NAME, STIM_ALFA_PLUGIN_ROOT_URL . 'admin/css/stim-alfa-admin.css', array(), STIM_ALFA_VERSION, 'all' );
	}

	/**
	 * @return void
	 */
	public function init_menu(): void {
		add_menu_page(
			__( 'Siteimprove Alfa', 'siteimprove-alfa' ),
			__( 'Siteimprove Alfa', 'siteimprove-alfa' ),
			'manage_options',
			'siteimprove_alfa',
			array( $this, 'render_dashboard' ),
			plugins_url( 'siteimprove-alfa/admin/img/si-icon.svg' ),
		);
	}

	/**
	 * @return void
	 */
	public function render_dashboard(): void {
		$page = new Dashboard_Page();
		$page->render_page();
	}
}
