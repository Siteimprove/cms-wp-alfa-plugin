<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Core;

trait View_Trait {

	/**
	 * @param string $view
	 * @param array $params
	 * @param bool $absolute
	 *
	 * @return void
	 */
	protected function render( string $view, array $params = array(), bool $absolute = false ): void {
		if ( ! $absolute ) {
			$view = SITEIMPROVE_ACCESSIBILITY_PLUGIN_ROOT_PATH . $view;
		}

		if ( ! file_exists( $view ) ) {
			throw new \LogicException( esc_html( sprintf( 'View file not found: %s', $view ) ) );
		}

		$this->include_view( $view, $params );
	}

	/**
	 * @param string $view
	 * @param array $params
	 *
	 * @return void
	 */
	private function include_view( string $view, array $params ): void {
		// Rendering the view in a static closure to reduce its scope to only the passed parameters. It still has access to the global scope.
		( static function () use ( $view, $params ) {
			foreach ( $params as $key => $value ) {
				${$key} = $value;
			}
			require $view;
		} )();
	}
}
