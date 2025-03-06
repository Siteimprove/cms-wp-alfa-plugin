<button id="siteimprove-scan-panel-button" class="siteimprove-component"></button>
<div id="siteimprove-scan-panel" class="siteimprove-component" style="display: none">
	<div class="scan-panel-header">
		<a href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', \Siteimprove\Alfa\Admin\Issues_Page::MENU_SLUG ) ) ); ?>">
			<?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-alfa' ); ?>
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