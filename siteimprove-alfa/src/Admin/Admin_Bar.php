<?php

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Repository\Scan_Repository;

class Admin_Bar implements Hook_Interface {

	private const NONCE_NAME_SAVE = 'saveScanResultNonce';
	private const NONCE_KEY       = 'security';

	private Scan_Repository $scan_repository;

	public function __construct( Scan_Repository $scan_repository ) {
		$this->scan_repository = $scan_repository;
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 999 );
		add_action( 'wp_ajax_save_scan_result', array( $this, 'save_scan_result' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// Only relevant if admin bar is showing, and the current page represents a single post.
		if ( ! is_admin_bar_showing() || ! is_singular() ) {
			return;
		}

		wp_enqueue_script( SITEIMPROVE_ALFA_PLUGIN_NAME, SITEIMPROVE_ALFA_PLUGIN_ROOT_URL . 'assets/admin-bar.bundle.js', array( 'wp-i18n', 'jquery' ), SITEIMPROVE_ALFA_VERSION, false );

		$post_id = get_the_ID();

		wp_localize_script(
			SITEIMPROVE_ALFA_PLUGIN_NAME,
			'siteimproveAlfaSaveScanResultAjax',
			array(
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'post_id'         => $post_id,
				'view_link'       => get_edit_post_link( $post_id ),
				static::NONCE_KEY => wp_create_nonce( static::NONCE_NAME_SAVE ),
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

	/**
	 * @return void
	 */
	public function save_scan_result(): void {
		check_ajax_referer( static::NONCE_NAME_SAVE, static::NONCE_KEY );

		$json = wp_unslash( $_POST['data'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data = json_decode( $json, true );

		if ( ! $data ) {
			wp_send_json_error( sprintf( 'Invalid JSON data: %s', json_last_error_msg() ) );
		}

		$result = $this->scan_repository->create_or_update_scan( (array) $data, (int) $_POST['post_id'] ?? null );

		if ( $result ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json_error( 'Internal database error.' );
		}
	}
}
