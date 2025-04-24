<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Service;

use Siteimprove\Accessibility\Service\Repository\Issue_Repository;
use Siteimprove\Accessibility\Service\Repository\Scan_Repository;
use stdClass;

class Daily_Stats_Processor {

	/**
	 * @var Scan_Repository
	 */
	private Scan_Repository $scan_repository;

	/**
	 * @var Issue_Repository
	 */
	private Issue_Repository $issue_repository;

	public function __construct( Scan_Repository $scan_repository, Issue_Repository $issue_repository ) {
		$this->scan_repository  = $scan_repository;
		$this->issue_repository = $issue_repository;
	}

	/**
	 * @return array {scans: <int>, rules: [<string>: [<string>: <int>]]}
	 */
	public function get_aggregated_issues(): array {
		$scan_count = $this->scan_repository->count_all_scans();
		$issues     = $this->issue_repository->find_all_issues();

		$aggregated_stats = array(
			'scans' => $scan_count,
			'rules' => array(),
		);

		foreach ( $issues as $issue ) {
			$aggregated_stats['rules'][ $issue->rule ][ $issue->conformance ] = ( $aggregated_stats['rules'][ $issue->rule ][ $issue->conformance ] ?? 0 ) + $issue->occurrences;
		}

		return $aggregated_stats;
	}

	/**
	 * @param array $results
	 *
	 * @return stdClass {issues: [date: <string>, pages: <int>, conformance: [<string>: <int>]]], occurrences: [date: <string>, pages: <int>, conformance: [<string>: <int>]]]}
	 */
	public function prepare_daily_stats( array $results ): stdClass {
		$daily_stats              = new StdClass();
		$daily_stats->issues      = array();
		$daily_stats->occurrences = array();

		if ( empty( $results ) ) {
			return $daily_stats;
		}

		$date = $results[0]->date;

		foreach ( $results as $i => $result ) {
			$stats = json_decode( $result->aggregated_stats, true );

			// Fill in the empty dates between the previous and the current stat records with repeated data.
			while ( $i > 0 && $date < $result->date ) {
				$daily_stats = $this->fill_empty_date( $daily_stats, $date, 'issues' );
				$daily_stats = $this->fill_empty_date( $daily_stats, $date, 'occurrences' );
				$date        = $this->next_day( $date );
			}

			// Prepare aggregated issues and occurrences of the current stat record.
			list($daily_stats->issues[], $daily_stats->occurrences[]) = $this->prepare_stat_record( $stats, $result->date );
			$date = $this->next_day( $result->date );
		}

		// Fill in the remaining empty dates until yesterday with repeated data.
		$end_date = wp_date( 'Y-m-d', strtotime( '-1 day' ) );
		while ( $date <= $end_date ) {
			$daily_stats = $this->fill_empty_date( $daily_stats, $date, 'issues' );
			$daily_stats = $this->fill_empty_date( $daily_stats, $date, 'occurrences' );
			$date        = $this->next_day( $date );
		}

		return $daily_stats;
	}

	/**
	 * @param stdClass $daily_stats
	 * @param string $date
	 *
	 * @return stdClass
	 */
	private function fill_empty_date( stdClass $daily_stats, string $date, string $data_key ): stdClass {
		$last_data                = end( $daily_stats->$data_key );
		$last_data['date']        = $date;
		$daily_stats->$data_key[] = $last_data;

		return $daily_stats;
	}

	/**
	 * @param string $date
	 *
	 * @return string
	 */
	private function next_day( string $date ): string {
		return wp_date( 'Y-m-d', strtotime( $date . ' +1 day' ) );
	}

	/**
	 * @param array $stats
	 * @param string $date
	 *
	 * @return array [date: <string>, pages: <int>, conformance: [<string>: <int>]]
	 */
	public function prepare_stat_record( array $stats, string $date ): array {
		$issues      = array();
		$occurrences = array();
		foreach ( $stats['rules'] as $level_group ) {
			foreach ( $level_group as $level => $amount ) {
				$issues[ $level ]      = ( $issues[ $level ] ?? 0 ) + 1;
				$occurrences[ $level ] = ( $occurrences[ $level ] ?? 0 ) + $amount;
			}
		}

		return array(
			array(
				'date'        => $date,
				'pages'       => $stats['scans'],
				'conformance' => $issues,
			),
			array(
				'date'        => $date,
				'pages'       => $stats['scans'],
				'conformance' => $occurrences,
			),
		);
	}
}
