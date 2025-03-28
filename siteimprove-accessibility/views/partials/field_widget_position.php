<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array $widget_position_options
 * @var string $selected
 */

?>

<fieldset>
	<select name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_WIDGET_POSITION ); ?>">
		<?php foreach ( $widget_position_options as $key => $value ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php echo selected( $selected === $key ); ?>><?php echo esc_attr( $value ); ?></option>
		<?php endforeach; ?>
	</select>
	<p><?php esc_html_e( 'Choose where the accessibility widget will appear when viewing content.', 'siteimprove-accessibility' ); ?></p>
</fieldset>