<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Service\Daily_Stats_Processor;
use Siteimprove\Alfa\Service\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;
use WP_REST_Request;
use WP_REST_Response;

class Get_Daily_Stats_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/daily-stats';
	private const METHOD          = 'GET';

	private Scan_Repository $scan_repository;
	private Daily_Stats_Repository $daily_stats_repository;
	private Daily_Stats_Processor $daily_stats_processor;

	/**
	 * @param Daily_Stats_Repository $daily_stats_repository
	 */
	public function __construct(
		Scan_Repository $scan_repository,
		Daily_Stats_Repository $daily_stats_repository,
		Daily_Stats_Processor $daily_stats_processor
	) {
		$this->scan_repository        = $scan_repository;
		$this->daily_stats_repository = $daily_stats_repository;
		$this->daily_stats_processor  = $daily_stats_processor;
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
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function handle_request( WP_REST_Request $request ): WP_REST_Response {
		// TODO: add filtering based on request
		$results = $this->daily_stats_repository->find_daily_stats();

		if ( ! $results ) {
			return new WP_REST_Response( array() );
		}

		// prepare daily stats from history
		$daily_stats = $this->daily_stats_processor->prepare_daily_stats( $results );

		// prepare today's stat from scans
		$scans            = $this->scan_repository->find_all_scan_stats();
		$aggregated_stats = $this->daily_stats_processor->aggregate_scan_stats( $scans );
		list($daily_stats->issues[], $daily_stats->occurrences[]) = $this->daily_stats_processor->prepare_stat_record( $aggregated_stats, wp_date( 'Y-m-d' ) );
		// TODO: seems like that even though the data is available for the current day, the chart doesn't render it for some reason. Probably the issue is timezone mismatch related.

		return new WP_REST_Response( $daily_stats );
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}
}
