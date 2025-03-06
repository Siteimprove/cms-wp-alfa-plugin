<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Core;

class Database {

	/**
	 * @return void
	 */
	public function install(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$scans_table = $wpdb->prefix . 'siteimprove_accessibility_scans';
		dbDelta(
			"CREATE TABLE $scans_table (
		        id bigint(20) NOT NULL AUTO_INCREMENT,
		        post_id bigint(20) DEFAULT NULL,
		        url varchar(2048) DEFAULT NULL,
		        title varchar(512) DEFAULT NULL,
		        scan_results longtext NOT NULL,
		        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		        PRIMARY KEY (id),
		        UNIQUE KEY post_id (post_id),
		        UNIQUE KEY url (url)
		    ) $charset_collate;"
		);

		$rules_table = $wpdb->prefix . 'siteimprove_accessibility_rules';
		dbDelta(
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
		dbDelta(
			"CREATE TABLE $occurrences_table (
		        scan_id bigint(20) NOT NULL,
		        rule_id int NOT NULL,
		        occurrence int NOT NULL,
		        PRIMARY KEY (rule_id, scan_id)
		    ) $charset_collate;"
		);

		$stats_table = $wpdb->prefix . 'siteimprove_accessibility_daily_stats';
		dbDelta(
			"CREATE TABLE $stats_table (
		        `date` DATE DEFAULT (CURDATE()) NOT NULL,
		        aggregated_stats mediumtext NOT NULL,
		        PRIMARY KEY (`date`)
		    ) $charset_collate;"
		);
	}

	/**
	 * @return void
	 */
	public function uninstall(): void {
		global $wpdb;

		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_accessibility_occurrences' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_accessibility_rules' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_accessibility_scans' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_accessibility_daily_stats' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}
}
