<?php
/**
 * WP Courseware Database Table Certificates.
 *
 * @package WPCW
 * @subpackage Database\Tables
 * @since 4.3.0
 */
namespace WPCW\Database\Tables;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class DB_Table_Certificates.
 *
 * @since 4.3.0
 */
final class DB_Table_Certificates extends DB_Table {

	/**
	 * @var string Table name
	 * @since 4.3.0
	 */
	protected $name = 'wpcw_certificates';

	/**
	 * @var int Database Table version
	 * @since 4.3.0
	 */
	protected $version = 450;

	/**
	 * Setup the database schema.
	 *
	 * @since 4.3.0
	 */
	protected function set_schema() {
		$this->schema = "cert_user_id int(11) NOT NULL,
			             cert_course_id int(11) NOT NULL,
			             cert_access_key varchar(50) NOT NULL,
			             cert_generated datetime NOT NULL,
			             UNIQUE KEY cert_user_id (cert_user_id,cert_course_id)";
	}

	/**
	 * Get Upgrades.
	 *
	 * @since 4.5.0
	 */
	protected function get_upgrades() {
		return array(
			'450' => 'upgrade_to_450',
		);
	}

	/**
	 * Upgrade to version 4.5.0
	 *
	 * @since 4.5.0
	 */
	protected function upgrade_to_450() {
		maybe_convert_table_to_utf8mb4( $this->table_name );
	}
}
