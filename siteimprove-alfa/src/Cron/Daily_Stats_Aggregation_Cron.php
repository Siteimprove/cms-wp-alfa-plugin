<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Cron;

use Siteimprove\Alfa\Service\Daily_Stats_Processor;
use Siteimprove\Alfa\Service\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Service\Repository\Scan_Repository;

class Daily_Stats_Aggregation_Cron {

	private Scan_Repository $scan_repository;
	private Daily_Stats_Repository $daily_stats_repository;
	private Daily_Stats_Processor $daily_stats_processor;

	/**
	 * @param Scan_Repository $scan_repository
	 * @param Daily_Stats_Repository $daily_stats_repository
	 * @param Daily_Stats_Processor $daily_stats_processor
	 */
	public function __construct(
		Scan_Repository $scan_repository,
		Daily_Stats_Repository $daily_stats_repository,
		Daily_Stats_Processor $daily_stats_processor
	) {
		$this->scan_repository        = $scan_repository;
		$this->daily_stats_repository = $daily_stats_repository;
		$this->daily_stats_processor  = $daily_stats_processor;

		add_action( 'siteimprove_alfa_daily_stats_aggregation', array( $this, 'aggregate_daily_stats' ) );
	}

	/**
	 * @return void
	 */
	public function schedule(): void {
		if ( ! wp_next_scheduled( 'siteimprove_alfa_daily_stats_aggregation' ) ) {
			wp_schedule_event(
				strtotime( 'today midnight', current_time( 'timestamp' ) ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'daily',
				'siteimprove_alfa_daily_stats_aggregation'
			);
		}
	}

	/**
	 * @return void
	 */
	public function aggregate_daily_stats(): void {
		$scans = $this->scan_repository->find_all_scans( array( 'scan_stats' ) );

		$aggregated_stats = $this->daily_stats_processor->aggregate_scan_stats( $scans );

		$this->daily_stats_repository->create_or_update_stats(
			strtotime( '-1 day', current_time( 'timestamp' ) ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$aggregated_stats
		);
	}
}
