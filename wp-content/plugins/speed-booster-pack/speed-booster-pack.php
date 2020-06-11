<?php
/**
 * Plugin Name:        Speed Booster Pack
 * Plugin URI:        https://wordpress.org/plugins/speed-booster-pack/
 * Description:        Speed optimization is vital for SEO. Optimize your PageSpeed scores today!
 * Author:            Optimocha
 * Version:            3.8.6
 * Author URI:        https://optimocha.com/
 * License:            GPLv3 or later
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP:    5.6
 * Text Domain:        speed-booster-pack
 * Domain Path:        /lang
 *
 * Copyright 2015-2017 Tiguan (office@tiguandesign.com)
 * Copyright 05/05/2017 - 10/04/2017 ShortPixel (alex@shortpixel.com)
 * Copyright 2017-2019 MachoThemes (office@machothemes.com)
 * Copyright 2019 Optimocha (hey@optimocha.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/*----------------------------------------------------------------------------------------------------------
    Define some useful plugin constants
-----------------------------------------------------------------------------------------------------------*/

define( 'SPEED_BOOSTER_PACK_PATH', plugin_dir_path( __FILE__ ) );
define( 'SPEED_BOOSTER_PACK_URL', preg_replace( '#^https?:#', '', plugin_dir_url( __FILE__ ) ) );
define( 'SPEED_BOOSTER_PACK_VERSION', '3.8.6' );
define( 'SBP_CACHE_DIR', WP_CONTENT_DIR . '/cache/speed-booster' );
define( 'SBP_CACHE_URL', WP_CONTENT_URL . '/cache/speed-booster' );

/*----------------------------------------------------------------------------------------------------------
    Global Variables
-----------------------------------------------------------------------------------------------------------*/

$sbp_options = get_option( 'sbp_settings' ); // retrieve the plugin settings from the options table

/*----------------------------------------------------------------------------------------------------------
    Main Plugin Class
-----------------------------------------------------------------------------------------------------------*/

