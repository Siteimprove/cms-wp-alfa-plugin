<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;

class Navigation implements Hook_Interface {

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'init_menu' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_style(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/siteimprove-alfa.css',
			array(),
			SITEIMPROVE_ALFA_VERSION,
		);
	}

	/**
	 * @return void
	 */
	public function init_menu(): void {
		$issues_page = new Issues_Page();

		add_menu_page(
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			'manage_options',
			$issues_page::MENU_SLUG,
			array( $issues_page, 'render_page' ),
			plugins_url( 'siteimprove-alfa/assets/img/si-icon.svg' ),
		);

		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Accessibility issues', 'siteimprove-accessibility' ),
			__( 'Issues', 'siteimprove-accessibility' ),
			'manage_options',
			$issues_page::MENU_SLUG,
			array( $issues_page, 'render_page' ),
		);

		$reports_page = new Reports_Page();
		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Progress over time', 'siteimprove-accessibility' ),
			__( 'Reports', 'siteimprove-accessibility' ),
			'manage_options',
			$reports_page::MENU_SLUG,
			array( $reports_page, 'render_page' ),
		);

		$settings_page = new Settings_Page();
		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Settings', 'siteimprove-accessibility' ),
			__( 'Settings', 'siteimprove-accessibility' ),
			'manage_options',
			$settings_page::MENU_SLUG,
			array( $settings_page, 'render_page' ),
		);
	}
}
