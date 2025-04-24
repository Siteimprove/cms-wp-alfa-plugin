<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Core\Usage_Tracking_Trait;
use Siteimprove\Accessibility\Core\View_Trait;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

class Scan_Panel implements Hook_Interface {

	use View_Trait;
	use Usage_Tracking_Trait;

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'add_scan_panel' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		wp_enqueue_script(
			'SiteimproveAccessibilityCmsComponents',
			SITEIMPROVE_CDN_URL . 'siteimprove-accessibility-cms-components-latest.js',
			array( 'react', 'react-dom' ),
			SITEIMPROVE_ACCESSIBILITY_VERSION,
			false
		);
		wp_enqueue_script(
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/scan-panel.bundle.js',
			array( 'wp-i18n', 'wp-api-fetch', 'jquery', 'react', 'react-dom' ),
			SITEIMPROVE_ACCESSIBILITY_VERSION,
			false
		);

		wp_enqueue_style(
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/siteimprove-accessibility.css',
			array(),
			SITEIMPROVE_ACCESSIBILITY_VERSION,
		);

		$post_id = is_singular() ? get_the_ID() : null;
		wp_localize_script(
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_NAME,
			'siteimproveAccessibilityScan',
			array(
				'post_id'    => $post_id,
				'auto_check' => $this->isAutoCheckEnabled(),
			)
		);

		$this->enqueue_usage_tracking_scripts();
	}

	/**
	 * @return void
	 */
	public function add_scan_panel(): void {
		$this->render( 'views/scan_panel.php' );
	}

	/**
	 * @return bool
	 */
	public function isAutoCheckEnabled(): bool {
		$force_auto_check = filter_var( wp_unslash( $_GET['siteimprove-auto-check'] ?? false ), FILTER_VALIDATE_BOOLEAN ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return ( $force_auto_check || ( is_preview() && get_option( Siteimprove_Accessibility::OPTION_PREVIEW_AUTO_CHECK, 0 ) ) );
	}
}
