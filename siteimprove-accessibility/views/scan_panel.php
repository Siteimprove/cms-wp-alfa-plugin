<?php

use Siteimprove\Accessibility\Admin\Issues_Page;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<button id="siteimprove-scan-panel-button" aria-label="scan" class="siteimprove-component
	<?php echo esc_attr( get_option( Siteimprove_Accessibility::OPTION_WIDGET_POSITION ) ); ?>
	<?php echo get_option( Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED ) ? 'visible' : ''; ?>
"></button>
<div id="siteimprove-scan-panel" class="siteimprove-component <?php echo esc_attr( get_option( Siteimprove_Accessibility::OPTION_WIDGET_POSITION ) ); ?>" style="display: none">
	<div class="scan-panel-header">
		<a href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', Issues_Page::MENU_SLUG ) ) ); ?>">
			<?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-accessibility' ); ?>
		</a>
		<button id="siteimprove-scan-hide" aria-label="hide"></button>
	</div>
	<div class="scan-panel-body">
		<div class="scan-panel-control">
			<button class="siteimprove-scan-button" data-observe-key="a11y-WordPress-ScanButton"><span><?php esc_html_e( 'Check page', 'siteimprove-accessibility' ); ?></span></button>
		</div>
		<div id="siteimprove-scan-results"></div>
	</div>
</div>