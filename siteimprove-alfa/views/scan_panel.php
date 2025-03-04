<button id="siteimprove-scan-panel-button" class="siteimprove-component"></button>
<div id="siteimprove-scan-panel" class="siteimprove-component" style="display: none">
	<div class="scan-panel-header">
		<a href="<?php echo admin_url( 'admin.php?page=siteimprove_alfa' ); ?>">
			<?php esc_html_e( 'Siteimprove | Alfa', 'siteimprove-alfa' ); ?>
		</a>
		<button id="siteimprove-scan-hide"></button>
	</div>
	<div class="scan-panel-body">
		<div class="scan-panel-control">
			<button class="siteimprove-scan-button"><span><?php esc_html_e( 'Check page', 'siteimprove-alfa' ); ?></span></button>
		</div>
		<div id="siteimprove-scan-results"></div>
	</div>
</div>