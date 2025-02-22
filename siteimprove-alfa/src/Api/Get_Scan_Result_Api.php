<?php

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;
use WP_REST_Response;

class Get_Scan_Result_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/scan-result/(?P<id>\d+)';
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

	public function register_hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

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
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function handle_request( $request ): WP_REST_Response {
		$post_id = $request['id'];
		$result  = $this->scan_repository->find_scan_by_post_id( $post_id );

		if ( $result ) {
			return rest_ensure_response( json_decode( $result, true ) );
		} else {
			return rest_ensure_response( array() );
		}
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}
}
