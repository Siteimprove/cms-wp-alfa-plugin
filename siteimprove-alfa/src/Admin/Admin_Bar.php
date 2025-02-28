<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;

class Admin_Bar implements Hook_Interface {

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 999 );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		wp_enqueue_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/admin-bar.bundle.js',
			array( 'wp-i18n', 'wp-api-fetch', 'jquery' ),
			SITEIMPROVE_ALFA_VERSION,
			false
		);

		wp_enqueue_style(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/admin-bar.css',
			array(),
			SITEIMPROVE_ALFA_VERSION,
		);

		$post_id = is_singular() ? get_the_ID() : null;
		wp_localize_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			'siteimproveAlfaSaveScanData',
			array(
				'post_id'   => $post_id,
				'view_link' => $post_id ? get_edit_post_link( $post_id ) : admin_url( 'admin.php?page=siteimprove_alfa' ),
			)
		);
	}

	/**
	 * @param $wp_admin_bar
	 *
	 * @return void
	 */
	public function add_admin_bar_node( $wp_admin_bar ): void {
		// Only relevant if admin bar is showing on the public site.
		if ( ! is_admin_bar_showing() || is_admin() ) {
			return;
		}

		$wp_admin_bar->add_node(
			array(
				'id'    => 'stim-alfa-check-accessibility',
				'title' => sprintf( '<span class="ab-icon"></span><span class="label">%s</span>', __( 'Check Accessibility', 'siteimprove-alfa' ) ),
				'href'  => '#',
				'meta'  => array(
					'title' => __( 'Start Siteimprove Alfa accessibility scan', 'siteimprove-alfa' ),
				),
			)
		);
	}
}
