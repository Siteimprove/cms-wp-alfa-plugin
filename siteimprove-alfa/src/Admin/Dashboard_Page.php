<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Admin;

use Siteimprove\Alfa\Core\Abstract_Page;
use Siteimprove\Alfa\Core\Hook_Interface;

class Dashboard_Page extends Abstract_Page implements Hook_Interface {

	public function register_hooks(): void {
		// TODO: register dashboard specific scripts.
	}

	public function render_page(): void {
		$this->render(
			'admin/views/dashboard.php',
		);
	}
}
