<?php

namespace Siteimprove\Alfa\Cron;

use Siteimprove\Alfa\Core\Hook_Interface;
use Siteimprove\Alfa\Repository\Daily_Stats_Repository;
use Siteimprove\Alfa\Repository\Scan_Repository;

class Daily_Stats_Aggregation_Cron implements Hook_Interface {

	private Scan_Repository $scan_repository;
	private Daily_Stats_Repository $daily_stats_repository;

	/**
	 * @param Scan_Repository $scan_repository
	 * @param Daily_Stats_Repository $daily_stats_repository
	 */
	public function __construct( Scan_Repository $scan_repository, Daily_Stats_Repository $daily_stats_repository ) {
		$this->scan_repository        = $scan_repository;
		$this->daily_stats_repository = $daily_stats_repository;
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		if ( ! wp_next_scheduled( 'siteimprove_alfa_daily_stats_aggregation' ) ) {
			wp_schedule_event( time(), 'daily', 'siteimprove_alfa_daily_stats_aggregation' );
		}
	}

	/**
	 * @return void
	 */
	public function aggregate_daily_stats(): void {
		$scans = $this->scan_repository->find_all_scan_stats();

		$aggregated_stats = $this->aggregate_stats( $scans );

		$this->daily_stats_repository->insert_or_update_stats(
			strtotime( 'yesterday', current_time( 'timestamp' ) ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$aggregated_stats
		);
	}

	/**
	 * @param array $scans
	 *
	 * @return array
	 */
	private function aggregate_stats( array $scans ): array {
		$aggregated_stats = array(
			'scans' => count( $scans ),
			'rules' => array(),
		);

		foreach ( $scans as $scan ) {
			$stats = json_decode( $scan->scan_stats, true );
			foreach ( $stats as $rule => $conformanceLevels ) {
				foreach ( $conformanceLevels as $level => $amount ) {
					$aggregated_stats['rules'][ $rule ][ $level ] = ( $aggregated_stats['rules'][ $rule ][ $level ] ?? 0 ) + $amount;
				}
			}
		}

		return $aggregated_stats;
	}
}
