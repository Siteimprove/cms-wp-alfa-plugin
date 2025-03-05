<?php declare( strict_types=1 );

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Core\View_Trait;

class Settings_Page implements Hook_Interface {

	use View_Trait;

	const MENU_SLUG = 'siteimprove_accessibility_settings';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
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
		add_settings_section(
			'siteimprove_accessibility_manage_features_section',
			__('Manage features', 'siteimprove-accessibility'),
			'',
			'siteimprove_accessibility_settings'
		);

//		add_settings_field(
//			'siteimprove_accessibility_is_single_page_check_enabled', // TODO: add option constant
//			__('Single page checks', 'siteimprove-accessibility'),
//			array( $this, 'field_is_single_page_check_enabled_callback' ),
//			'siteimprove_accessibility_settings',
//			'siteimprove_accessibility_manage_features_section'
//		);

		add_settings_field(
			'siteimprove_accessibility_is_widget_enabled', // TODO: add option constant
			__('Enable widget', 'siteimprove-accessibility'),
			array( $this, 'field_is_widget_enabled_callback' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			'siteimprove_accessibility_widget_position', // TODO: add option constant
			'Widget position',
			array( $this, 'field_widget_position_callback' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			'siteimprove_accessibility_allowed_user_role', // TODO: add option constant
			'Minimum user role',
			array( $this, 'field_allowed_user_role_callback' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			'siteimprove_accessibility_allowed_rules', // TODO: add option constant
			'Allowed conformance levels',
			array( $this, 'field_allowed_rules_callback' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);

		add_settings_field(
			'siteimprove_accessibility_customer_support',
			'Customer Support',
			array( $this, 'field_customer_support_callback' ),
			'siteimprove_accessibility_settings',
			'siteimprove_accessibility_manage_features_section'
		);
	}

	public function field_is_single_page_check_enabled_callback(): void {
		$this->render('views/partials/field_is_single_page_check_enabled.php');
	}

	public function field_is_widget_enabled_callback(): void {
		$this->render('views/partials/field_is_widget_enabled.php');
	}

	public function field_widget_position_callback(): void {
		$this->render('views/partials/field_widget_position.php');
	}

	public function field_allowed_user_role_callback(): void {
		$this->render('views/partials/field_minimum_user_role.php');
	}

	public function field_allowed_rules_callback(): void {
		$this->render('views/partials/field_allowed_rules.php');
	}

	public function field_customer_support_callback(): void {
		$this->render('views/partials/field_customer_support.php');
	}
}
