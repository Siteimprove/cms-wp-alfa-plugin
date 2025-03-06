<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Repository\Issue_Repository;
use WP_REST_Response;

class Get_Issues_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/issues';
	private const METHOD          = 'GET';

	/**
	 * @var Issue_Repository
	 */
	private Issue_Repository $issue_repository;

	/**
	 * @param Issue_Repository $issue_repository
	 */
	public function __construct( Issue_Repository $issue_repository ) {
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
	 * @return WP_REST_Response
	 */
	public function handle_request(): WP_REST_Response {
		$issues = $this->issue_repository->find_issues_with_pages();

		return new WP_REST_Response( $issues );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}
}
