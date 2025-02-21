<?php declare(strict_types = 1);

namespace Siteimprove\Alfa\Core;

class Database {

	/**
	 * @return void
	 */
	public function install(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$scans_table = $wpdb->prefix . 'siteimprove_alfa_scans';
		$sql         = "CREATE TABLE $scans_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			post_id bigint(20) NOT NULL,
			scan_results longtext NOT NULL,
			scan_stats text NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
        	UNIQUE KEY post_id (post_id)
		) $charset_collate;";

		$daily_stats_table = $wpdb->prefix . 'siteimprove_alfa_daily_stats';
		$sql              .= "CREATE TABLE $daily_stats_table (
			`date` DATE DEFAULT (CURDATE()) NOT NULL,
			aggregated_stats mediumtext NOT NULL,
			PRIMARY KEY  (`date`)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}

	/**
	 * @return void
	 */
	public function uninstall(): void {
		global $wpdb;

		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_alfa_scans' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s%s;', $wpdb->prefix, 'siteimprove_alfa_daily_stats' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}
}
