<?php

namespace Siteimprove\Accessibility\Service\Repository;

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
					'id' => $scan_id,
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
	public function count_all_scans(): int {
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

		$scans = $wpdb->get_results( $wpdb->prepare( $query, $args ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $scans ) ) {
			$scans = $this->expand_scans_with_issues( $scans );
		}

		return array_map( array( $this, 'cast_page_fields' ), $scans );
	}

	/**
	 * @param array $params
	 *
	 * @return int
	 */
	public function count_pages_with_issues( array $params ): int {
		global $wpdb;

		list( $query, $args ) = $this->prepare_pages_with_issues_query( $params, false );

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM ($query) subquery", $args ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * @return void
	 */
	public function delete_scans_above_threshold(): void {
		global $wpdb;

		$scan_threshold    = 25;
		$scans_table       = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';

		// Delete occurrences table entries that are not part of the latest scans
		$wpdb->query( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'DELETE occurrences
				FROM %i occurrences
	            LEFT JOIN (
	            	SELECT id
	            	FROM %i
	            	ORDER BY id
	            	DESC LIMIT %d
                ) latest_scans ON occurrences.scan_id = latest_scans.id
	            WHERE latest_scans.id IS NULL',
				$occurrences_table,
				$scans_table,
				$scan_threshold
			)
		);

		// Delete scans table entries not within the scan threshold
		$wpdb->query( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'DELETE scans
				FROM %i scans 
                LEFT JOIN (
                	SELECT id
                	FROM %i
                	ORDER BY id
                	DESC LIMIT %d
                ) latest_scans ON scans.id = latest_scans.id
       			WHERE latest_scans.id IS NULL',
				$scans_table,
				$scans_table,
				$scan_threshold
			)
		);
	}

	/**
	 * @param array $params
	 * @param bool $use_limit
	 *
	 * @return array( query<string>, args<array>)
	 */
	private function prepare_pages_with_issues_query( array $params, bool $use_limit = true ): array {
		global $wpdb;

		$params = $this->sanitize_request_params( $params );
		$args   = array();

		$scans_table       = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';

		$query = "SELECT
				s.id,
				s.title,
				s.url,
				COUNT(o.rule_id) as issuesCount,
				SUM(o.occurrence) as occurrences,
				s.created_at as lastChecked
	        FROM $scans_table s
	        JOIN $occurrences_table o ON o.scan_id = s.id
	        WHERE 1 = 1";

		// Rule filtering
		if ( $params['rule_id'] ) {
			$query .= ' AND o.rule_id = %d';
			$args[] = $params['rule_id'];
		}

		// Dynamic search filtering
		if ( ! empty( $params['search_term'] ) && ! empty( $params['search_field'] ) ) {
			$query .= " AND {$params['search_field']} LIKE %s";
			$args[] = '%' . $wpdb->esc_like( $params['search_term'] ) . '%';
		}

		// Add GROUP BY
		$query .= ' GROUP BY s.id';

		// Sorting
		if ( ! empty( $params['sort_field'] ) && ! empty( $params['sort_direction'] ) ) {
			$query .= " ORDER BY {$params['sort_field']} {$params['sort_direction']}";
		}

		// Limit and Offset
		if ( $use_limit && isset( $params['limit'] ) && isset( $params['offset'] ) ) {
			$query .= ' LIMIT %d OFFSET %d';
			$args[] = $params['limit'];
			$args[] = $params['offset'];
		}

		return array( $query, $args );
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	private function sanitize_request_params( array $params ): array {
		$valid_field_names = array(
			'title',
			'url',
			'occurrences',
			'issuesCount',
			'lastChecked',
		);

		return array(
			'limit'          => (int) $params['pageSize'] ?? 10,
			'offset'         => ( (int) $params['pageSize'] ?? 10 ) * ( (int) ( $params['page'] ?? 1 ) - 1 ),
			'sort_field'     => in_array( $params['sort']['property'], $valid_field_names, true ) ? $params['sort']['property'] : null,
			'sort_direction' => strtoupper( $params['sort']['direction'] ?? '' ) === 'DESC' ? 'DESC' : 'ASC',
			'search_term'    => $params['query'] ?? null,
			'search_field'   => in_array( $params['searchType'], $valid_field_names, true ) ? $params['searchType'] : null,
			'rule_id'        => (int) $params['ruleId'] ?? null,
		);
	}

	/**
	 * @param array $scans
	 *
	 * @return array
	 */
	private function expand_scans_with_issues( array $scans ): array {
		global $wpdb;

		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$scan_ids          = array_map(
			function ( \stdClass $record ) {
				return (int) $record->id;
			},
			$scans
		);

		$query = sprintf( "SELECT * FROM $occurrences_table o WHERE o.scan_id IN (%s)", implode( ', ', $scan_ids ) );

		$occurrences = $wpdb->get_results( $wpdb->prepare( $query ) );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		foreach ( $occurrences as $occurrence ) {
			foreach ( $scans as $scan ) {
				if ( $scan->id === $occurrence->scan_id ) {
					$scan->issues   = is_array( $scan->issues ) ? $scan->issues : array();
					$scan->issues[] = array(
						'id'          => (int) $occurrence->rule_id,
						'occurrences' => (int) $occurrence->occurrence,
					);
					break;
				}
			}
		}

		return $scans;
	}

	/**
	 * @param \stdClass $page
	 *
	 * @return \stdClass
	 */
	private function cast_page_fields( \stdClass $page ): \stdClass {
		$page->id          = (int) $page->id;
		$page->occurrences = (int) $page->occurrences;
		$page->issuesCount = (int) $page->issuesCount; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page->lastChecked = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $page->lastChecked ) );  // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $page;
	}
}
