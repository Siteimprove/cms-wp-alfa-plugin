<?php

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Core\Usage_Tracking_Trait;

class Gutenberg_Sidebar implements Hook_Interface {

	use Usage_Tracking_Trait;

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// Only relevant for post and page editor.
		$screen = get_current_screen();
		if ( 'post' !== $screen->base || ( 'post' !== $screen->post_type && 'page' !== $screen->post_type ) ) {
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
			'siteimprove-accessibility-gutenberg-sidebar',
			SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/gutenberg.bundle.js',
			array( 'wp-i18n', 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch', 'SiteimproveAccessibilityCmsComponents' ),
			SITEIMPROVE_ACCESSIBILITY_VERSION,
			false
		);

		$this->enqueue_usage_tracking_scripts();
	}
}
