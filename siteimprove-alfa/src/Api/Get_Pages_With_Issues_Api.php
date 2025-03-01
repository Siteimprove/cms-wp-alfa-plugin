<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;
use WP_REST_Response;

class Get_Pages_With_Issues_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
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
	 * @return WP_REST_Response
	 */
	public function handle_request(): WP_REST_Response {
		$scans = $this->scan_repository->find_all_scans();

		if ( ! count( $scans ) ) {
			return new WP_REST_Response( array() );
		}

		$posts = $this->get_posts_by_scans( $scans );

		$results = array_map(
			function ( $item ) use ( $posts ) {
				return array(
					'post_id'      => $item->post_id ?? null,
					'title'        => $item->post_id ? get_the_title( $posts[ $item->post_id ] ) : null,
					'url'          => $item->post_id ? get_permalink( $posts[ $item->post_id ] ) : $item->url,
					'scan_results' => $item->scan_results,
					'scan_stats'   => $item->scan_stats,
				);
			},
			$scans
		);

		return new WP_REST_Response( $results );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @param array $scans
	 *
	 * @return \WP_Post[]
	 */
	private function get_posts_by_scans( array $scans ): array {
		$post_ids = array_filter(
			array_map(
				function ( $item ) {
					return $item->post_id;
				},
				$scans
			)
		);

		$posts_data = get_posts(
			array(
				'include'     => $post_ids,
				'post_type'   => 'any',
				'post_status' => array( 'publish', 'draft', 'pending', 'private' ),
				'numberposts' => - 1,
			)
		);

		$posts = array();
		foreach ( $posts_data as $post ) {
			$posts[ $post->ID ] = $post;
		}

		return $posts;
	}
}
