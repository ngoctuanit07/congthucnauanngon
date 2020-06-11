<?php
/**
 * @package speed-booster-pack
 */

// Security control for vulnerability attempts
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Database Tables and Options name Configrations
$option_names = [ 'sbp_settings', 'sbp_js_footer_exceptions', 'sbp_css_exceptions', 'sbp_lazyload_exclusions', 'sbp_preboost' ];

if ( ! is_array( $option_names ) ) {
	$option_names = [ $option_names ];
}

foreach ( $option_names as $option_name ) {
	if ( is_multisite() ) {
		delete_site_option( $option_name );
	} else {
		delete_option( $option_name );
	}
}
