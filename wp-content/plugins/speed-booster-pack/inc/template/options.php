<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// handle closed postboxes
$user_id     = get_current_user_id();
$option_name = 'closedpostboxes_' . 'toplevel_page_sbp-options'; // use the "pagehook" ID
$option_arr  = get_user_option( $option_name, $user_id ); // get the options for that page


if ( is_array( $option_arr ) && in_array( 'exclude-from-footer', $option_arr ) ) {
	$closed = true;
}


if ( is_array( $option_arr ) && in_array( 'defer-from-footer', $option_arr ) ) {
	$closed_defer = true;
}

if ( isset( $_POST['migrate_js_mover'] ) ) {
	for ( $i = 1; $i <= 4; $i ++ ) {
		unregister_setting( 'speed_booster_settings_group', 'sbp_js_footer_exceptions' . $i );
		delete_option( 'sbp_js_footer_exceptions' . $i );
	}
}

?>

<div class="wrap">

    <div class="sb-pack clearfix">

        <div class="col-main">

            <h1 class="admin-page-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <h2 class="nav-tab-wrapper wp-clearfix">
                <a class="nav-tab" href="#general-options"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'General', 'speed-booster-pack' ); ?></a>
                <a class="nav-tab" href="#advanced-options"><span class="dashicons dashicons-admin-settings"></span> <?php _e( 'Advanced', 'speed-booster-pack' ); ?></a>
                <a class="nav-tab" href="#cdn-options"><span class="dashicons dashicons-admin-site-alt"></span> <?php _e( 'CDN', 'speed-booster-pack' ); ?></a>
                <a class="nav-tab" href="#google-analytics"><span class="dashicons dashicons-chart-area"></span> <?php _e( 'Google Analytics', 'speed-booster-pack' ); ?></a>
                <a class="nav-tab" href="#optimize-more"><span class="dashicons dashicons-dashboard"></span> <?php _e( 'Optimize More', 'speed-booster-pack' ); ?></a>
            </h2>

            <form method="post" action="admin.php?page=sbp-options" id="migrate_js_mover_form">
                <input type="hidden" name="migrate_js_mover" value="1">
            </form>

            <form method="post" id="sbp-form" action="options.php">

				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php settings_fields( 'speed_booster_settings_group' ); ?>

				<?php
				$is_using_new_version = ! ( get_option( 'sbp_js_footer_exceptions1' ) || get_option( 'sbp_js_footer_exceptions2' ) || get_option( 'sbp_js_footer_exceptions3' ) || get_option( 'sbp_js_footer_exceptions4' ) );
				$jquery_exceptions    = [
					'type'  => 'section',
					'label' => __( 'JavaScript Optimization', 'speed-booster-pack' ),
					'items' => [
						'break_warning'    => [
							'type' => 'warning',
							'text' => __( 'This feature has the MOST potential to break your site! Please use with caution and test your website after enabling this option. Excluding inline scripts and/or JavaScript file URLs below might help fix the issues you might encounter.', 'speed-booster-pack' ),
						],
						'jquery_to_footer' => [
							'type'          => 'checkbox',
							'label'         => __( 'Move scripts to footer', 'speed-booster-pack' ),
							'tooltip'       => __( 'This option move all scripts to the footer while keeping stylesheets in the header to improve page loading speed and get a higher score on the major speed testing sites such as GTmetrix or other website speed testing tools', 'speed-booster-pack' ),
							'options_group' => 'sbp_settings',
						],
					],
				];

				if ( ! $is_using_new_version ) {
					for ( $i = 1; $i < 5; $i ++ ) {
						$jquery_exceptions['items']["sbp_js_footer_exceptions$i"] = [
							'name' => "sbp_js_footer_exceptions$i",
							'type' => 'text',
						];
					}
				} else {
					$jquery_exceptions['items']['sbp_js_footer_exceptions'] = [
						'label' => __( 'Each line will be taken as a separate exclusion rule; you can exclude URLs, parts of URLs or parts of inline scripts. DON\'T enter whole blocks of inline scripts!', 'speed-booster-pack' ),
						'type'  => 'textarea',
						'name'  => 'sbp_js_footer_exceptions'
					];
				}

				$sbp_options_array = [
					//general options panel
					'general-options'  => [
						//General section
						'sections' => [
							[
								'type'  => 'section',
								'label' => __( 'Safe Optimizations', 'speed-booster-pack' ),
								'items' => [
									'query_strings'          => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove query strings', 'speed-booster-pack' ),
										'tooltip'       => __( 'Since most proxies do not cache resources with a ? in their URL, this option allows you to remove any query strings (version numbers) from static resources like CSS & JS files, thus improving your speed scores in services like GTmetrix, PageSpeed, YSlow and Pingdom.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_emojis'          => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove WordPress Emoji scripts', 'speed-booster-pack' ),
										'tooltip'       => __( 'Emojis are fun and all, but if you are aren’t using them they actually load a JavaScript file (wp-emoji-release.min.js) on every page of your website. For a lot of businesses, this is not needed and simply adds load time to your site. So we recommend disabling this.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_wsl'             => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove WordPress Shortlink', 'speed-booster-pack' ),
										'tooltip'       => __( 'WordPress URL shortening is sometimes useful, but it automatically adds an ugly code in your header, so you can remove it.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_adjacent'        => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove Adjacent Posts Links', 'speed-booster-pack' ),
										'tooltip'       => __( 'WordPress incorrectly implements this feature that supposedly should fix a pagination issues but it messes up, so there is no reason to keep these around. However, some browsers may use Adjacent Posts Links to navigate your site, although you can remove it if you run a well designed theme.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'wml_link'               => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove Windows Live Writer Manifest', 'speed-booster-pack' ),
										'tooltip'       => __( 'Windows Live Writer (WLW) is a Microsoft application for composing and managing blog posts offline and publish them later. If you are not using Windows Live Writer application, you can remove it from the WP head.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'wp_generator'           => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove WordPress Version', 'speed-booster-pack' ),
										'tooltip'       => __( 'Remove the META tag that reveals the WordPress version you use. Not exactly a speed-related matter, but you should consider it as an important security measure.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_self_pingbacks' => [
										'type'          => 'checkbox',
										'label'         => __( 'Disable Self Pingbacks', 'speed-booster-pack' ),
										'tooltip'       => __( 'A pingback is a special type of comment that’s created when you link to another blog post, as long as the other blog is set to accept pingbacks.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_jquery_migrate'  => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove jQuery Migrate', 'speed-booster-pack' ),
										'tooltip'       => __( 'Remove the jquery-migrate.js script that helps older jQuery plugins to be compatible with new jQuery versions. You safely turn this setting on if your jQuery plugins are all new.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_dashicons'      => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove Dashicons', 'speed-booster-pack' ),
										'tooltip'       => __( 'Remove Dashicons from front end.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_heartbeat'      => [
										'type'          => 'checkbox',
										'label'         => __( 'Disable Heartbeat', 'speed-booster-pack' ),
										'tooltip'       => __( 'Disable heartbeat everywhere ( used for autosaving and revision tracking ).', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'heartbeat_frequency'    => [
										'type'          => 'select',
										'label'         => __( 'Heartbeat frequency', 'speed-booster-pack' ),
										'tooltip'       => __( 'Controls how often the WordPress Heartbeat API is allowed to run. ', 'speed-booster-pack' ),
										'options'       => [
											'15' => '15',
											'30' => '30',
											'45' => '45',
											'60' => '60',
										],
										'options_group' => 'sbp_settings',
									],
									'limit_post_revisions'   => [
										'type'          => 'select',
										'label'         => __( 'Limit Post Revisions', 'speed-booster-pack' ),
										'tooltip'       => __( 'Controls how many revisions WordPress will save ', 'speed-booster-pack' ),
										'options'       => [
											'1'     => '1',
											'2'     => '2',
											'3'     => '3',
											'4'     => '4',
											'5'     => '5',
											'10'    => '10',
											'15'    => '15',
											'20'    => '20',
											'25'    => '25',
											'30'    => '30',
											'false' => 'Disable',
										],
										'options_group' => 'sbp_settings',
									],
									'autosave_interval'      => [
										'type'          => 'select',
										'label'         => __( 'Autosave interval', 'speed-booster-pack' ),
										'tooltip'       => __( 'Controls how WordPress will autosave posts and pages while editing.', 'speed-booster-pack' ),
										'options'       => [
											'1' => __( '1 minute', 'speed-booster-pack' ),
											'2' => __( '2 minutes', 'speed-booster-pack' ),
											'3' => __( '3 minutes', 'speed-booster-pack' ),
											'4' => __( '4 minutes', 'speed-booster-pack' ),
											'5' => __( '5 minutes', 'speed-booster-pack' ),
										],
										'options_group' => 'sbp_settings',
									],
								],
							],
						],
					],
					//advanced options panel
					'advanced-options' => [
						'sections' => [
							[
								'type'  => 'section',
								'label' => __( 'Advanced Optimizations', 'speed-booster-pack' ),
								'items' => [
									'sbp_optimize_fonts'              => [
										'type'          => 'checkbox',
										'label'         => __( '(BETA)', 'speed-booster-pack' ) . ' ' . __( 'Optimize Google Fonts', 'speed-booster-pack' ),
										'tooltip'       => __( 'This feature effectively gathers all the Google Font URLs in your pages together, combines all of them in one URL and adds the famous "font-display:swap" CSS feature.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'enable_instant_page'             => [
										'type'          => 'checkbox',
										'label'         => __( 'Enable instant.page', 'speed-booster-pack' ),
										'tooltip'       => __( 'Check this option if you want to use the instant.page link preloader. This is a new and experimental feature; use with caution. If something goes wrong, simply uncheck this option and save the settings.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_cart_fragments'          => [
										'type'          => 'checkbox',
										'label'         => __( 'WooCommerce: Disable cart fragments', 'speed-booster-pack' ),
										'tooltip'       => __( 'Check this option to disable WooCommerce&#39;s &quot;cart fragments&quot; script, which overrides all caching function to update cart totals on each page load in your theme header. This is a new and experimental feature; use with caution. If something goes wrong, simply uncheck this option and save the settings.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'dequeue_wc_scripts'              => [
										'type'          => 'checkbox',
										'label'         => __( '(BETA)', 'speed-booster-pack' ) . ' ' . __( 'Remove WooCommerce scripts in non-WC pages', 'speed-booster-pack' ),
										'tooltip'       => __( 'Check this option if you want to remove WooCommerce scripts in non-WooCommerce pages. This is a new and experimental feature; use with caution. If something goes wrong, simply uncheck this option and save the settings.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_password_strength_meter' => [
										'type'          => 'checkbox',
										'label'         => __( '(BETA)', 'speed-booster-pack' ) . ' ' . __( 'WooCommerce: Disable password strength meter', 'speed-booster-pack' ),
										'tooltip'       => __( 'Check this option if you want to disable WooCommerce password strength meter. This is a new and experimental feature; use with caution. If something goes wrong, simply uncheck this option and save the settings.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'disable_google_maps'             => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove Google Maps', 'speed-booster-pack' ),
										'tooltip'       => __( 'Remove Google Maps from front end. ', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_rest_api_links'           => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove REST API Links', 'speed-booster-pack' ),
										'tooltip'       => __( 'The WordPress REST API provides API endpoints for WordPress data types that allow developers to interact with sites remotely by sending and receiving JSON (JavaScript Object Notation) objects.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'remove_all_feeds'                => [
										'type'          => 'checkbox',
										'label'         => __( 'Remove all RSS feed links', 'speed-booster-pack' ),
										'tooltip'       => __( 'This option will remove all RSS feed links to cleanup your WordPress header. It is also useful on Unicorn – The W3C Markup Validation Service to get rid out the “feed does not validate” error.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'minify_html_js'                  => [
										'type'          => 'checkbox',
										'label'         => __( 'Minify HTML', 'speed-booster-pack' ),
										'tooltip'       => __( 'Activate this option only if you don’t want to use other minify plugins or other speed optimization plugin that has minify option included. If something goes wrong, simply uncheck this option and save the settings.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
								],
							],
							[
								'type'  => 'section',
								'label' => __( 'Lazy Load', 'speed-booster-pack' ),
								'items' => [
									'sbp_enable_lazy_load'    => [
										'type'          => 'checkbox',
										'label'         => __( '(BETA)', 'speed-booster-pack' ) . ' ' . __( 'Enable lazy loading of images, videos and iframes', 'speed-booster-pack' ),
										'tooltip'       => __( 'Check this option if you want to enable native lazy loading with fallback.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_lazyload_exclusions' => [
										'type'        => 'textarea',
										'name'        => 'lazyload_exclusions',
										'label'       => __( 'Exclude assets from lazy load', 'speed-booster-pack' ),
										'description' => __( 'Exclude assets from being lazy loaded by id, class, src or srcset.', 'speed-booster-pack' ),
									],
								],
							],
							$jquery_exceptions,
							[
								'type'  => 'section',
								'label' => __( 'CSS Optimization', 'speed-booster-pack' ),
								'items' => [
									'sbp_css_async'      => [
										'type'          => 'checkbox',
										'label'         => __( 'Inline all CSS', 'speed-booster-pack' ),
										'tooltip'       => __( 'Checking this option will inline the contents of all your stylesheets. This helps with the annoying render blocking error Google Page Speed Insights displays.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_css_minify'     => [
										'type'          => 'checkbox',
										'label'         => __( 'Minify all (previously) inlined CSS', 'speed-booster-pack' ),
										'tooltip'       => __( 'Minifying all inlined CSS styles will optimize the CSS delivery and will eliminate the annoying message on Google Page Speed regarding to render-blocking css.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_footer_css'     => [
										'type'          => 'checkbox',
										'label'         => __( 'Move all inlined CSS into the footer', 'speed-booster-pack' ),
										'tooltip'       => __( 'Inserting all CSS styles inline to the footer is a sensitive option that will eliminate render-blocking CSS warning in Google Page Speed test. If there is something broken after activation, you need to disable this option. Please note that before enabling this sensitive option, it is strongly recommended that you also enable the “ Move scripts to the footer” option.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_css_exceptions' => [
										'type'        => 'textarea',
										'name'        => 'css_exceptions',
										'label'       => __( 'Exclude styles from being inlined and/or minified option: ', 'speed-booster-pack' ),
										'description' => __( 'Enter one by line, the handles of CSS files or the final part of the style URL.', 'speed-booster-pack' ),
									],
								],
							],
							[
								'type'  => 'section',
								'label' => __( 'PreBoost: Asset Preloader', 'speed-booster-pack' ),
								'items' => [
									'sbp_enable_preboost' => [
										'type'          => 'checkbox',
										'label'         => __( 'Preload assets', 'speed-booster-pack' ),
										'tooltip'       => __( 'Generates &lt;link rel=\'preload\'&gt; tags', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_preboost'        => [
										'type'        => 'textarea',
										'name'        => 'sbp_preboost',
										'label'       => __( 'Preload', 'speed-booster-pack' ),
										'description' => __( 'Enter full URLs of the assets you want to preload. One URL per line.', 'speed-booster-pack' ),
									],
								],
							]
						],
					],
					'cdn-options'      => [
						'sections' => [
							[
								'type'        => 'section',
								'label'       => __( 'CDN', 'speed-booster-pack' ),
								'description' => __( 'CDN options that allow you to rewrite your site URLs with your CDN URLs.', 'speed-booster-pack' ),
								'items'       => [
									'sbp_enable_cdn'               => [
										'type'          => 'checkbox',
										'label'         => __( 'Enable CDN Rewrite', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enables rewriting of your site URLs with your CDN URLs', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_cdn_url'                  => [
										'type'          => 'text',
										'label'         => __( 'CDN URL', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enter your CDN URL without the trailing slash. Example: https://cdn.example.com', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_cdn_included_directories' => [
										'type'          => 'text',
										'label'         => __( 'Included Directories', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enter any directories you would like to be included in CDN rewriting, separated by commas (,). Default: wp-content,wp-includes', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
										'default'       => 'wp-content,wp-includes',
									],
									'sbp_cdn_exclusions'           => [
										'type'          => 'text',
										'label'         => __( 'CDN Exclusions', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enter any directories or file extensions you would like to be excluded from CDN rewriting, separated by commas (,). Default: .php', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
										'default'       => '.php',
									],
								],
							],
						],
					],
					'google-analytics' => [
						'sections' => [
							[
								'type'        => 'section',
								'label'       => __( 'Google Analytics', 'speed-booster-pack' ),
								'description' => __( 'Optimization options for Google Analytics.', 'speed-booster-pack' ),
								'items'       => [
									'local_analytics_warning'      => [
										'type' => 'warning',
										'text' => __( 'Enabling the Local Analytics option doesn\'t remove other Google Analytics scripts added via other plugins or theme options.',
											'speed-booster-pack' ),
										'icon' => 'dashicons-warning',
									],
									'minimal_analytics_warning'    => [
										'type'  => 'warning',
										'text'  => __( 'Minimal Analytics (by David Künnen) is an experimental script which doesn\'t use the regular Google Analytics JS file, but is much lighter (1.5KB). Use with caution!',
											'speed-booster-pack' ),
										'icon'  => 'dashicons-warning',
										'class' => 'minimal-analytics-warning',
									],
									'sbp_enable_local_analytics'   => [
										'type'          => 'checkbox',
										'label'         => __( 'Enable Local Analytics', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enable syncing of the Google Analytics script to your own server.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_ga_tracking_id'           => [
										'type'          => 'text',
										'label'         => __( 'Tracking ID', 'speed-booster-pack' ),
										'tooltip'       => __( 'Enter your Google Analytics tracking ID', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
										'placeholder'   => 'GTM-XXXXX or UA-XXXXX-X',
									],
									'sbp_ga_tracking_script'       => [
										'type'          => 'radio',
										'label'         => __( 'Select Script', 'speed-booster-pack' ),
										'tooltip'       => __( 'Select which Google Analytics script you want to use.',
											'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
										'options'       => [
											'gtm'       => 'Google Tag Manager',
											'analytics' => 'Google Analytics',
											'gtag'      => 'Global Site Tag',
											'minimal'   => 'Minimal Analytics',
										],
									],
									'sbp_tracking_position'        => [
										'type'          => 'select',
										'label'         => __( 'Tracking code position', 'speed-booster-pack' ),
										'tooltip'       => __( 'Load the script in the header or footer of the site.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
										'options'       => [
											'footer' => 'Footer (Default)',
											'header' => 'Header',
										],
									],
									'sbp_disable_display_features' => [
										'type'          => 'checkbox',
										'label'         => __( 'Disable Display Features', 'speed-booster-pack' ),
										'tooltip'       => __( 'Disable marketing and advertising which generates a 2nd HTTP request', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_anonymize_ip'             => [
										'type'          => 'checkbox',
										'label'         => __( 'Anonymize IP', 'speed-booster-pack' ),
										'tooltip'       => __( 'Shorten visitor IP to comply with privacy restrictions in some countries.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_track_loggedin_admins'    => [
										'type'          => 'checkbox',
										'label'         => __( 'Track Admins', 'speed-booster-pack' ),
										'tooltip'       => __( 'Include logged in WordPress admins in your GA report.', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_bounce_rate'              => [
										'type'          => 'text',
										'label'         => __( 'Adjust Bounce Rate', 'speed-booster-pack' ),
										'tooltip'       => __( 'Set a timeout limit in seconds to better evaluate the quality of your traffic (1 - 100)', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
									'sbp_monsterinsights'          => [
										'type'          => 'checkbox',
										'label'         => __( 'MonsterInsights Integration', 'speed-booster-pack' ),
										'tooltip'       => __( 'Allows MonsterInsights to manage your Google Analytics while still using the locally hosted analytics.js generated by Speed Booster Pack', 'speed-booster-pack' ),
										'options_group' => 'sbp_settings',
									],
								],
							],
						],
					],
				];

				$sbp_alert_box                      = '<div class="sbp-static-notice bg-red"><span class="dashicons dashicons-warning"></span> ' . __( 'Activating these settings might cause conflicts with other plugins or your theme, resulting in broken styles or scripts. Use with caution!', 'speed-booster-pack' ) . '</div>';
				$sbp_deprecated_js_footer_alert_box = '<div class="sbp-static-notice bg-yellow"><span class="dashicons dashicons-warning"></span> ' . __( 'We recoded the feature that moves all inline scripts and JavaScript files to footer. The old code is deprecated now and will be removed in the new versions. Right now, you\'re using the old feature and it\'s still working, but you need to migrate to the new feature as soon as possible. And make sure that you jot down the current JS optimization exclusion settings!', 'speed-booster-pack' ) . '<br /><br /><button type="submit" form="migrate_js_mover_form" class="button button-primary">' . __( 'Migrate to the new feature now!', 'speed-booster-pack' ) . '</button></div>';

				?>

				<?php

				//Start the tabs
				foreach ( $sbp_options_array as $k => $values ) { ?>
                    <!--  Tab sections  -->
                    <div id="<?php echo $k; ?>" class="sb-pack-tab">

						<?php

						if ( $k == 'advanced-options' || $k == 'google-analytics' || $k == 'cdn-options' ) {
							echo $sbp_alert_box;
						}

						if ( $k == 'advanced-options' ) {
							if ( ! $is_using_new_version ) {
								echo $sbp_deprecated_js_footer_alert_box;
							}
						}

						?>

						<?php if ( isset( $values['label'] ) ) { ?>
                            <h3><?php echo $values['label']; ?></h3>
							<?php
						}


						//Start the sections
						foreach ( $values['sections'] as $section => $section_value ) {

							?>
                            <div class="postbox" id="<?php echo $k . "-" . $section; ?>">
                                <h3 class="hndle ui-sortable-handle"
                                    style="cursor: pointer;"><?php echo ( isset( $section_value['label'] ) ) ? $section_value['label'] : ""; ?></h3>
                                <div class="inside">
									<?php

									//Start the options
									foreach ( $section_value['items'] as $item => $item_value ) {

										if ( 'checkbox' == $item_value['type'] ) { ?>
                                            <div class="onoffswitch-wrapper">
												<?php if ( isset( $item_value['tooltip'] ) ) { ?>
                                                    <span class="tooltip-right"
                                                          data-tooltip="<?php echo $item_value['tooltip']; ?>">
                                            <i class="dashicons dashicons-editor-help"></i>
                                        </span>
												<?php } ?>
                                                <span class="chekbox-title"><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></span>

                                                <div class="onoffswitch">
                                                    <div class="epsilon-toggle">
                                                        <input class="epsilon-toggle__input" type="checkbox" id="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" value="1" <?php checked( 1, isset( $sbp_options[ $item ] ) ); ?> >
                                                        <div class="epsilon-toggle__items">
                                                            <span class="epsilon-toggle__track"></span>
                                                            <span class="epsilon-toggle__thumb"></span>
                                                            <svg class="epsilon-toggle__off" width="6" height="6" aria-hidden="true" role="img" focusable="false" viewBox="0 0 6 6">
                                                                <path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
                                                            </svg>
                                                            <svg class="epsilon-toggle__on" width="2" height="6" aria-hidden="true" role="img" focusable="false" viewBox="0 0 2 6">
                                                                <path d="M0 0h2v6H0z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <label for="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>"></label>
                                                </div>
                                            </div>
										<?php }
										if ( 'select' == $item_value['type'] ) { ?>
                                            <p>
												<?php if ( isset( $item_value['tooltip'] ) ) { ?>
                                                    <span class="tooltip-right" data-tooltip="<?php echo $item_value['tooltip']; ?>"><i class="dashicons dashicons-editor-help"></i></span>
												<?php } ?>
                                                <label for="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" class="<?php echo ( isset( $item_value['label'] ) ) ? 'label-text' : ''; ?>"><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></label>
                                                <select id="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>">
													<?php
													foreach ( $item_value['options'] as $option_k => $op_v ) {
														?>
                                                        <option value="<?php echo $option_k; ?>" <?php selected(
															$option_k,
															$sbp_options[ $item ],
															true
														); ?> ><?php echo $op_v; ?></option>
														<?php
													}
													?>
                                                </select>
                                            </p>
										<?php }

										if ( 'radio' == $item_value['type'] ) { ?>
                                            <p>
                                            <div style="width: min-width: 350px; float: left; display: inline-block;">
												<?php if ( isset( $item_value['tooltip'] ) ) { ?>
                                                    <span class="tooltip-right"
                                                          data-tooltip="<?php echo $item_value['tooltip']; ?>">
                                            <i class="dashicons dashicons-editor-help"></i>
                                        </span>
												<?php } ?>
                                                <label for="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>"
                                                       class="<?php echo ( isset( $item_value['label'] ) ) ? 'label-text' : ''; ?>"><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></label>
                                            </div>
                                            <div style="display: inline-block; float: left; padding-left: 5px;">
												<?php
												foreach ( $item_value['options'] as $option_k => $op_v ) {
													?>
                                                    <label><input type="radio" value="<?php echo $option_k; ?>" class="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" <?php checked( $option_k, @$sbp_options[ $item ], true ); ?>> <?php echo @$op_v; ?>
                                                    </label><br/>
													<?php
												}
												?>
                                            </div>
                                            <div style="clear: both;"></div>
                                            </p>
										<?php }

										if ( 'link' == $item_value['type'] ) { ?>
                                            <p>
                                                <a href="<?php echo $item_value['url']; ?>" class="button <?php echo $item_value['class']; ?>"><?php echo $item_value['text']; ?></a>
                                            </p>
										<?php }

										if ( 'text' == $item_value['type'] ) { ?>
                                            <p>
												<?php
												$default_value = ( isset( $item_value['default'] ) ) ? $item_value['default'] : "";
												if ( isset( $item_value['options_group'] ) ) {
													$op_text = ( isset( $sbp_options[ $item ] ) && "" != $sbp_options[ $item ] ) ? $sbp_options[ $item ] : $default_value;
												} else {
													$op_text = ( get_option( $item ) ) ? get_option( $item ) : $default_value;
												}

												?>
												<?php if ( isset( $item_value['tooltip'] ) ) { ?>
                                                    <span class="tooltip-right" data-tooltip="<?php echo $item_value['tooltip']; ?>"><i class="dashicons dashicons-editor-help"></i></span>
												<?php } ?>
                                                <label for="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" class="<?php echo ( isset( $item_value['label'] ) ) ? 'label-text' : ''; ?>"><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></label>

                                                <input id="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" type="text" value="<?php echo esc_attr( $op_text ); ?>"/>
                                            </p>
										<?php }

										if ( 'textarea' === $item_value['type'] && 'lazyload_exclusions' === $item_value['name'] ) { ?>
                                            <h4><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></h4>
                                            <p>
                                                <textarea cols="50" rows="3" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" id="<?php echo $item; ?>"><?php echo wp_kses_post( $lazyload_exclusions ); ?></textarea>
                                            </p>
                                            <p class="description">
												<?php echo isset( $item_value['description'] ) ? $item_value['description'] : ''; ?>
                                            </p>
										<?php }

										if ( 'textarea' === $item_value['type'] && 'css_exceptions' === $item_value['name'] ) { ?>
                                            <h4><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></h4>
                                            <p>
                                                <textarea cols="50" rows="3" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" id="<?php echo $item; ?>"><?php echo wp_kses_post( $css_exceptions ); ?></textarea>
                                            </p>
                                            <p class="description">
												<?php echo isset( $item_value['description'] ) ? $item_value['description'] : ''; ?>
                                            </p>
										<?php }

										if ( 'textarea' === $item_value['type'] && 'sbp_preboost' === $item_value['name'] ) { ?>
                                            <h4><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></h4>
                                            <p>
                                                <textarea cols="50" rows="3" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" id="<?php echo $item; ?>"><?php echo wp_kses_post( $sbp_preboost ); ?></textarea>
                                            </p>
                                            <p class="description">
												<?php echo isset( $item_value['description'] ) ? $item_value['description'] : ''; ?>
                                            </p>
										<?php }

										if ( 'textarea' === $item_value['type'] && 'sbp_js_footer_exceptions' === $item_value['name'] ) { ?>
                                            <h4><?php echo ( isset( $item_value['label'] ) ) ? $item_value['label'] : ''; ?></h4>
                                            <p>
                                                <textarea cols="50" rows="3" name="<?php echo ( isset( $item_value['options_group'] ) ) ? $item_value['options_group'] . '[' . $item . ']' : $item; ?>" id="<?php echo $item; ?>"><?php echo wp_kses_post( $js_footer_exceptions ); ?></textarea>
                                            </p>
                                            <p class="description">
												<?php echo isset( $item_value['description'] ) ? $item_value['description'] : ''; ?>
                                            </p>
										<?php }

										if ( 'warning' == $item_value['type'] ) {
											?>
                                            <div class="sbp-warning <?php echo isset( $item_value['background'] ) ? $item_value['background'] : 'bg-red' ?> <?php echo isset( $item_value['class'] ) ? $item_value['class'] : null; ?>">
												<?php if ( isset( $item_value['icon'] ) ): ?>
                                                    <span class="dashicons <?php echo $item_value['icon'] ?>"></span>
												<?php endif; ?>
												<?php echo $item_value['text']; ?>
                                            </div>
											<?php
										}
									}
									?>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div> <!-- Tab sections  -->
				<?php } ?>

                <div id="optimize-more" class="sb-pack-tab">
                    <div class="feature-box postbox">
                        <div class="inside clearfix">
                            <img class="feature-box-left feature-box-image" src="<?php echo SPEED_BOOSTER_PACK_URL ?>inc/images/optimocha.png" alt="Optimocha"/>
                            <div class="feature-box-right">
                                <h2 class="feature-box-title"><?php _e( 'Speed up your website with Optimocha', 'speed-booster-pack' ) ?></h2>
                                <p class="feature-box-description"><?php _e( 'Optimocha is a tailored speed optimization service where you can get your website optimized by a speed optimization expert. With a one-time "investment", your website will be taken care of real people. <strong>A significant speed improvement is guaranteed</strong>, so can be sure that your investment will return to you with a faster website!', 'speed-booster-pack' ); ?></p>
                                <p class="feature-box-description"><?php _e( 'Or, if you\'d like to have someone maintain your website speed, keep everything up-to-date and ensure your website is secure all the time; you can purchase Optimocha\'s monthly optimization &amp; maintenance packages. <strong>Annual payments have more and more benefits</strong>, be sure to check them out!', 'speed-booster-pack' ); ?></p>
                                <p class="feature-box-button">
                                    <a href="https://optimocha.com/?ref=sbp" target="_blank" class="button button-primary button-large"><?php _e( "Speed Up Your Website!", 'speed-booster-pack' ); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="feature-box postbox">
                        <div class="inside clearfix">
                            <img class="feature-box-left feature-box-image" src="<?php echo SPEED_BOOSTER_PACK_URL ?>inc/images/shortpixel.png" alt="ShortPixel"/>
                            <div class="feature-box-right">
                                <h2 class="feature-box-title"><?php _e( 'Optimize your images with ShortPixel', 'speed-booster-pack' ) ?></h2>
                                <p class="feature-box-description"><?php _e( 'Image optimization is essential for all websites - especially for sites with lots of images! ShortPixel Image Optimizer is probably the best image optimization plugin you can use in the WordPress ecosystem. By clicking the link below, <strong>you will get 50% more image optimization credits for the same price</strong>! The image optimization credits are good for unlimited websites and do not expire, so you can use your API key in all your websites, anytime you want.', 'speed-booster-pack' ); ?></p>
                                <p class="feature-box-button">
                                    <a href="https://optimocha.com/go/shortpixel" target="_blank" class="button button-primary button-large"><?php _e( "Optimize Your Images!", 'speed-booster-pack' ); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="feature-box postbox">
                        <div class="inside clearfix">
                            <img class="feature-box-left feature-box-image" src="<?php echo SPEED_BOOSTER_PACK_URL ?>inc/images/wp-engine.png" alt="WP Engine"/>
                            <div class="feature-box-right">
                                <h2 class="feature-box-title"><?php _e( 'Get better hosting at WP Engine', 'speed-booster-pack' ) ?></h2>
                                <p class="feature-box-description"><?php _e( 'Choosing a good web hosting company make such a big difference, but often overlooked. It\'s understandable that people like to try cheap hosting packages in very large hosting companies, but if you\'re looking for a WordPress-centric hosting company with servers specially optimized for WordPress websites, be sure to check out WP Engine. Clicking the link below, <strong>you can get up to three months for free on annual payments!</strong>', 'speed-booster-pack' ); ?></p>
                                <p class="feature-box-button">
                                    <a href="https://optimocha.com/go/wpengine" target="_blank" class="button button-primary button-large"><?php _e( "Get Better Hosting!", 'speed-booster-pack' ); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                </div><!--#optimize-more-->

                <div>
					<?php submit_button( __( 'Save Changes', 'speed-booster-pack' ), 'primary large', 'submit', false ); ?>
                </div>

            </form>

        </div><!--/.col-main-->

        <div class="col-side">

            <div class="postbox">
                <h3 class="hndle"><?php _e( 'Invest in More Speed!', 'speed-booster-pack' ); ?></h3>
                <div class="inside">
                    <p><?php _e( 'People abandon pages that take more than a few seconds to load, which means slow pages lose you visitors (and money). You don’t want that to happen, do you?', 'speed-booster-pack' ) ?></p>
                    <p><?php _e( 'If you’re ready to <em>invest</em> in speeding up your website, click below for our professional, tailored speed optimization services!', 'speed-booster-pack' ); ?></a></p>
                    <p style="text-align:center">
                        <a href="https://optimocha.com/?ref=sbp" target="_blank" class="button button-primary button-large"><?php _e( "Speed Up Your Website!", 'speed-booster-pack' ); ?></a>
                    </p>
                </div>
            </div>

            <div class="postbox">
                <h3 class="hndle"><?php _e( 'Test Your Website', 'speed-booster-pack' ); ?></h3>
                <div class="inside">
                    <p><?php _e( 'It\'s always a good idea to keep testing your website so you can track your website\'s speed. Click the buttons below to see how well your website performs in various speed test tools!', 'speed-booster-pack' ) ?></p>
                    <p style="text-align:center">
                        <a href="https://gtmetrix.com/?url=<?php echo home_url( '/' ); ?>" target="_blank" class="button button-secondary"><?php _e( "Test on GTmetrix", 'speed-booster-pack' ); ?></a>&nbsp;<a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo home_url( '/' ); ?>" target="_blank" class="button button-secondary"><?php _e( "Test on Google PageSpeed", 'speed-booster-pack' ); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div><!--/.sb-pack-->
</div> <!-- end wrap div -->