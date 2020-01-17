<?php
/**
 * WP Courseware Database.
 *
 * @package WPCW
 * @subpackage Core
 * @since 4.3.0
 */
namespace WPCW\Core;

use WPCW\Database\Tables\DB_Table;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class DB.
 *
 * @since 4.3.0
 */
final class Database {

	/**
	 * @var array The array of created tables.
	 * @since 4.3.0
	 */
	private $tables;

	/**
	 * Database constructor.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		$this->setup_tables();
		$this->back_compat();
	}

	/**
	 * Setup Database Tables.
	 *
	 * @since 4.3.0
	 *
	 * @return array The array of database tables.
	 */
	private function setup_tables() {
		foreach ( $this->get_tables() as $table_key => $table_class_name ) {
			$table_class = "\\WPCW\\Database\\Tables\\$table_class_name";
			if ( class_exists( $table_class ) ) {
				$this->tables[ $table_key ] = new $table_class;
			}
		}
	}

	/**
	 * Fix Tables.
	 *
	 * @since 4.4.4
	 */
	public function fix_tables() {
		if ( ! empty( $this->tables ) ) {
			/** @var DB_Table $table */
			foreach ( $this->tables as $table ) {
				$table->maybe_fix();
			}
		}
	}

	/**
	 * Database Backwards Compat.
	 *
	 * @since 4.3.0
	 */
	private function back_compat() {
		global $wpcwdb;

		// old_reference => new_reference
		$map = array(
			'courses'              => 'courses',
			'coursemeta'           => 'coursemeta',
			'modules'              => 'modules',
			'units_meta'           => 'units',
			'user_courses'         => 'user_courses',
			'user_progress'        => 'user_progress',
			'user_progress_quiz'   => 'user_progress_quizzes',
			'quiz'                 => 'quizzes',
			'quiz_feedback'        => 'quizzes_feedback',
			'quiz_qs'              => 'quizzes_questions',
			'quiz_qs_mapping'      => 'quizzes_questions_map',
			'question_tags'        => 'question_tags',
			'question_tag_mapping' => 'question_tags_map',
			'question_rand_lock'   => 'question_random_lock',
			'map_member_levels'    => 'member_levels',
			'certificates'         => 'certificates',
			'queue_dripfeed'       => 'queue_dripfeed',
			'orders'               => 'orders',
			'ordermeta'            => 'ordermeta',
			'order_items'          => 'order_items',
			'order_itemmeta'       => 'order_itemmeta',
			'subscriptions'        => 'subscriptions',
			'coupons'              => 'coupons',
			'couponmeta'           => 'couponmeta',
			'logs'                 => 'logs',
			'notes'                => 'notes',
			'sessions'             => 'sessions',
		);

		foreach ( $map as $old_reference => $new_reference ) {
			if ( property_exists( $wpcwdb, $old_reference ) ) {
				$wpcwdb->{$old_reference} = $this->get_table_name( $new_reference );
			}
		}
	}

	/**
	 * Get Database Tables.
	 *
	 * @since 4.3.0
	 *
	 * @return array The defined table names and their classes.
	 */
	private function get_tables() {
		return array(
			'certificates'          => 'DB_Table_Certificates',
			'coursemeta'            => 'DB_Table_Course_Meta',
			'courses'               => 'DB_Table_Courses',
			'couponmeta'            => 'DB_Table_Coupon_Meta',
			'coupons'               => 'DB_Table_Coupons',
			'logs'                  => 'DB_Table_Logs',
			'member_levels'         => 'DB_Table_Member_Levels',
			'modules'               => 'DB_Table_Modules',
			'notes'                 => 'DB_Table_Notes',
			'order_itemmeta'        => 'DB_Table_Order_Item_Meta',
			'order_items'           => 'DB_Table_Order_Items',
			'ordermeta'             => 'DB_Table_Order_Meta',
			'orders'                => 'DB_Table_Orders',
			'question_random_lock'  => 'DB_Table_Question_Random_Lock',
			'question_tags'         => 'DB_Table_Question_Tags',
			'question_tags_map'     => 'DB_Table_Question_Tags_Map',
			'queue_dripfeed'        => 'DB_Table_Queue_Dripfeed',
			'quizzes'               => 'DB_Table_Quizzes',
			'quizzes_feedback'      => 'DB_Table_Quizzes_Feedback',
			'quizzes_questions'     => 'DB_Table_Quizzes_Questions',
			'quizzes_questions_map' => 'DB_Table_Quizzes_Questions_Map',
			'subscriptions'         => 'DB_Table_Subscriptions',
			'sessions'              => 'DB_Table_Sessions',
			'units'                 => 'DB_Table_Units',
			'user_courses'          => 'DB_Table_User_Courses',
			'user_progress'         => 'DB_Table_User_Progress',
			'user_progress_quizzes' => 'DB_Table_User_Progress_Quizzes',
		);
	}

	/**
	 * Get Database Table Name.
	 *
	 * @since 4.3.0
	 *
	 * @param string $key The database table key.
	 *
	 * @return string $table_name The database table name.
	 */
	public function get_table_name( $key = '' ) {
		$table      = isset( $this->tables[ $key ] ) ? $this->tables[ esc_attr( $key ) ] : null;
		$table_name = '';

		if ( $table instanceof DB_Table ) {
			$table_name = $table->get_table_name();
		}

		return $table_name;
	}

	/**
	 * Drop Tables.
	 *
	 * @since 4.4.0
	 */
	public function drop_tables() {
		global $wpdb;

		foreach ( $this->get_tables() as $table_key => $table_class_name ) {
			if ( $table_name = $this->get_table_name( $table_key ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
			}
		}
	}
}
