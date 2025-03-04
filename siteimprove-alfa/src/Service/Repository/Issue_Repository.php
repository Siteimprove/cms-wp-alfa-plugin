<?php

namespace Siteimprove\Alfa\Service\Repository;

class Issue_Repository {

	/**
	 * @param string $rule
	 * @param string $conformance
	 *
	 * @return int|NULL The ID of the rule on success, NULL otherwise.
	 */
	public function create_or_update_rule( string $rule, string $conformance ): ?int {
		global $wpdb;

		$table_name = $wpdb->prefix . 'siteimprove_accessibility_rules';
		$data       = array(
			'rule'        => $rule,
			'conformance' => $conformance,
		);

		// TODO: add caching?
		$rule_id = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT id FROM %i WHERE rule = %s',
				$table_name,
				$rule
			)
		);

		if ( $rule_id ) {
			$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table_name,
				$data,
				array(
					'rule' => $rule,
				)
			);

			return false !== $result ? $rule_id : NULL;
		}

		$result = $wpdb->insert( $table_name, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return ( false !== $result ) ? $wpdb->insert_id : NULL;
	}

	/**
	 * @param int $scan_id
	 *
	 * @return bool
	 */
	public function delete_scan_occurrences( int $scan_id ): bool {
		global $wpdb;
		$table_name = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$result = $wpdb->delete( $table_name, array( 'scan_id' => $scan_id ), array( '%d' ) );

		return false !== $result;
	}

	/**
	 * @param int $scan_id
	 * @param int $rule_id
	 * @param int $occurrence
	 *
	 * @return int|null
	 */
	public function create_occurrence( int $scan_id, int $rule_id, int $occurrence ): ?int {
		global $wpdb;
		$table_name = $wpdb->prefix . 'siteimprove_accessibility_occurrences';

		$result = $wpdb->insert(
			$table_name,
			array(
				'scan_id'    => $scan_id,
				'rule_id'    => $rule_id,
				'occurrence' => $occurrence,
			)
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return ( false !== $result ) ? $wpdb->insert_id : NULL;
	}

	/**
	 * @return array
	 */
	public function find_all_issues(): array {
		global $wpdb;

		$rules_table = $wpdb->prefix . 'siteimprove_accessibility_rules';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';

		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT r.rule, r.conformance, SUM(o.occurrence) occurrence
			    FROM %i r
			    JOIN %i o ON o.rule_id = r.id
				GROUP BY r.id",
				$rules_table,
				$occurrences_table
			)
		);
	}

	/**
	 * @return array
	 */
	public function find_issues_with_pages(): array {
		global $wpdb;

		$rules_table = $wpdb->prefix . 'siteimprove_accessibility_rules';
		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$scans_table = $wpdb->prefix . 'siteimprove_accessibility_scans';

		// TODO: caching?
		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT r.id, r.rule, SUM(o.occurrence) occurrence, COUNT(s.id) pages
			    FROM %i r
			    JOIN %i o ON o.rule_id = r.id
			    JOIN %i s ON s.id = o.scan_id
				GROUP BY r.id",
				$rules_table,
				$occurrences_table,
				$scans_table
			)
		);
	}
}
