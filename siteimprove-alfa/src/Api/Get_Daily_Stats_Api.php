<?php

namespace Siteimprove\Alfa\Api;

use Siteimprove\Alfa\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Core\Hook_Interface;
use WP_REST_Response;

class Get_Daily_Stats_Api implements Hook_Interface {

	private const ROUTE_NAMESPACE = 'siteimprove-alfa';
	private const ROUTE           = '/daily-stats';
	private const METHOD          = 'GET';

	/**
	 * @var Daily_Stats_Repository
	 */
	private Daily_Stats_Repository $daily_stats_repository;

	/**
	 * @param Daily_Stats_Repository $daily_stats_repository
	 */
	public function __construct( Daily_Stats_Repository $daily_stats_repository ) {
		$this->daily_stats_repository = $daily_stats_repository;
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
	public function handle_request($request): WP_REST_Response {
		// TODO: add filtering based on request
		$results = $this->daily_stats_repository->find_daily_stats();

		if (!$results) { return rest_ensure_response([]); }

		// TODO: refactor and simplify code
		$issues = [];
		$occurrences = [];
		$endDate = date('Y-m-d', strtotime('-1 day'));
		$fill_date = $results[0]->date;

		foreach ($results as $i => $result) {
			$stats = json_decode($result->aggregated_stats, true);

			$issue_counters = $occurrence_counters = [];
			foreach ($stats['rules'] as $levelGroup) {
				foreach ($levelGroup as $level => $amount) {
					$issue_counters[$level] = ($issue_counters[$level] ?? 0) + 1;
					$occurrence_counters[$level] = ($occurrence_counters[$level] ?? 0) + $amount;
				}
			}

			while ($i > 0 && $fill_date < $result->date) {
				$last_issues = end($issues);
				$last_issues['date'] = $fill_date;
				$issues[] = $last_issues;

				$last_occurrences = end($occurrences);
				$last_occurrences['date'] = $fill_date;
				$occurrences[] = $last_occurrences;

				$fill_date = date('Y-m-d', strtotime($fill_date . ' +1 day'));
			}

			$issues[] = ['date' => $result->date, 'pages' => $stats['scans'], 'conformance' => $issue_counters];
			$occurrences[] = ['date' => $result->date, 'pages' => $stats['scans'], 'conformance' => $occurrence_counters];

			$fill_date = date('Y-m-d', strtotime($result->date . ' +1 day'));
		}

		// TODO: refactor code duplication
		while ($fill_date <= $endDate) {
			$last_issues = end($issues);
			$last_issues['date'] = $fill_date;
			$issues[] = $last_issues;

			$last_occurrences = end($occurrences);
			$last_occurrences['date'] = $fill_date;
			$occurrences[] = $last_occurrences;

			$fill_date = date('Y-m-d', strtotime($fill_date . ' +1 day'));
		}

		// TODO: also query and return up-to-date data for today's date

		return rest_ensure_response(['issues' => $issues, 'occurrences' => $occurrences]);
	}

	/**
	 * @return bool
	 */
	public function authenticate_request(): bool {
		return current_user_can( 'manage_options' );
	}
}
