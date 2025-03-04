<?php

namespace Siteimprove\Alfa\Service\Repository;

class Scan_Repository {

	/**
	 * @param array $scan_results
	 * @param string $url Unique URL of a page.
	 * @param string $title Title of the page.
	 * @param int|null $post_id Unique post ID or NULL.
	 *
	 * @return int|null ID of the scan, or NULL if query failed.
	 */
	public function create_or_update_scan( array $scan_results, string $url, string $title, ?int $post_id ): ?int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$data       = array(
			'post_id'      => $post_id,
			'url'          => $url,
			'title'        => $title,
			'scan_results' => wp_json_encode( $scan_results ),
			'created_at'   => current_time( 'mysql' ),
		);

		$scan_id = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT id FROM %i WHERE %i = %s',
				$table_name,
				$post_id ? 'post_id' : 'url',
				$post_id ?: $url // phpcs:ignore Universal.Operators.DisallowShortTernary.Found
			)
		);

		if ( $scan_id ) {
			$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table_name,
				$data,
				array(
					'post_id' => $post_id,
					'url'     => $url,
				)
			);

			return ( false !== $result ) ? $scan_id : NULL;
		}

		$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return ( false !== $result ) ? $wpdb->insert_id : NULL;
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function find_scan_by_post_id( int $post_id ): mixed {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';
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

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';
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
	 * @return int
	 */
	public function get_total_scan_count(): int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';

		// TODO: caching?
		return (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT COUNT(*) FROM %i',
				$table_name
			)
		);
	}

	/**
	 * @return array
	 */
	public function find_pages_with_issues(): array {
		global $wpdb;

		$scans_table = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$rules_table = $wpdb->prefix . 'siteimprove_accessibility_rules';

		// TODO: caching?
		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT s.id, s.title, s.url, COUNT(r.id) issues, SUM(o.occurrence) occurrence
			    FROM %i s
			    JOIN %i o ON o.scan_id = s.id
			    JOIN %i r ON r.id = o.rule_id
				GROUP BY s.id",
				$scans_table,
				$occurrences_table,
				$rules_table
			)
		);
	}
}
