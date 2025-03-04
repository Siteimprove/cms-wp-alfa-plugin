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
		<h3><?php echo esc_html(get_admin_page_title()); ?></h3>
	</div>
	<div id="siteimprove-daily-stats" class="siteimprove-component-container">
		<div class="siteimprove-component-placeholder"><?php esc_html_e( 'Loading analytics ...', 'siteimprove-alfa' ); ?></div>
	</div>
</div>