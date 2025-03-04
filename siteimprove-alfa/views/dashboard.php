<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="siteimprove-dashboard-header">
	<strong><?php esc_html_e( 'Siteimprove | Alfa', 'siteimprove-alfa' ); ?></strong>
</div>

<div class="siteimprove-dashboard-container wrap">
	<div class="siteimprove-dashboard-heading">
		<h3><?php esc_html_e( 'Accessibility issues', 'siteimprove-alfa' ); ?></h3>
	</div>
	<div id="siteimprove-scan-report" class="siteimprove-component-container">
		<div class="siteimprove-component-placeholder"><?php esc_html_e( 'Loading issues ...', 'siteimprove-alfa' ); ?></div>
	</div>
</div>

<div class="siteimprove-dashboard-container wrap">
	<div class="siteimprove-dashboard-heading">
		<h3><?php esc_html_e( 'Progress over time', 'siteimprove-alfa' ); ?></h3>
	</div>
	<div id="siteimprove-daily-stats" class="siteimprove-component-container">
		<div class="siteimprove-component-placeholder"><?php esc_html_e( 'Loading analytics ...', 'siteimprove-alfa' ); ?></div>
	</div>
</div>