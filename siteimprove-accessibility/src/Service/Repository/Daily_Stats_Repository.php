<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Service\Repository;

class Daily_Stats_Repository {

	/**
	 * @param int $timestamp
	 * @param array $stats
	 *
	 * @return bool
	 */
	public function create_or_update_stats( int $timestamp, array $stats ): bool {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_daily_stats';
		$date       = wp_date( 'Y-m-d', $timestamp );
		$data       = array(
			'date'             => $date,
			'aggregated_stats' => wp_json_encode( $stats ),
		);

		$exists = (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT COUNT(*) FROM %i WHERE date = %s',
				$table_name,
				$date
			)
		);

		if ( $exists ) {
			$result = $wpdb->update( $table_name, $data, array( 'date' => $date ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		} else {
			$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		return ( false !== $result );
	}

	/**
	 * @param int|null $timestamp_from
	 * @param int|null $timestamp_to
	 *
	 * @return array
	 */
	public function find_daily_stats( ?int $timestamp_from = null, ?int $timestamp_to = null ): array {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_daily_stats';
		$date_from  = wp_date( 'Y-m-d', $timestamp_from ?? strtotime( '-6 months', current_time( 'timestamp' ) ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$date_to    = wp_date( 'Y-m-d', $timestamp_to ?? current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		// TODO: if the last data point is behind the date_from time, then the first data point will be only the first entry from the selected date range, thus data before that data point won't be shown.

		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT `date`, aggregated_stats FROM %i WHERE `date` >= %s AND `date` <= %s ORDER BY `date` ASC',
				$table_name,
				$date_from,
				$date_to
			)
		);

		return $results;
	}
}
