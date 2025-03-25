<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var bool $is_page_check_used
 */
?>

<div id="siteimprove-dashboard-header">
	<strong><?php esc_html_e( 'Siteimprove Accessibility', 'siteimprove-accessibility' ); ?></strong>
</div>

<div class="siteimprove-dashboard-container wrap">
	<div class="siteimprove-dashboard-heading">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	</div>
	<div id="siteimprove-scan-report" class="siteimprove-component-container">
		<?php if ( $is_page_check_used ) : ?>
			<div class="siteimprove-component-placeholder"><?php esc_html_e( 'Loading issues ...', 'siteimprove-accessibility' ); ?></div>
			<div class="siteimprove-empty-issues-container" style="display: none;">
				<img src="<?php echo esc_url( SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/img/a11y-icon.svg' ); ?>" alt="<?php esc_html_e( 'All clear!', 'siteimprove-accessibility' ); ?>" />
				<h2><?php esc_html_e( 'All clear!', 'siteimprove-accessibility' ); ?></h2>
				<p>
					No accessibility issues were found on your content. You're looking good!<br />
					New issues will populate here as you create and scan new content.
				</p>
			</div>
		<?php else : ?>
			<div class="siteimprove-empty-issues-container">
				<h2><?php esc_html_e( 'Find and fix accessibility issues', 'siteimprove-accessibility' ); ?></h2>
				<p><?php esc_html_e( 'This table will populate with accessibility issues as you scan your content.', 'siteimprove-accessibility' ); ?></p>
				<p><img src="<?php echo esc_url( SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_URL . 'assets/img/issues-table-720.jpg' ); ?>" alt="<?php esc_html_e( 'Issues table', 'siteimprove-accessibility' ); ?>" /></p>
				<a href="<?php echo esc_url( add_query_arg( 'siteimprove-auto-check', 'true', home_url() ) ); ?>" class="button button-primary"><?php esc_html_e( 'Check Homepage', 'siteimprove-accessibility' ); ?></a>
				<p>
					<?php
					printf(
						wp_kses(
							// translators: %1$s link to list of posts, %2$s link to list of pages
							__( 'Or, open any <a href="%1$s">post</a> or <a href="%2$s">page</a> preview to begin an accessibility check.', 'siteimprove-accessibility' ),
							array( 'a' => array( 'href' => array() ) )
						),
						esc_url( admin_url( 'edit.php' ) ),
						esc_url( admin_url( 'edit.php?post_type=page' ) )
					);
					?>
				</p>
			</div>
		<?php endif; ?>
	</div>
</div>
