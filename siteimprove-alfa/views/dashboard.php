<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="siteimprove-dashboard-header">
	<strong><?php esc_html_e( 'Siteimprove | Alfa', 'siteimprove-alfa' ); ?></strong>
</div>

<!--ul id="siteimprove-dashboard-navigation">
	<li><a href="?1" class="active">Issues</a></li>
	<li><a href="?2">Reports</a></li>
</ul-->

<div id="siteimprove-dashboard-container">
	<div class="wrap">
		<div class="siteimprove-dashboard-heading">
			<h3><?php esc_html_e( 'Progress over time', 'siteimprove-alfa' ); ?></h3>
		</div>
		<div id="siteimprove-daily-stats">
			<div style="text-align: center; line-height:100px;"><?php esc_html_e( 'Loading analytics ...', 'siteimprove-alfa' ); ?></div>
		</div>
	</div>
</div>