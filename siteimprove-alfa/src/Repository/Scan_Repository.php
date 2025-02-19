<?php

namespace Siteimprove\Alfa\Repository;

class Scan_Repository {

	/**
	 * @param array $scan_result
	 * @param int|null $post_id
	 *
	 * @return int|null The ID of the inserted or updated row on success, null otherwise.
	 */
	public function create_or_update_scan( array $scan_result, ?int $post_id = null ): ?int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$data       = array(
			'post_id'     => $post_id,
			'scan_result' => wp_json_encode( $scan_result ),
			'created_at'  => current_time( 'mysql' ),
		);

		$result = $wpdb->update( $table_name, $data, array( 'post_id' => $post_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( ! $result ) {
			$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		return ( $result ) ? $wpdb->insert_id : null;
	}
}
