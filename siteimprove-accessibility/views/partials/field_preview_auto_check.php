<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_PREVIEW_AUTO_CHECK ); ?>" <?php echo checked( get_option( Siteimprove_Accessibility::OPTION_PREVIEW_AUTO_CHECK ) ); ?>>
		<?php esc_html_e( 'Enable accessibility auto-check on page previews.', 'siteimprove_accessibility' ); ?>
	</label>
</fieldset>