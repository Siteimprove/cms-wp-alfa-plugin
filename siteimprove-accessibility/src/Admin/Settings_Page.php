<?php declare( strict_types=1 );

namespace Siteimprove\Accessibility\Admin;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Core\View_Trait;
use Siteimprove\Accessibility\Siteimprove_Accessibility;

class Settings_Page implements Hook_Interface {

	use View_Trait;

	const MENU_SLUG = 'siteimprove_accessibility_settings';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_filter( 'plugin_action_links_siteimprove-accessibility/siteimprove-accessibility.php', array( $this, 'action_links' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function action_links( array $links ): array {
		$settings_link = sprintf(
			'<a href="admin.php?page=%s">%s</a>',
			self::MENU_SLUG,
			__( 'Settings', 'siteimprove-accessibility' )
		);

		array_unshift($links, $settings_link);

		return $links;
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		$this->render( 'views/settings.php' );
	}

	/**
	 * @return void
	 */
	public function register_settings(): void {
		// Register settings fields.

		add_settings_section(
			'siteimprove_accessibility_manage_features_section',
			__( 'Manage features', 'siteimprove-accessibility' ),
			'',
			'siteimprove_accessibility_settings'
		);

		add_settings_field(
			Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED,
			__( 'Enable widget', 'siteimprove-accessibility' ),
			array( $this, 'render_field_is_widget_enabled' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			Siteimprove_Accessibility::OPTION_WIDGET_POSITION,
			'Widget position',
			array( $this, 'render_field_widget_position' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE,
			'Minimum user role',
			array( $this, 'render_field_allowed_user_role' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			'siteimprove_accessibility_customer_support',
			'Customer Support',
			array( $this, 'render_field_customer_support' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		// Register settings to be persisted.

		register_setting(
			'siteimprove_accessibility_settings',
			Siteimprove_Accessibility::OPTION_IS_WIDGET_ENABLED,
			array(
				'default' => true,
				'sanitize_callback' => array($this, 'sanitize_is_widget_enabled'),
			)
		);

		register_setting(
			'siteimprove_accessibility_settings',
			Siteimprove_Accessibility::OPTION_WIDGET_POSITION,
			array(
				'default' => 'top-right',
				'sanitize_callback' => array($this, 'sanitize_widget_position'),
			)
		);

		register_setting(
			'siteimprove_accessibility_settings',
			Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE,
			array(
				'default' => 'administrator',
				'sanitize_callback' => array($this, 'sanitize_allowed_user_role'),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render_field_is_widget_enabled(): void {
		$this->render( 'views/partials/field_is_widget_enabled.php' );
	}

	/**
	 * @return void
	 */
	public function render_field_widget_position(): void {
		$this->render(
			'views/partials/field_widget_position.php',
			array(
				'widget_position_options' => $this->get_widget_position_options(),
				'selected' => get_option( Siteimprove_Accessibility::OPTION_WIDGET_POSITION ),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render_field_allowed_user_role(): void {
		$this->render(
			'views/partials/field_allowed_user_role.php',
			array(
				'allowed_user_role_options' => $this->get_allowed_user_role_options(),
				'selected' => get_option( Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE ),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render_field_customer_support(): void {
		$this->render( 'views/partials/field_customer_support.php' );
	}

	/**
	 * @param $value
	 *
	 * @return int
	 */
	public function sanitize_is_widget_enabled($value): int {
		return (int) rest_sanitize_boolean($value);
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize_widget_position($value): string {
		$value = sanitize_text_field( $value );
		$options = $this->get_widget_position_options();

		return array_key_exists($value, $options) ? $value : key( $options );
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize_allowed_user_role($value): string {
		$value = sanitize_text_field( $value );
		$options = $this->get_allowed_user_role_options();

		return array_key_exists($value, $options) ? $value : key( $options );
	}

	/**
	 * @return array
	 */
	private function get_widget_position_options(): array {
		return array(
			'top-right' => __( 'Top Right', 'siteimprove-accessibility' ),
			'top-left'  => __( 'Top Left', 'siteimprove-accessibility' ),
			'bottom-right' => __( 'Bottom Right', 'siteimprove-accessibility' ),
			'bottom-left' => __( 'Bottom Left', 'siteimprove-accessibility' ),
		);
	}

	/**
	 * @return array
	 */
	private function get_allowed_user_role_options(): array {
		return array(
			'administrator' => __( 'Administrator', 'siteimprove-accessibility' ),
			'editor'  => __( 'Editor', 'siteimprove-accessibility' ),
			'author' => __( 'Author', 'siteimprove-accessibility' ),
			'contributor' => __( 'Contributor', 'siteimprove-accessibility' ),
		);
	}
}
