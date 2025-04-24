<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

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
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/siteimprove-accessibility.css',
			array(),
			SITEIMPROVE_ACCESSIBILITY_VERSION,
		);
	}

	/**
	 * @return void
	 */
	public function init_menu(): void {
		$issues_page = new Issues_Page();
		$capability  = get_option( Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE );

		add_menu_page(
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			$capability,
			$issues_page::MENU_SLUG,
			array( $issues_page, 'render_page' ),
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/img/si-icon.svg',
		);

		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Accessibility issues', 'siteimprove-accessibility' ),
			__( 'Issues', 'siteimprove-accessibility' ),
			$capability,
			$issues_page::MENU_SLUG,
			array( $issues_page, 'render_page' ),
		);

		$reports_page = new Reports_Page();
		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Compliance history', 'siteimprove-accessibility' ),
			__( 'Reports', 'siteimprove-accessibility' ),
			$capability,
			$reports_page::MENU_SLUG,
			array( $reports_page, 'render_page' ),
		);

		$settings_page = new Settings();
		add_submenu_page(
			$issues_page::MENU_SLUG,
			__( 'Settings', 'siteimprove-accessibility' ),
			__( 'Settings', 'siteimprove-accessibility' ),
			'manage_options', // Only for administrators.
			$settings_page::MENU_SLUG,
			array( $settings_page, 'render_page' ),
		);
	}
}
