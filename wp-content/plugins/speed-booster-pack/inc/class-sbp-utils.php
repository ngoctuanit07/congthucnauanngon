<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class SBP_Utils {
	public static function get_file_extension( $url ) {
		// Remove Query String
		if ( strpos( $url, "?" ) !== false ) {
			$url = substr( $url, 0, strpos( $url, "?" ) );
		}
		if ( strpos( $url, "#" ) !== false ) {
			$url = substr( $url, 0, strpos( $url, "#" ) );
		}

		return pathinfo( $url, PATHINFO_EXTENSION );
	}
}