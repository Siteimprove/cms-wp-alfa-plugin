<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;
use WP_REST_Request;
use WP_REST_Response;

class Post_Save_Scan_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/save-scan';
	private const METHOD          = 'POST';

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
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function handle_request( WP_REST_Request $request ): WP_REST_Response {
		$post_id      = $request['post_id'] ? (int) $request['post_id'] : null;
		$scan_results = rest_is_object( $request['scan_results'] ) ? rest_sanitize_object( $request['scan_results'] ) : null;
		$scan_stats   = rest_is_object( $request['scan_stats'] ) ? rest_sanitize_object( $request['scan_stats'] ) : null;

		if ( ! $post_id || ! $scan_results || ! $scan_stats ) {
			return new WP_REST_Response( 'Missing or invalid data!', 400 );
		}

		$result = $this->scan_repository->create_or_update_scan( $scan_results, $scan_stats, $post_id );

		if ( $result ) {
			return new WP_REST_Response( $this->create_response_summary( $scan_stats ) );
		}

		return new WP_REST_Response( 'Internal database error!', 500 );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @param array $scan_stats
	 *
	 * @return array
	 */
	private function create_response_summary( array $scan_stats ): array {
		$scan_stats = array_filter(
			$scan_stats,
			function ( $item ) {
				return ! empty( $item );
			}
		);

		return array(
			'count_issues' => count( $scan_stats ),
		);
	}
}
