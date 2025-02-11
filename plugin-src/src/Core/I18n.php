<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Core;

class I18n implements Hook_Interface {

	/**
	 * Register i18n hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		load_plugin_textdomain(
			'siteimprove-alfa',
			false,
			dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/'
		);
	}
}
