<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Api;

use Siteimprove\Accessibility\Core\Hook_Interface;
use Siteimprove\Accessibility\Service\Daily_Stats_Processor;
use Siteimprove\Accessibility\Service\Repository\Daily_Stats_Repository;
use Siteimprove\Accessibility\Siteimprove_Accessibility;
use WP_REST_Response;

class Get_Daily_Stats_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-accessibility';
	private const ROUTE           = '/daily-stats';
	private const METHOD          = 'GET';

	private Daily_Stats_Repository $daily_stats_repository;
	private Daily_Stats_Processor $daily_stats_processor;

	/**
	 * @param Daily_Stats_Repository $daily_stats_repository
	 * @param Daily_Stats_Processor $daily_stats_processor
	 */
	public function __construct(
		Daily_Stats_Repository $daily_stats_repository,
		Daily_Stats_Processor $daily_stats_processor
	) {
		$this->daily_stats_repository = $daily_stats_repository;
		$this->daily_stats_processor  = $daily_stats_processor;
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
		// prepare daily stats from history
		$results     = $this->daily_stats_repository->find_daily_stats();
		$daily_stats = $this->daily_stats_processor->prepare_daily_stats( $results );

		// prepare today's stat from scans
		$aggregated_stats = $this->daily_stats_processor->get_aggregated_issues();
		list($daily_stats->issues[], $daily_stats->occurrences[]) = $this->daily_stats_processor->prepare_stat_record( $aggregated_stats, wp_date( 'Y-m-d' ) );

		return new WP_REST_Response( $daily_stats );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( get_option( Siteimprove_Accessibility::OPTION_ALLOWED_USER_ROLE ) );
	}
}
