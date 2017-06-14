<?php
if ( ! defined( 'ABSPATH' ) ) {
	echo "access denied";
	exit; // Exit if accessed directly
}
/**
 * Pass through version to use when Composer handles classes load.
 *
 * @param callable $callback
 */
function scb_init( $callback = null ) {
	if ( $callback ) {
		call_user_func( $callback );
	}
}
