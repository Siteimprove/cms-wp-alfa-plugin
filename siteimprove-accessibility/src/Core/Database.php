<?php declare(strict_types = 1);

namespace Siteimprove\Accessibility\Core;

class Database {

	/**
	 * @return void
	 */
	public function install(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$scans_table = $wpdb->prefix . 'siteimprove_accessibility_scans';
		$this->db_delta(
			$scans_table,
			"CREATE TABLE $scans_table (
		        id bigint(20) NOT NULL AUTO_INCREMENT,
		        post_id bigint(20) DEFAULT NULL,
		        url varchar(750) DEFAULT NULL,
		        title varchar(512) DEFAULT NULL,
		        scan_results longtext NOT NULL,
		        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		        PRIMARY KEY (id),
		        UNIQUE KEY post_id (post_id),
		        UNIQUE KEY url (url)
		    ) $charset_collate;"
		);

		$rules_table = $wpdb->prefix . 'siteimprove_accessibility_rules';
		$this->db_delta(
			$rules_table,
			"CREATE TABLE $rules_table (
		        id int NOT NULL AUTO_INCREMENT,
		        rule varchar(32) NOT NULL,
		        conformance varchar(32) NOT NULL,
		        PRIMARY KEY (id),
		        UNIQUE KEY rule (rule),
		        KEY conformance_idx (conformance)
		    ) $charset_collate;"
		);

		$occurrences_table = $wpdb->prefix . 'siteimprove_accessibility_occurrences';
		$this->db_delta(
			$occurrences_table,
			"CREATE TABLE $occurrences_table (
		        scan_id bigint(20) NOT NULL,
		        rule_id int NOT NULL,
		        occurrence int NOT NULL,
		        PRIMARY KEY (rule_id, scan_id)
		    ) $charset_collate;"
		);

		$stats_table = $wpdb->prefix . 'siteimprove_accessibility_daily_stats';
		$this->db_delta(
			$stats_table,
			"CREATE TABLE $stats_table (
		        `date` DATE NOT NULL,
		        aggregated_stats TEXT NOT NULL,
		        PRIMARY KEY (`date`)
		    ) $charset_collate;"
		);
	}

	/**
	 * @return void
	 */
	public function uninstall(): void {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i;', $wpdb->prefix . 'siteimprove_accessibility_occurrences' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i;', $wpdb->prefix . 'siteimprove_accessibility_rules' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i;', $wpdb->prefix . 'siteimprove_accessibility_scans' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i;', $wpdb->prefix . 'siteimprove_accessibility_daily_stats' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * @param string $table_name
	 * @param string $sql
	 *
	 * @return void
	 */
	private function db_delta( string $table_name, string $sql ): void {
		global $wpdb;

		dbDelta( $sql );

		if ( ! empty( $wpdb->last_error ) ) {
			throw new \RuntimeException( esc_html( sprintf( 'Error executing dbDelta on "%s" table: %s', $table_name, $wpdb->last_error ) ) );
		}
	}
}