if ( ! class_exists( 'Speed_Booster_Pack' ) ) {
	class Speed_Booster_Pack {

		/*----------------------------------------------------------------------------------------------------------
			Function Construct
		-----------------------------------------------------------------------------------------------------------*/

		public function __construct() {
			if ( $this->should_run() !== true ) {
				return;
			}
			global $sbp_options;

			// Enqueue admin scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'sbp_admin_enqueue_scripts' ] );

			// load plugin textdomain
			add_action( 'plugins_loaded',
				function () {
					load_plugin_textdomain( 'speed-booster-pack' );
				} );

			// Get Utils
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/class-sbp-utils.php' );

			// Load plugin settings page
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/helpers.php' );

			// Load preboost class
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/class-sbp-preboost.php' );

			// Load Google Font Optimizer class
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/class-sbp-font-optimizer.php' );

			// Display & dismiss notices
			add_action( 'admin_notices', [ &$this, 'sbp_display_notices' ] );
			add_action( 'wp_ajax_sbp_dismiss_notice', 'sbp_dismiss_notice' );

			// Migrate to new JS Footer Mover function
			add_action( 'admin_init', [ $this, 'sbp_migrate_js_to_footer' ] );

			// Load plugin settings page
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/settings.php' );
			$Speed_Booster_Pack_Options = new Speed_Booster_Pack_Options();

			// Load main plugin functions
			require_once( SPEED_BOOSTER_PACK_PATH . 'inc/core.php' );
			$Speed_Booster_Pack_Core = new Speed_Booster_Pack_Core();

			// Enqueue admin style
			add_action( 'admin_enqueue_scripts', [ $this, 'sbp_enqueue_styles' ] );

			// Filters
			$this->path = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$this->path", [ $this, 'sbp_settings_link' ] );

		} // END public function __construct

		
		private function should_run() {

			/*
			(TODO: find cornerstone builder url)
			*/
			$page_builders = [
				"sbp_disable"                   => "1", // speed booster pack
				"action"                        => "elementor", // elementor
				"ai-debug-blocks"               => "1", // ad inserter
				"ao_noptimize"                  => "1", // autoptimize
				"ao_noptirocket"                => "1", // autoptimize & wp rocket
				"brizy"                         => "edit", // brizy builder
				"bt-beaverbuildertheme"         => "show", // beaver builder 2
				"ct_builder"                    => "true", // oxygen builder
				"customize_changeset_uuid"      => null, // wordpress core customizer
				"et_fb"                         => "1", // divi builder
				"fb-edit"                       => "1", // fusion builder
				"fl_builder"                    => null, // beaver builder 1
				"PageSpeed"                     => "off", // mod_pagespeed
				"preview"                       => "true", // wordpress core preview
				"siteorigin_panels_live_editor" => null, // siteorigin page builder
				"tb_preview"                    => "1", // themify builder
				"tipi_builder"                  => "1", // tipi builder
				"tve"                           => "true", // thrive architect
				"vc_action"                     => "vc_inline", // wpbakery page builder
			];

			global $sbp_options;

			foreach ( $page_builders as $page_builder => $value ) {
				if ( isset( $_GET[ $page_builder ] ) && ( $value == $_GET[ $page_builder ] || null == $value ) ) {
					return false;
				}
			}

			return true;
		}


		/*----------------------------------------------------------------------------------------------------------
			Display/dismiss admin notices if needed
		-----------------------------------------------------------------------------------------------------------*/

		function sbp_display_notices() {
			$is_using_new_version = ! ( get_option( 'sbp_js_footer_exceptions1' ) || get_option( 'sbp_js_footer_exceptions2' ) || get_option( 'sbp_js_footer_exceptions3' ) || get_option( 'sbp_js_footer_exceptions4' ) );

			if ( ! $is_using_new_version ) {
				include_once( SPEED_BOOSTER_PACK_PATH . 'inc/template/notices/migrate_js_to_footer.php' );
			}

			if ( sbp_should_display_notice( 'translate' ) ) {
				// Check user's language
				if ( get_user_locale() != 'en_US' && ! file_exists( WP_LANG_DIR . '/plugins/speed-booster-pack-' . get_user_locale() . '.mo' ) ) {
					// Get Available Languages
					require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
					$language_list = wp_get_available_translations();
					require_once( SPEED_BOOSTER_PACK_PATH . 'inc/template/notices/translate.php' );
				}
			}

			if ( sbp_should_display_notice( 'welcome', 'all' ) ) {
				global $sbp_settings_page;
				$screen = get_current_screen();
				if ( $screen->id != $sbp_settings_page ) {
					require_once( SPEED_BOOSTER_PACK_PATH . 'inc/template/notice.php' );
				}
			}

			if ( sbp_should_display_notice( 'beta_testers' ) ) {
				require_once( SPEED_BOOSTER_PACK_PATH . 'inc/template/notices/beta_testers.php' );
			}
		}

		/*----------------------------------------------------------------------------------------------------------
			Activate the plugin
		-----------------------------------------------------------------------------------------------------------*/

		public static function sbp_activate() {

			add_option( 'sbp_js_footer_exceptions', '/js/jquery/jquery.js' );

		} // END public static function sb_activate


		/*----------------------------------------------------------------------------------------------------------
			Deactivate the plugin
		-----------------------------------------------------------------------------------------------------------*/

		public static function sbp_deactivate() {
		}


		/*----------------------------------------------------------------------------------------------------------
			CSS style of the plugin options page
		-----------------------------------------------------------------------------------------------------------*/

		function sbp_enqueue_styles( $hook ) {

			// load stylesheet only on plugin options page
			global $sbp_settings_page;
			if ( $hook != $sbp_settings_page ) {
				return;
			}
			wp_enqueue_style( 'sbp-styles',
				plugin_dir_url( __FILE__ ) . 'css/style.css',
				null,
				SPEED_BOOSTER_PACK_VERSION );

		}


		/*----------------------------------------------------------------------------------------------------------
			Enqueue admin scripts to plugin options page
		-----------------------------------------------------------------------------------------------------------*/

		public function sbp_admin_enqueue_scripts( $hook_sbp ) {
			// load scripts only on plugin options page
			global $sbp_settings_page;

			wp_add_inline_script( 'jquery', 'jQuery(document).on(\'click\', \'.sbp-notice .notice-dismiss\', function() {
    var $notice = jQuery(this).parent();
    var notice_key = $notice.data(\'notice\');
    var data = {action: \'sbp_dismiss_notice\', notice_key: notice_key};
    jQuery.get(ajaxurl, data);
});' );

			if ( $hook_sbp != $sbp_settings_page ) {
				return;
			}
			wp_enqueue_script( 'postbox' );

			wp_enqueue_script( 'sbp-admin-scripts',
				plugins_url( 'inc/js/admin-scripts.js', __FILE__ ),
				[
					'jquery',
					'postbox'
				],
				SPEED_BOOSTER_PACK_VERSION,
				true );

			wp_enqueue_script( 'sbp-plugin-install',
				plugins_url( 'inc/js/plugin-install.js', __FILE__ ),
				[
					'jquery',
					'updates',
				],
				SPEED_BOOSTER_PACK_VERSION,
				true );

		}


		/*----------------------------------------------------------------------------------------------------------
			Add settings link on plugins page
		-----------------------------------------------------------------------------------------------------------*/

		function sbp_settings_link( $links ) {
			$pro_link      = ' <a href="https://optimocha.com/?ref=sbp" target="_blank">Pro Help</a > ';
			$settings_link = ' <a href="admin.php?page=sbp-options">Settings</a > ';
			array_unshift( $links, $settings_link );
			array_unshift( $links, $pro_link );

			return $links;

		} //    End function sbp_settings_link

		public function sbp_migrate_js_to_footer() {
			if ( isset( $_POST['migrate_js_mover'] ) ) {
				for ( $i = 1; $i <= 4; $i ++ ) {
					unregister_setting( 'speed_booster_settings_group', 'sbp_js_footer_exceptions' . $i );
					delete_option( 'sbp_js_footer_exceptions' . $i );
				}
			}
		}
	}// End class Speed_Booster_Pack
} //    End if (!class_exists("Speed_Booster_Pack")) (1)

if ( class_exists( 'Speed_Booster_Pack' ) ) {

	// Installation and uninstallation hooks
	register_activation_hook( __FILE__, [ 'Speed_Booster_Pack', 'sbp_activate' ] );
	register_deactivation_hook( __FILE__, [ 'Speed_Booster_Pack', 'sbp_deactivate' ] );

	// instantiate the plugin class
	$speed_booster_pack = new Speed_Booster_Pack();

}