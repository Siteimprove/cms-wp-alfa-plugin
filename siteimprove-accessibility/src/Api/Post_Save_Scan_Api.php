<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Api;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Service\Repository\Issue_Repository;
use Siteimprove\Accessibility\Service\Repository\Scan_Repository;
use Siteimprove\Accessibility\Siteimprove_Accessibility;
use WP_REST_Request;
use WP_REST_Response;

class Post_Save_Scan_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-accessibility';
	private const ROUTE           = '/save-scan';
	private const METHOD          = 'POST';

	/**
	 * @var Scan_Repository
	 */
	private Scan_Repository $scan_repository;

	/**
	 * @var Issue_Repository
	 */
	private Issue_Repository $issue_repository;

	/**
	 * @param Scan_Repository $scan_repository
	 * @param Issue_Repository $issue_repository
	 */
	public function __construct( Scan_Repository $scan_repository, Issue_Repository $issue_repository ) {
		$this->scan_repository  = $scan_repository;
		$this->issue_repository = $issue_repository;
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
		$url          = $request['url'] ? sanitize_url( $request['url'] ) : null;
		$title        = $request['title'] ? sanitize_text_field( $request['title'] ) : null;
		$scan_results = rest_is_object( $request['scan_results'] ) ? rest_sanitize_object( $request['scan_results'] ) : array();
		$scan_stats   = rest_is_object( $request['scan_stats'] ) ? rest_sanitize_object( $request['scan_stats'] ) : array();

		if ( ! $url || ! $title ) {
			return new WP_REST_Response( 'Missing or invalid data!', 400 );
		}

		$scan_id = $this->scan_repository->create_or_update_scan( $scan_results, $url, $title, $post_id );
		if ( ! $scan_id ) {
			return new WP_REST_Response( 'Internal database error: scan saving failed!', 500 );
		}

		$this->issue_repository->delete_scan_occurrences( $scan_id );

		foreach ( $scan_stats as $rule => $details ) {
			$rule_id = $this->issue_repository->create_or_update_rule( $rule, $details['conformance'] );

			$this->issue_repository->create_occurrence( $scan_id, $rule_id, $details['occurrence'] );
		}

		return new WP_REST_Response( $this->create_response_summary( $scan_stats ) );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( get_option( Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE ) );
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
