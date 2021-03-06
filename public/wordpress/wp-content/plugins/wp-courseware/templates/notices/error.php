<?php
/**
 * Error Message Template.
 *
 * This template can be overridden by copying it to yourtheme/wp-courseware/notices/error.php.
 *
 * @package WPCW
 * @subpackage Templates\Notices
 * @version 4.3.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Check for messages.
if ( ! $messages ) {
	return;
}

foreach ( $messages as $message ) : ?>
    <div class="wpcw-notice wpcw-notice-error"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach;
