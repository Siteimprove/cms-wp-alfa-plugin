<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Core;

class Service_Container {

	private array $services  = array();
	private array $instances = array();

	/**
	 * Register a service into the dependency injection container.
	 *
	 * @param string $name The service name.
	 * @param callable $service The closure or callable that defines how to instantiate the service.ű
	 *
	 * @return static
	 */
	public function register( string $name, callable $service ): static {
		$this->services[ $name ] = $service;

		return $this;
	}

	/**
	 * Get an instance of the service.
	 *
	 * @param string $name The service name.
	 *
	 * @return mixed The service object.
	 */
	public function get( string $name ): mixed {
		if ( ! isset( $this->services[ $name ] ) ) {
			throw new \LogicException( esc_html( sprintf( 'Service not registered: %s', $name ) ) );
		}

		if ( ! isset( $this->instances[ $name ] ) ) {
			$this->instances[ $name ] = $this->services[ $name ]( $this );
		}

		return $this->instances[ $name ];
	}
}
