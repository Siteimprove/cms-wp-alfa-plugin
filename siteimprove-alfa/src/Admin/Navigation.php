<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;

class Navigation implements Hook_Interface {

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_menu', array( $this, 'init_menu' ) );
	}

	/**
	 * @return void
	 */
	public function init_menu(): void {
		$dashboard = new Dashboard();

		add_menu_page(
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			__( 'Siteimprove Accessibility', 'siteimprove-accessibility' ),
			'manage_options',
			$dashboard::SLUG_ISSUES,
			array( $dashboard, 'render_issues' ),
			plugins_url( 'siteimprove-alfa/assets/img/si-icon.svg' ),
		);

		add_submenu_page(
			$dashboard::SLUG_ISSUES,
			__( 'Accessibility issues', 'siteimprove-accessibility' ),
			__( 'Issues', 'siteimprove-accessibility' ),
			'manage_options',
			$dashboard::SLUG_ISSUES,
			array( $dashboard, 'render_issues' ),
		);

		add_submenu_page(
			$dashboard::SLUG_ISSUES,
			__( 'Progress over time', 'siteimprove-accessibility' ),
			__( 'Reports', 'siteimprove-accessibility' ),
			'manage_options',
			$dashboard::SLUG_REPORTS,
			array( $dashboard, 'render_reports' ),
		);

		add_submenu_page(
			$dashboard::SLUG_ISSUES,
			__( 'Settings', 'siteimprove-accessibility' ),
			__( 'Settings', 'siteimprove-accessibility' ),
			'manage_options',
			$dashboard::SLUG_SETTINGS,
			array( $dashboard, 'render_settings' ),
		);
	}
}
