<?php

namespace Siteimprove\Alfa\Service\Repository;

class Scan_Repository {

	/**
	 * @param array $scan_results
	 * @param array $scan_stats
	 * @param int|null $post_id
	 *
	 * @return int|null The ID of the inserted or updated row on success, null otherwise.
	 */
	public function create_or_update_scan( array $scan_results, array $scan_stats, int $post_id ): ?int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$data       = array(
			'post_id'      => $post_id,
			'scan_results' => wp_json_encode( $scan_results ),
			'scan_stats'   => wp_json_encode( $scan_stats ),
			'created_at'   => current_time( 'mysql' ),
		);

		$result = $wpdb->update( $table_name, $data, array( 'post_id' => $post_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( ! $result ) {
			$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		return $result ? $result : null;
	}

	/**
	 * @param int $post_id
	 *
	 * @return string|null
	 */
	public function find_scan_by_post_id( int $post_id ): ?string {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$result     = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_results FROM %i WHERE post_id = %d',
				$table_name,
				$post_id
			)
		);

		return $result;
	}

	/**
	 * @return array
	 */
	public function find_all_scan_stats(): array {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$results    = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_stats FROM %i',
				$table_name
			)
		);

		return $results;
	}
}
