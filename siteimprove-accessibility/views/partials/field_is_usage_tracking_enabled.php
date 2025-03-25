<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_PREVIEW_IS_USAGE_TRACKING_ENABLED ); ?>" <?php echo checked( get_option( Siteimprove_Accessibility::OPTION_PREVIEW_IS_USAGE_TRACKING_ENABLED ) ); ?>>
		<?php esc_html_e( 'Allow anonymous usage tracking.', 'siteimprove_accessibility' ); ?>
	</label>
</fieldset>