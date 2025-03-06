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

			return ( false !== $result ) ? $scan_id : null;
		}

		$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return ( false !== $result ) ? $wpdb->insert_id : null;
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function find_scan_by_post_id( int $post_id ): mixed {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';

		return $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_results, created_at FROM %i WHERE post_id = %d',
				$table_name,
				$post_id
			)
		);
	}

	/**
	 * @param string $url
	 *
	 * @return mixed
	 */
	public function find_scan_by_url( string $url ): mixed {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';

		return $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT scan_results, created_at FROM %i WHERE url = %s',
				$table_name,
				$url
			)
		);
	}

	/**
	 * @return int
	 */
	public function get_total_scan_count(): int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_scans';

		return (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT COUNT(*) FROM %i',
				$table_name
			)
		);
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	public function find_pages_with_issues( array $params ): array {
		global $wpdb;

		list($query, $args) = $this->prepare_pages_with_issues_query( $params );
		$results            = $wpdb->get_results( $wpdb->prepare( $query, $args ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

		return array_map( array( $this, 'cast_page_fields' ), $results );
	}

	/**
	 * @param array $params
	 *
	 * @return int
	 */
	public function count_pages_with_issues( array $params ): int {
		global $wpdb;

		$params['limit']  = null;
		$params['offset'] = null;

		list( $query, $args ) = $this->prepare_pages_with_issues_query( $params );

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM ($query) subquery", $args ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	private function prepare_pages_with_issues_query( array $params ): array {
		global $wpdb;
		$args = array();

		$scans_table       = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$rules_table       = $wpdb->prefix . 'siteimprove_accessibility_rules';

		$sql = "SELECT s.id, s.title, s.url, SUM(o.occurrence) as occurrences, GROUP_CONCAT(r.id) as issues, COUNT(r.id) as issues_count, s.created_at as lastChecked
	        FROM $scans_table s
	        JOIN $occurrences_table o ON o.scan_id = s.id
	        JOIN $rules_table r ON r.id = o.rule_id
	        WHERE 1=1";

		// Dynamic search filtering
		if ( ! empty( $params['search_term'] ) && ! empty( $params['search_field'] ) ) {
			$sql   .= " AND {$params['search_field']} LIKE %s";
			$args[] = '%' . $wpdb->esc_like( $params['search_term'] ) . '%';
		}

		// Add GROUP BY
		$sql .= ' GROUP BY s.id';

		// Sorting
		if ( ! empty( $params['sort_field'] ) && ! empty( $params['sort_direction'] ) ) {
			$sql .= " ORDER BY {$params['sort_field']} {$params['sort_direction']}";
		}

		// Limit and Offset
		if ( isset( $params['limit'] ) && isset( $params['offset'] ) ) {
			$sql   .= ' LIMIT %d OFFSET %d';
			$args[] = $params['limit'];
			$args[] = $params['offset'];
		}

		return array( $sql, $args );
	}

	/**
	 * @param \stdClass $page
	 *
	 * @return \stdClass
	 */
	private function cast_page_fields( \stdClass $page ): \stdClass {
		$page->id          = (int) $page->id;
		$page->occurrences = (int) $page->occurrences;
		$page->issues      = array_map( 'intval', explode( ',', $page->issues ) );

		return $page;
	}
}
