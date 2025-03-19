<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Cron;

use Siteimprove\Accessibility\Service\Daily_Stats_Processor;
use Siteimprove\Accessibility\Service\Repository\Daily_Stats_Repository;

class Daily_Stats_Aggregation_Cron {

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

		add_action( 'siteimprove_accessibility_daily_stats_aggregation', array( $this, 'aggregate_daily_stats' ) );
	}

	/**
	 * @return void
	 */
	public function schedule(): void {
		if ( ! wp_next_scheduled( 'siteimprove_accessibility_daily_stats_aggregation' ) ) {
			wp_schedule_event(
				strtotime( 'today midnight', current_time( 'timestamp' ) ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'daily',
				'siteimprove_accessibility_daily_stats_aggregation'
			);
		}
	}

	/**
	 * @return void
	 */
	public function aggregate_daily_stats(): void {
		$aggregated_stats = $this->daily_stats_processor->get_aggregated_issues();
		$encoded_stats    = wp_json_encode( $aggregated_stats );
		$timestamp        = strtotime( '-1 day', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$latest_stats     = $this->daily_stats_repository->find_daily_stats( $timestamp );

		if ( empty( $latest_stats ) || $latest_stats[0]->aggregated_stats !== $encoded_stats ) {
			$this->daily_stats_repository->create_or_update_stats( $timestamp, $encoded_stats );
		}
	}
}
