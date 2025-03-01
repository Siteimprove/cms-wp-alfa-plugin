<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;
use WP_REST_Response;

class Get_Issues_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/issues';
	private const METHOD          = 'GET';

	/**
	 * @var Scan_Repository
	 */
	private Scan_Repository $scan_repository;

	/**
	 * @param Scan_Repository $scan_repository
	 */
	public function __construct( Scan_Repository $scan_repository ) {
		$this->scan_repository = $scan_repository;
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			static::ROUTE_NAMESPACE,
			static::ROUTE,
			array(
				'methods'             => static::METHOD,
				'callback'            => array( $this, 'handle_request' ),
				'permission_callback' => array( $this, 'authenticate_request' ),
			)
		);
	}

	/**
	 * @return WP_REST_Response
	 */
	public function handle_request(): WP_REST_Response {
		$scans = $this->scan_repository->find_all_scans( array( 'scan_stats' ) );

		if ( ! count( $scans ) ) {
			return new WP_REST_Response( array() );
		}

		$issues = array();
		foreach ( $scans as $scan ) {
			$stats = json_decode( $scan->scan_stats, true );
			foreach ( $stats as $rule => $conformance_levels ) {
				$issues[ $rule ]['pages'] = ( $issues[ $rule ]['pages'] ?? 0 ) + 1;
				foreach ( $conformance_levels as $amount ) {
					$issues[ $rule ]['occurrences'] = ( $issues[ $rule ]['occurrences'] ?? 0 ) + $amount;
				}
			}
		}

		return new WP_REST_Response( $issues );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}
}
