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
	<div id="siteimprove-scan-report" class="siteimprove-component-container">
		<div class="siteimprove-component-placeholder"><?php esc_html_e( 'Loading issues ...', 'siteimprove-alfa' ); ?></div>
	</div>
</div>
