<?php
/**
 * Student Account - View Courses.
 *
 * This template can be overridden by copying it to yourtheme/wp-courseware/account/account-view-courses.php.
 *
 * @package WPCW
 * @subpackage Templates\Account
 * @version 4.3.0
 *
 * Variables available in this template:
 * ---------------------------------------------------
 * @var array $courses The array of student courses.
 * @var int $current_page The current page of the student courses.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$courses_columns = apply_filters( 'wpcw_student_account_courses_columns', array(
	'course-title'    => esc_html__( 'Course', 'wp-courseware' ),
	'course-progress' => esc_html__( 'Progress', 'wp-courseware' ),
) );

if ( $courses ) : ?>
    <h2><?php echo apply_filters( 'wpcw_student_account_courses_title', esc_html__( 'Enrolled Courses', 'wp-courseware' ) ); ?></h2>

	<?php echo do_shortcode( '[wpcw_course_progress user_progress="true" user_grade="true"]' ); ?>
<?php else : ?>
	<?php wpcw_print_notice( sprintf( __( 'You are not enrolled in any courses. <a href="%s">View Courses &rarr;</a>', 'wp-courseware' ), wpcw_get_page_permalink( 'courses' ) ), 'info' ); ?>
<?php endif; ?>
