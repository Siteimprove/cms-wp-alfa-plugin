<?php

namespace Siteimprove\Alfa\Core;

class Service_Container
{
	private array $services  = [];
	private array $instances = [];

	/**
	 * Register a service into the dependency injection container.
	 *
	 * @param string $name The service name.
	 * @param callable $callable The closure or callable that defines how to instantiate the service.ű
	 *
	 * @return static
	 */
	public function register($name, callable $callable): static
	{
		$this->services[$name] = $callable;

		return $this;
	}

	/**
	 * Get an instance of the service.
	 *
	 * @param string $name The service name.
	 *
	 * @return mixed The service object.
	 *
	 * @throws \Exception
	 */
	public function get(string $name): mixed
	{
		if (!isset($this->services[$name])) {
			throw new \Exception("Service not registered: " . $name);
		}

		if (!isset($this->instances[$name])) {
			$this->instances[$name] = $this->services[$name]($this);
		}

		return $this->instances[$name];
	}
}