<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED ); ?>" <?php echo checked( get_option( Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED ) ); ?>>
		<?php esc_html_e( 'Display widget when previewing pages.', 'siteimprove-accessibility' ); ?>
	</label>
</fieldset>