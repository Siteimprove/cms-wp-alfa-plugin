<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Api;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Service\Repository\Scan_Repository;
use WP_REST_Request;
use WP_REST_Response;

class Get_Pages_With_Issues_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-accessibility';
	private const ROUTE           = '/pages-with-issues';
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
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function handle_request( WP_REST_Request $request ): WP_REST_Response {
		$params = $this->sanitize_request_params( $request );
		$pages  = $this->scan_repository->find_pages_with_issues( $params );

		$result = array(
			'total'   => $this->scan_repository->count_pages_with_issues( $params ),
			'records' => $pages,
		);

		return new WP_REST_Response( $result );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	private function sanitize_request_params( WP_REST_Request $request ): array {
		$column_map = array(
			'Title'       => 'title',
			'URL'         => 'url',
			'issuesCount' => 'issues_count',
			'occurrences' => 'occurrences',
			'lastChecked' => 'lastChecked',
		);

		return array(
			'limit'          => (int) $request->get_param( 'pageSize' ) ?? 10,
			'offset'         => ( (int) $request->get_param( 'pageSize' ) ?? 10 ) * ( (int) $request->get_param( 'page' ) - 1 ),
			'sort_field'     => $column_map[ $request->get_param( 'sort' )['property'] ?? null ] ?? null,
			'sort_direction' => $request->get_param( 'sort' )['direction'] ?? 'ASC',
			'search_term'    => $request->get_param( 'query' ) ?? null,
			'search_field'   => $column_map[ $request->get_param( 'searchType' ) ?? null ] ?? null,
		);
	}
}
