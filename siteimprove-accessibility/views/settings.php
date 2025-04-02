<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="siteimprove-dashboard-header">
	<strong><?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-accessibility' ); ?></strong>
</div>

<div class="siteimprove-dashboard-container wrap">
	<div class="siteimprove-dashboard-heading">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<?php settings_errors(); ?>

		<div class="siteimprove-extension-notification">
            <div class="text-content">
                <h2><?php esc_html_e( 'Instant Accessibility Checks, In Your Browser.', 'siteimprove-accessibility' ); ?></h2>
                <p><?php esc_html_e( 'Securely test webpages, multi-step forms and dynamic content for accessibility with our free extension. Highlight issues and understand their impact on users, all within your browser.', 'siteimprove-accessibility' ); ?></p>
            </div>
			<a href="https://www.siteimprove.com/integrations/browser-extensions/" class="button button-primary" target="_blank">Get the extension ↗</a>
		</div>
	</div>

	<form method="post" action="options.php" id="siteimprove-settings-form">
		<?php
		settings_fields( 'siteimprove_accessibility_settings' );
		do_settings_sections( 'siteimprove_accessibility_settings' );
		?>
        <p class="submit">
	        <?php submit_button( __( 'Update settings', 'siteimprove-accessibility' ), 'primary', 'submit', false ); ?>
            <span style="display: block; margin-top: 10px;"><a href="https://frontier.siteimprove.com/page/terms-and-conditions" target="_blank"><?php esc_html_e( 'Terms and Conditions', 'siteimprove-accessibility' ); ?></a></span>
        </p>
	</form>
</div>