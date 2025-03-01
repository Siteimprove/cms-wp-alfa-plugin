<?php

namespace Siteimprove\Alfa\Service\Repository;

class Scan_Repository {

	/**
	 * @param array $scan_results
	 * @param array $scan_stats
	 * @param int|null $post_id ID of post, or NULL if URL is defined.
	 * @param string|null $url URL of page, or NULL if post_id is defined.
	 *
	 * @return bool
	 */
	public function create_or_update_scan( array $scan_results, array $scan_stats, ?int $post_id, ?string $url ): bool {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$data       = array(
			'post_id'      => $post_id,
			'url'          => $url,
			'scan_results' => wp_json_encode( $scan_results ),
			'scan_stats'   => wp_json_encode( $scan_stats ),
			'created_at'   => current_time( 'mysql' ),
		);

		$exists = (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT COUNT(*) FROM %i WHERE %i = %s',
				$table_name,
				$post_id ? 'post_id' : 'url',
				$post_id ?: $url // phpcs:ignore Universal.Operators.DisallowShortTernary.Found
			)
		);

		if ( $exists ) {
			$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table_name,
				$data,
				array(
					'post_id' => $post_id,
					'url'     => $url,
				)
			);
		} else {
			$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		return ( false !== $result );
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function find_scan_by_post_id( int $post_id ): mixed {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$result     = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_results, created_at FROM %i WHERE post_id = %d',
				$table_name,
				$post_id
			)
		);

		return $result;
	}

	/**
	 * @param string $url
	 *
	 * @return mixed
	 */
	public function find_scan_by_url( string $url ): mixed {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_alfa_scans';
		$result     = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_results, created_at FROM %i WHERE url = %s',
				$table_name,
				$url
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
