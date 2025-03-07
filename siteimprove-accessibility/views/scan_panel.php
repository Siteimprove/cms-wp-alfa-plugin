<?php

use Siteimprove\Accessibility\Admin\Issues_Page;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<button id="siteimprove-scan-panel-button" class="siteimprove-component <?php echo get_option(Siteimprove_Accessibility::OPTION_WIDGET_POSITION); ?> <?php if ( get_option(Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED) ) { echo 'visible'; } ?>"></button>
<div id="siteimprove-scan-panel" class="siteimprove-component <?php echo get_option(Siteimprove_Accessibility::OPTION_WIDGET_POSITION); ?>" style="display: none">
	<div class="scan-panel-header">
		<a href="<?php echo esc_url( admin_url( sprintf( 'admin.php?page=%s', Issues_Page::MENU_SLUG ) ) ); ?>">
			<?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-accessibility' ); ?>
		</a>
		<button id="siteimprove-scan-hide"></button>
	</div>
	<div class="scan-panel-body">
		<div class="scan-panel-control">
			<button class="siteimprove-scan-button"><span><?php esc_html_e( 'Check page', 'siteimprove-accessibility' ); ?></span></button>
		</div>
		<div id="siteimprove-scan-results"></div>
	</div>
</div>