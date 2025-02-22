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
		// Only relevant if admin bar is showing, and the current page represents a single post.
		if ( ! is_admin_bar_showing() || ! is_singular() ) {
			return;
		}

		wp_enqueue_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/admin-bar.bundle.js',
			array( 'wp-i18n', 'wp-api-fetch', 'jquery' ),
			SITEIMPROVE_ALFA_VERSION,
			false
		);

		$post_id = get_the_ID();
		wp_localize_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			'siteimproveAlfaSaveScanData',
			array(
				'post_id'   => $post_id,
				'view_link' => get_edit_post_link( $post_id ),
			)
		);
	}

	/**
	 * @param $wp_admin_bar
	 *
	 * @return void
	 */
	public function add_admin_bar_node( $wp_admin_bar ): void {
		// Only relevant if admin bar is showing on the public site, and the current page represents a single post.
		if ( ! is_admin_bar_showing() || is_admin() || ! is_singular() ) {
			return;
		}

		// TODO: make sure it's visible in mobile view
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
