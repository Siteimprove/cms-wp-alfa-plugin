<?php declare( strict_types=1 );

namespace Siteimprove\Accessibility\Core;

use Siteimprove\Accessibility\Siteimprove_Accessibility;

trait Usage_Tracking_Trait {

	/**
	 * @param array $init_params
	 *
	 * @return void
	 */
	public function enqueue_usage_tracking_scripts( array $init_params = array() ): void {
		if ( get_option( Siteimprove_Accessibility::OPTION_PREVIEW_IS_USAGE_TRACKING_ENABLED, 1 ) ) {
			wp_enqueue_script(
				'siteimprove-accessibility-usage-tracking',
				SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/usage-tracking.js',
				array( 'jquery' ),
				SITEIMPROVE_ACCESSIBILITY_VERSION,
				false
			);

			wp_add_inline_script(
				'siteimprove-accessibility-usage-tracking',
				sprintf(
					'const siteimproveUsageTrackingInitParams = %s;',
					wp_json_encode( $init_params )
				),
				'before'
			);
		}
	}
}
