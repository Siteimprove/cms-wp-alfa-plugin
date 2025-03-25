<?php

use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array $allowed_user_role_options
 * @var string $selected
 */

?>

<fieldset>
	<select name="<?php echo esc_attr( Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE ); ?>">
		<?php foreach ( $allowed_user_role_options as $key => $value ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php echo selected( $selected === $key ); ?>><?php echo esc_attr( $value ); ?></option>
		<?php endforeach; ?>
	</select>
	<p><?php esc_html_e( 'Choose the lowest-level user role that needs access to Siteimprove Accessibility. Roles with higher permissions will also have access.', 'siteimprove-accessibility' ); ?></p>
</fieldset>