<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_IS_USAGE_TRACKING_ENABLED ); ?>" data-observe-key="a11y-WordPress-UsageTrackingCheckbox" <?php echo checked( get_option( Siteimprove_Accessibility::OPTION_IS_USAGE_TRACKING_ENABLED, 1 ) ); ?>>
		<?php esc_html_e( 'Enable anonymous usage data collection.', 'siteimprove-accessibility' ); ?>
	</label>
</fieldset>