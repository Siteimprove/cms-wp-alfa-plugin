<?php

namespace Siteimprove\Alfa\Service\Repository;

class Scan_Repository {

	/**
	 * @param array $scan_results
	 * @param array $scan_stats
	 * @param int|null $post_id ID of post, or NULL if URL is defined.
	 * @param string|null $url URL of page, or NULL if post_id is defined.
	 *
	 * @return int|null The ID of the inserted or updated row on success, null otherwise.
	 */
	public function create_or_update_scan( array $scan_results, array $scan_stats, ?int $post_id, ?string $url ): ?int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$data       = array(
			'post_id'      => $post_id,
			'url'          => $url,
			'scan_results' => wp_json_encode( $scan_results ),
			'scan_stats'   => wp_json_encode( $scan_stats ),
			'created_at'   => current_time( 'mysql' ),
		);

		$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_name,
			$data,
			array(
				'post_id' => $post_id,
				'url'     => $url,
			)
		);

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
	 * @param array $select Name of the selected fields.
	 *
	 * @return array
	 */
	public function find_all_scans( array $select = array( '*' ) ): array {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$fields     = join( ', ', $select );

		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT $fields FROM %i", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$table_name
			)
		);
	}
}
