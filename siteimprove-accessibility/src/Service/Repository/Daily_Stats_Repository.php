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

		// Fetch the range of data within 'date_from' and 'date_to'.
		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT `date`, aggregated_stats FROM %i WHERE `date` >= %s AND `date` <= %s ORDER BY `date` ASC',
				$table_name,
				$date_from,
				$date_to
			)
		);

		if ( empty( $results ) || $results[0]->date > $date_from ) {
			// Fetch the latest data point before the 'date_from' timestamp for historical context.
			$pre_range_data = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT `date`, aggregated_stats FROM %i WHERE `date` < %s ORDER BY `date` DESC LIMIT 1',
					$table_name,
					$date_from
				)
			);

			if ( ! empty( $pre_range_data ) ) {
				// Set the date of the latest data point to 'date_from' for displaying only the relevant time period.
				$pre_range_data->date = $date_from;
				array_unshift( $results, $pre_range_data );
			}
		}

		return $results;
	}
}
