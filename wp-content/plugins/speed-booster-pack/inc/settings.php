<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Speed_Booster_Pack_Options' ) ) {

	class Speed_Booster_Pack_Options {

		private $sbp_options;
		private $exclude_from_move = [
			'html5.js',
			'show_ads.js',
			'histats.com/js',
			'ws.amazon.com/widgets',
			'/ads/',
			'intensedebate.com',
			'scripts.chitika.net/',
			'jotform.com/',
			'gist.github.com',
			'forms.aweber.com',
			'video.unrulymedia.com',
			'stats.wp.com',
			'stats.wordpress.com',
			'widget.rafflecopter.com',
			'widget-prime.rafflecopter.com',
			'releases.flowplayer.org',
			'c.ad6media.fr',
			'cdn.stickyadstv.com',
			'www.smava.de',
			'contextual.media.net',
			'app.getresponse.com',
			'adserver.reklamstore.com',
			's0.wp.com',
			'wprp.zemanta.com',
			'files.bannersnack.com',
			'smarticon.geotrust.com',
			'js.gleam.io',
			'ir-na.amazon-adsystem.com',
			'web.ventunotech.com',
			'verify.authorize.net',
			'ads.themoneytizer.com',
			'embed.finanzcheck.de',
			'imagesrv.adition.com',
			'js.juicyads.com',
			'form.jotformeu.com',
			'speakerdeck.com',
			'content.jwplatform.com',
			'ads.investingchannel.com',
			'app.ecwid.com',
			'www.industriejobs.de',
			's.gravatar.com',
			'googlesyndication.com',
			'a.optmstr.com',
			'a.optmnstr.com',
			'a.opmnstr.com',
			'adthrive.com',
			'mediavine.com',
			'js.hsforms.net',
			'googleadservices.com',
			'f.convertkit.com',
			'recaptcha/api.js',
			'mailmunch.co',
			'apps.shareaholic.com',
			'dsms0mj1bbhn4.cloudfront.net',
			'nutrifox.com',
			'code.tidio.co',
			'www.uplaunch.com',
			'widget.reviewability.com',
			'embed-cdn.gettyimages.com/widgets.js',
			'app.mailerlite.com',
			'ck.page',
			'window.adsbygoogle',
			'google_ad_client',
			'googletag.display',
			'document.write',
			'google_ad',
			'adsbygoogle',
		];

		/*--------------------------------------------------------------------------------------------------------
			Construct the plugin object
		---------------------------------------------------------------------------------------------------------*/

		public function __construct() {

			add_action( 'admin_init', [ $this, 'sbp_admin_init' ] );
			add_action( 'admin_menu', [ $this, 'sbp_add_menu' ] );

			global $sbp_js_footer_exceptions;
			$sbp_js_footer_exceptions = explode( PHP_EOL, get_option( 'sbp_js_footer_exceptions' ) );
			$sbp_js_footer_exceptions = array_map( function ( $item ) {
				$item = trim( $item );

				return $item;
			},
				$sbp_js_footer_exceptions );
			$sbp_js_footer_exceptions = array_merge( $this->exclude_from_move, $sbp_js_footer_exceptions );
		}   //  END public function __construct


		public function sbp_admin_init() {

			register_setting( 'speed_booster_settings_group', 'sbp_settings' );
			register_setting( 'speed_booster_settings_group', 'sbp_css_exceptions' );
			register_setting( 'speed_booster_settings_group', 'sbp_js_footer_exceptions' );
			register_setting( 'speed_booster_settings_group', 'sbp_lazyload_exclusions' );
			register_setting( 'speed_booster_settings_group', 'sbp_preboost' );

		}  //  END public function admin_init

		/*--------------------------------------------------------------------------------------------------------
			// Add a page to manage the plugin's settings
		---------------------------------------------------------------------------------------------------------*/

		public function sbp_add_menu() {

			global $sbp_settings_page;
			$sbp_settings_page = add_menu_page( __( 'Speed Booster Options', 'speed-booster-pack' ),
				__( 'Speed Booster', 'speed-booster-pack' ),
				'manage_options',
				'sbp-options',
				[
					$this,
					'sbp_plugin_settings_page',
				],
				plugin_dir_url( __FILE__ ) . 'images/icon-16x16.png' );

		}   //  END public function add_menu()


		public function sbp_plugin_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}


			/*--------------------------------------------------------------------------------------------------------
				Global Variables used on options HTML page
			---------------------------------------------------------------------------------------------------------*/

			global $sbp_options;

			$this->plugin_url = plugin_dir_url( dirname( __FILE__ ) );

			// fallback for stylesheets exception handle
			$css_exceptions = get_option( 'sbp_css_exceptions' );

			// fallback for javascript exception handle
			$js_footer_exceptions = get_option( 'sbp_js_footer_exceptions' );


			$js_footer_exceptions1 = get_option( 'sbp_js_footer_exceptions1' );
			$js_footer_exceptions2 = get_option( 'sbp_js_footer_exceptions2' );
			$js_footer_exceptions3 = get_option( 'sbp_js_footer_exceptions3' );
			$js_footer_exceptions4 = get_option( 'sbp_js_footer_exceptions4' );

			$lazyload_exclusions = get_option( 'sbp_lazyload_exclusions' );
			$sbp_preboost = get_option( 'sbp_preboost' );


			/*--------------------------------------------------------------------------------------------------------*/


			// Render the plugin options page HTML
			include( SPEED_BOOSTER_PACK_PATH . 'inc/template/options.php' );

		} // END public function sbp_plugin_settings_page()

	}   //  END class Speed_Booster_Pack_Options

}   //  END if(!class_exists('Speed_Booster_Pack_Options'))
