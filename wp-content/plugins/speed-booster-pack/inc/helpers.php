<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * @param string $notice_key
 * @param string $display_page
 *
 * @return int|bool|null
 */
function sbp_should_display_notice( $notice_key, $display_page = 'toplevel_page_sbp-options' ) {
	// Check if it's dismissed
	if ( current_user_can( 'manage_options' ) ) {
		$notices = get_user_meta( get_current_user_id(), 'sbp_notices', true );
		if ( isset( $notices[ $notice_key ] ) ) {
			return $notices[ $notice_key ];
		}
	}

	// Check the page
	$screen = get_current_screen();
	if ( $screen->id != $display_page && $display_page != 'all' ) {
		return false;
	}

	return true;
}

/**
 * @param string $key
 * @param bool|int $id
 *
 * @return void
 */
function sbp_dismiss_notice( $key = null, $id = false ) {
	if ( $key === "" ) {
		$key = $_GET['notice_key'];
	}

	if ( current_user_can( 'manage_options' ) ) {
		$notices = get_user_meta( get_current_user_id(), 'sbp_notices', true );
		if ( ! $notices ) {
			$notices = [];
		}
		$notices[ $key ] = $id;
		update_user_meta( get_current_user_id(), 'sbp_notices', $notices );
	}
}