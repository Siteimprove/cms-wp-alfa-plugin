<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="siteimprove-dashboard-header">
	<strong><?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-alfa' ); ?></strong>
</div>

<div class="siteimprove-dashboard-container wrap">
	<div class="siteimprove-dashboard-heading">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	</div>

    <form method="post" action="options.php" id="siteimprove-settings-form">
		<?php
		settings_fields('siteimprove_accessibility_settings');
		do_settings_sections('siteimprove_accessibility_settings');
		submit_button(__('Update settings', 'siteimprove-accessibility'));
		?>
    </form>
</div>