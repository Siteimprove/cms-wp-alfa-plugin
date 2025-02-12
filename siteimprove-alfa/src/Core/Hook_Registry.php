<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Core;

class Hook_Registry {

	/**
	 * @var array<Hook_Interface>
	 */
	private array $registry = array();

	/**
	 * @param Hook_Interface $hook
	 *
	 * @return $this
	 */
	public function add( Hook_Interface $hook ): static {
		$this->registry[] = $hook;

		return $this;
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		foreach ( $this->registry as $hook ) {
			$hook->register_hooks();
		}
	}
}
