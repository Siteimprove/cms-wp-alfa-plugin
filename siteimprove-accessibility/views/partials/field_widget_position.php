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
	<select name="<?php echo Siteimprove_Accessibility::OPTION_WIDGET_POSITION; ?>">
        <?php foreach ( $widget_position_options as $key => $value ): ?>
            <option value="<?php echo $key;?>" <?php echo selected( $selected === $key ); ?>><?php echo $value; ?></option>
        <?php endforeach; ?>
	</select>
	<p><?php esc_html_e( 'Choose widget position on page when previewing page.', 'siteimprove_accessibility' ); ?></p>
</fieldset>