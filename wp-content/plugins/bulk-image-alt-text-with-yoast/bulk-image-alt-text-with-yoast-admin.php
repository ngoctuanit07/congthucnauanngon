<?php

require __DIR__ . '/vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';
add_action( 'admin_init', array( 'PAnD', 'init' ) );
class bialty_settings
{
    function __construct()
    {
        // stuff to do when the plugin is loaded
        add_action( 'admin_menu', array( &$this, 'bialty_admin_menu' ) );
        function bialty_styles()
        {
            wp_register_style(
                'bialty_admin-styles',
                plugin_dir_url( __FILE__ ) . 'assets/bialty-styles-admin.css',
                array(),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/bialty-styles-admin.css' )
            );
            wp_enqueue_style( 'bialty_admin-styles' );
            wp_register_script(
                'bialty_admin-script',
                plugin_dir_url( __FILE__ ) . 'assets/bialty-script-admin.js',
                array(),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/bialty-script-admin.js' )
            );
            wp_enqueue_script( 'bialty_admin-script' );
        }
        
        add_action( 'admin_enqueue_scripts', 'bialty_styles' );
    }
    
    function bialty_admin_menu()
    {
        add_options_page(
            'Bulk Image Alt Text Settings',
            'Bulk Image Alt Text',
            'manage_options',
            'bialty',
            array( &$this, 'bialty_settings_page' )
        );
    }
    
    // end function
    function bialty_settings_page()
    {
        global  $bialty ;
        $bialty_options = $bialty->bialty_options();
        //set active class for navigation tabs
        if ( isset( $_GET['tab'] ) ) {
            $active_tab = $_GET['tab'];
        }
        // end if
        $active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'bialty-settings' );
        // end active class
        // purchase notification
        $purchase_url = "options-general.php?page=bialty-pricing";
        $get_pro = sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable', 'bulk-image-alt-text-with-yoast' ), array(
            'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
        ) ), esc_url( $purchase_url ) );
        
        if ( isset( $_POST['update'] ) ) {
            // check if user is authorised
            if ( function_exists( 'current_user_can' ) && !current_user_can( 'manage_options' ) ) {
                die( 'Sorry, not allowed...' );
            }
            check_admin_referer( 'bialty_settings' );
            $alt_safe = array(
                "alt_empty_fkw",
                "alt_empty_title",
                "alt_empty_imagename",
                "alt_empty_both",
                "alt_empty_disable",
                "alt_not_empty_fkw",
                "alt_not_empty_title",
                "alt_not_empty_imagename",
                "alt_not_empty_both",
                "alt_not_empty_disable",
                "woo_alt_empty_fkw",
                "woo_alt_empty_title",
                "woo_alt_empty_imagename",
                "woo_alt_empty_both",
                "woo_alt_empty_disable",
                "woo_alt_not_empty_fkw",
                "woo_alt_not_empty_title",
                "woo_alt_not_empty_imagename",
                "woo_alt_not_empty_both",
                "woo_alt_not_empty_disable",
                "custom_alt_text_yes",
                "custom_alt_text_no",
                "bialty-vidseo",
                "boost-robot",
                "bialty-mobilook"
            );
            // if alt empty
            $alt_empty = sanitize_text_field( $_POST['alt_empty'] );
            $bialty_options['alt_empty'] = ( isset( $_POST['alt_empty'] ) && in_array( $alt_empty, $alt_safe ) ? $alt_empty : false );
            // if alt not empty
            $alt_not_empty = sanitize_text_field( $_POST['alt_not_empty'] );
            $bialty_options['alt_not_empty'] = ( isset( $_POST['alt_not_empty'] ) && in_array( $alt_not_empty, $alt_safe ) ? $alt_not_empty : false );
            ( isset( $_POST['add_site_title'] ) ? $bialty_options['add_site_title'] = $_POST['add_site_title'] : ($bialty_options['add_site_title'] = false) );
            ( isset( $_POST['remove_settings'] ) ? $bialty_options['remove_settings'] = true : ($bialty_options['remove_settings'] = false) );
            // remove settings on plugin deactivation
            // install robots.txt notice
            if ( isset( $_POST['boost-robot'] ) ) {
                $bialty_robots = sanitize_text_field( $_POST['boost-robot'] );
            }
            $bialty_options['boost-robot'] = ( isset( $_POST['boost-robot'] ) && in_array( $bialty_robots, $alt_safe ) ? $bialty_robots : false );
            // install mobilook notice
            if ( isset( $_POST['bialty-mobilook'] ) ) {
                $bialty_mobilook = sanitize_text_field( $_POST['bialty-mobilook'] );
            }
            $bialty_options['bialty-mobilook'] = ( isset( $_POST['bialty-mobilook'] ) && in_array( $bialty_mobilook, $alt_safe ) ? $bialty_mobilook : false );
            // install vidseo notice
            if ( isset( $_POST['bialty-vidseo'] ) ) {
                $bialty_vidseo = sanitize_text_field( $_POST['bialty-vidseo'] );
            }
            $bialty_options['bialty-vidseo'] = ( isset( $_POST['bialty-vidseo'] ) && in_array( $bialty_vidseo, $alt_safe ) ? $bialty_vidseo : false );
            update_option( 'bialty', $bialty_options );
            // update options
            echo  '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Settings saved.', 'bulk-image-alt-text-with-yoast' ) . '</strong></p></div>' ;
            $progress_bar = '<div class="meter animate"><span style="width: 100%"><span>All Done</span></span></div>';
        }
        
        ?>

    <div class="wrap bialty-containter">

        <h2><span class="dashicons dashicons-media-text" style="margin-top: 6px; font-size: 24px;"></span> Bulk Image Alt Texts
            <?php 
        echo  esc_html__( 'Settings', 'bulk-image-alt-text-with-yoast' ) ;
        ?>
        </h2>

        <h2 class="nav-tab-wrapper">
            <a href="<?php 
        echo  esc_url( '?page=bialty&tab=bialty-settings' ) ;
        ?>" class="nav-tab <?php 
        echo  ( $active_tab == 'bialty-settings' ? 'nav-tab-active' : '' ) ;
        ?>">Settings</a>
            <a href="<?php 
        echo  esc_url( '?page=bialty&tab=bialty-faq' ) ;
        ?>" class="nav-tab <?php 
        echo  ( $active_tab == 'bialty-faq' ? 'nav-tab-active' : '' ) ;
        ?>">FAQ</a>
            <a href="?page=bialty&tab=bialty-recs" class="nav-tab <?php 
        echo  ( $active_tab == 'bialty-recs' ? 'nav-tab-active' : '' ) ;
        ?>">Recommendations</a>
        </h2>

        <?php 
        
        if ( $active_tab == 'bialty-settings' ) {
            ?>

        <!-- start main settings column -->
        <div class="bialty-row">
            <div class="bialty-column col-9">
                <div class="bialty-main">
                    <form method="post">

                        <?php 
            if ( function_exists( 'wp_nonce_field' ) ) {
                wp_nonce_field( 'bialty_settings' );
            }
            ?>
                        
                        <br />
                        
                        <?php 
            
            if ( !class_exists( 'WPSEO_Meta' ) && !class_exists( 'RankMath' ) ) {
                ?>
                            <div class="bialty-alert bialty-warning"><span class="closebtn">&times;</span><?php 
                echo  esc_html__( 'Yoast SEO / Rank Math SEO is either not installed or disabled. "Focus Keyword" won\'t work (of course). "Both..." feature will add only Post titles to Alt tags', 'bulk-image-alt-text-with-yoast' ) ;
                ?></div>
                        <?php 
            }
            
            if ( isset( $progress_bar ) ) {
                echo  $progress_bar ;
            }
            ?>
                        
                        <div class="bialty-note">
                            <h3><?php 
            echo  esc_html__( 'BIALTY, how does it work?', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h3>
                            <p><?php 
            echo  esc_html__( '1. Select what to do with missing alt tags', 'bulk-image-alt-text-with-yoast' ) ;
            ?><br>
                            <?php 
            echo  esc_html__( '2. Select what to do with existing alt tags', 'bulk-image-alt-text-with-yoast' ) ;
            ?><br>
                            <?php 
            echo  esc_html__( '3. Click on "save changes" and your settings will be applied everywhere.', 'bulk-image-alt-text-with-yoast' ) ;
            ?><br>
                            <?php 
            echo  esc_html__( "4. Bialty will be active for all future publications which means that you won't have to worry anymore about Alt texts.", "bialty" ) ;
            ?></p>
                        </div>

                        <h2><?php 
            echo  esc_html__( 'About Page and Post Alt texts', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h2>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Replace Missing Alt Tags with', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                <div class="bialty-switch-radio">
                                    <input type="radio" id="alt_empty_btn1" name="alt_empty" value="alt_empty_fkw" <?php 
            if ( !class_exists( 'WPSEO_Meta' ) && !class_exists( 'RankMath' ) ) {
                echo  'disabled' ;
            }
            if ( isset( $bialty_options['alt_empty'] ) && !empty($bialty_options['alt_empty']) ) {
                checked( 'alt_empty_fkw' == $bialty_options['alt_empty'] );
            }
            ?> />
                                    <label for="alt_empty_btn1" <?php 
            if ( !class_exists( 'WPSEO_Meta' ) && !class_exists( 'RankMath' ) ) {
                echo  'class="bialty-disabled" ' ;
            }
            ?>><?php 
            echo  esc_html__( 'Yoast / Rank Math Focus Keyword', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="alt_empty_btn2" name="alt_empty" value="alt_empty_title" <?php 
            if ( isset( $bialty_options['alt_empty'] ) && !empty($bialty_options['alt_empty']) ) {
                checked( 'alt_empty_title' == $bialty_options['alt_empty'] );
            }
            ?> />
                                    <label for="alt_empty_btn2"><?php 
            echo  esc_html__( 'Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="alt_empty_btn3" name="alt_empty" value="alt_empty_imagename" <?php 
            echo  'disabled' ;
            ?> />
                                    <label for="alt_empty_btn3" <?php 
            echo  'class="bialty-disabled" ' ;
            ?>><?php 
            echo  esc_html__( 'Image Name', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="alt_empty_btn4" name="alt_empty" value="alt_empty_both" <?php 
            if ( empty($bialty_options['alt_empty']) || $bialty_options['alt_empty'] == "alt_empty_both" ) {
                checked( 'alt_empty_both' == $bialty_options['alt_empty'] );
            }
            ?> />
                                    <label for="alt_empty_btn4"><?php 
            echo  esc_html__( 'Focus Keyword & Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="alt_empty_btn5" name="alt_empty" value="alt_empty_disable" <?php 
            if ( isset( $bialty_options['alt_empty'] ) && !empty($bialty_options['alt_empty']) ) {
                checked( 'alt_empty_disable' == $bialty_options['alt_empty'] );
            }
            ?> />
                                    <label for="alt_empty_btn5"><?php 
            echo  esc_html__( 'Disable', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                </div>
                            </div>

                        </div>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Replace Defined Alt Tags with', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                
                                <div class="bialty-switch-radio">
                                    <input type="radio" id="alt_not_empty_btn1" name="alt_not_empty" value="alt_not_empty_fkw" <?php 
            if ( !class_exists( 'WPSEO_Meta' ) && !class_exists( 'RankMath' ) ) {
                echo  'disabled' ;
            }
            if ( isset( $bialty_options['alt_not_empty'] ) && !empty($bialty_options['alt_not_empty']) ) {
                checked( 'alt_not_empty_fkw' == $bialty_options['alt_not_empty'] );
            }
            ?> />
                                    <label for="alt_not_empty_btn1" <?php 
            if ( !class_exists( 'WPSEO_Meta' ) && !class_exists( 'RankMath' ) ) {
                echo  'class="bialty-disabled" ' ;
            }
            ?>><?php 
            echo  esc_html__( 'Yoast / Rank Math Focus Keyword', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="alt_not_empty_btn2" name="alt_not_empty" value="alt_not_empty_title" <?php 
            if ( isset( $bialty_options['alt_not_empty'] ) && !empty($bialty_options['alt_not_empty']) ) {
                checked( 'alt_not_empty_title' == $bialty_options['alt_not_empty'] );
            }
            ?> />
                                    <label for="alt_not_empty_btn2"><?php 
            echo  esc_html__( 'Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="alt_not_empty_btn3" name="alt_not_empty" value="alt_not_empty_imagename" <?php 
            echo  'disabled' ;
            ?> />
                                    <label for="alt_not_empty_btn3" <?php 
            echo  'class="bialty-disabled" ' ;
            ?>><?php 
            echo  esc_html__( 'Image Name', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="alt_not_empty_btn4" name="alt_not_empty" value="alt_not_empty_both" <?php 
            if ( empty($bialty_options['alt_not_empty']) || $bialty_options['alt_not_empty'] == "alt_not_empty_both" ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                    <label for="alt_not_empty_btn4"><?php 
            echo  esc_html__( 'Focus Keyword & Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="alt_not_empty_btn5" name="alt_not_empty" value="alt_not_empty_disable" <?php 
            if ( isset( $bialty_options['alt_not_empty'] ) && !empty($bialty_options['alt_not_empty']) ) {
                checked( 'alt_not_empty_disable' == $bialty_options['alt_not_empty'] );
            }
            ?> />
                                    <label for="alt_not_empty_btn5"><?php 
            echo  esc_html__( 'Disable', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                </div>

                                <?php 
            ?>

                                <div class="bialty-alert bialty-info" style="display: block;">
                                    <span class="closebtn">&times;</span>
                                    <?php 
            echo  $get_pro . " " . esc_html__( 'Image Name feature', 'bulk-image-alt-text-with-yoast' ) ;
            ?>
                                </div>

                                <?php 
            ?>
                                
                            </div>

                        </div>
                        
                        <h2><?php 
            echo  esc_html__( 'About Product Alt texts (for Woocommerce)', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h2>
                        
                        <?php 
            ?>
                        
                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Replace Missing Alt Tags with', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                <div class="bialty-switch-radio">
                                    <input type="radio" id="woo_alt_empty_btn1" name="woo_alt_empty" value="woo_alt_empty_fkw" disabled />
                                    <label for="woo_alt_empty_btn1" class="bialty-disabled"><?php 
            echo  esc_html__( 'Yoast / Rank Math Focus Keyword', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="woo_alt_empty_btn2" name="woo_alt_empty" value="woo_alt_empty_title" disabled />
                                    <label for="woo_alt_empty_btn2" class="bialty-disabled"><?php 
            echo  esc_html__( 'Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="woo_alt_empty_btn3" name="woo_alt_empty" value="woo_alt_empty_title" disabled />
                                    <label for="woo_alt_empty_btn3" class="bialty-disabled"><?php 
            echo  esc_html__( 'Image Name', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="woo_alt_empty_btn4" name="woo_alt_empty" value="woo_alt_empty_both" disabled />
                                    <label for="woo_alt_empty_btn4" class="bialty-disabled"><?php 
            echo  esc_html__( 'Focus Keyword & Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="woo_alt_empty_btn5" name="woo_alt_empty" value="woo_alt_empty_disable" disabled />
                                    <label for="woo_alt_empty_btn5" class="bialty-disabled"><?php 
            echo  esc_html__( 'Disable', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                </div>
                            </div>

                        </div>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Replace Defined Alt Tags with', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                <div class="bialty-switch-radio">
                                    <input type="radio" id="woo_alt_empty_btn1" name="" value="" disabled />
                                    <label for="woo_alt_empty_btn1" class="bialty-disabled"><?php 
            echo  esc_html__( 'Yoast / Rank Math Focus Keyword', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="woo_alt_empty_btn2" name="" value="" disabled />
                                    <label for="woo_alt_empty_btn2" class="bialty-disabled"><?php 
            echo  esc_html__( 'Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>

                                    <input type="radio" id="woo_alt_empty_btn3" name="" value="" disabled />
                                    <label for="woo_alt_empty_btn3" class="bialty-disabled"><?php 
            echo  esc_html__( 'Image Name', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="woo_alt_empty_btn4" name="" value="" disabled />
                                    <label for="woo_alt_empty_btn4" class="bialty-disabled"><?php 
            echo  esc_html__( 'Focus Keyword & Post Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                    
                                    <input type="radio" id="woo_alt_empty_btn5" name="" value="" disabled />
                                    <label for="woo_alt_empty_btn5" class="bialty-disabled"><?php 
            echo  esc_html__( 'Disable', 'bulk-image-alt-text-with-yoast' ) ;
            ?></label>
                                </div>
                                
                                <div class="bialty-alert bialty-info" style="display: block;">
                                    <span class="closebtn">&times;</span>
                                    <?php 
            echo  $get_pro . " " . esc_html__( 'Bulk Image Alt Text for Woocommerce Products', 'bulk-image-alt-text-with-yoast' ) ;
            ?>
                                </div>
                                
                            </div>

                        </div>
                        
                        <?php 
            //end free
            ?>

                        <?php 
            //echo "<div display='none'><script type='text/javascript'>console.log('Output: ".$bialty_options['woo_alt_not_empty']."')</script></div>";
            ?>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Add Site Title', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                <label class="bialty-switch">
                                    <input type="checkbox" id="add_site_title" name="add_site_title"
                                    <?php 
            if ( isset( $bialty_options['add_site_title'] ) && !empty($bialty_options['add_site_title']) ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                    <span class="bialty-slider bialty-round"></span>
                                </label>
                                &nbsp;
                                <span><?php 
            echo  esc_html__( 'Add website title defined in Settings &raquo; General to alt text as well', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                        </div>

                        <div class="bialty-note" style="margin: 10px 0;">
                            <h3><?php 
            echo  esc_html__( 'Warning:', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h3>
                            <p>●&nbsp; <?php 
            echo  __( 'Alt texts added/created by BIALTY plugin ARE NOT added to MEDIA LIBRARY (which is useless as not visible by search engines). All image Alt text are added into HTML code, in frontend . Please follow instructions below, at "How to check Alt Text now?", to see your settings applied.', 'bulk-image-alt-text-with-yoast' ) ;
            ?></p>

                            <p>●&nbsp; <?php 
            echo  sprintf( wp_kses( __( 'Alt texts (Alt attributes, Alt tags) are NOT Title attributes (title tags). Please use <a href="%s" target="_blank">BIGTA plugin</a> (by Pagup) if you need to add title tags to your images.', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( 'https://wordpress.org/plugins/bulk-image-title-attribute/' ) ) ;
            ?></p>
                        </div>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  esc_html__( 'Delete Settings', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                            <div class="bialty-column col-8">
                                <label class="bialty-switch">
					                <input type="checkbox" id="remove_settings" name="remove_settings"
					                <?php 
            if ( $bialty_options['remove_settings'] ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                    <span class="bialty-slider bialty-round"></span>
				                </label>
                                &nbsp;
                                <span><?php 
            echo  esc_html__( 'Checking this box will remove all settings when you deactivate plugin.', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>

                        </div>

                        <div class="bialty-note" style="margin: 10px 0;">
                            <h3><?php 
            echo  esc_html__( 'Custom ALT Text?', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h3>
                            <p><?php 
            echo  sprintf( wp_kses( __( 'Please use <a href="%s" target="_blank">BIALTY META BOX</a> to either disable plugin locally or add Alt text other than Post titles or Yoast Keyword.', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( plugin_dir_url( __FILE__ ) . 'assets/imgs/meta-box.png' ) ) ;
            ?></p>
                        </div>

                        <div class="bialty-alert bialty-info" style="margin-top: 10px;"><?php 
            echo  sprintf( wp_kses( __( 'We strongly recommend to combine BIALTY plugin with <a href="%s" target="_blank">BIGTA plugin</a> to auto-optimize your Image title attributes (Image title tags)', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( "https://wordpress.org/plugins/bulk-image-title-attribute/" ) ) ;
            ?>
                        </div>
                            
                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  __( 'Boost your ranking on Search engines', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>
                            
                            <div class="bialty-column col-8">
                                
                            <label class="bialty-switch bialty-boost-label">
                                <input type="checkbox" id="boost-robot" name="boost-robot" value="boost-robot" <?php 
            if ( $bialty_options['boost-robot'] ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                <span class="bialty-slider bialty-round"></span>
                            </label>

                                &nbsp; <span><?php 
            echo  __( 'Optimize site\'s crawlability with an optimized robots.txt', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                                
                                <div class="bialty-boost" <?php 
            
            if ( isset( $bialty_options['boost-robot'] ) && !empty($bialty_options['boost-robot']) ) {
                echo  'style="display: inline;"' ;
            } else {
                echo  'style="display: none;"' ;
            }
            
            ?>>

                                <div class="bialty-alert bialty-success" style="margin-top: 10px;"><?php 
            echo  sprintf( wp_kses( __( 'Click <a href="%s" target="_blank">HERE</a> to Install <a href="%2s" target="_blank">Better Robots.txt plugin</a> to boost your robots.txt', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( "https://wordpress.org/plugins/bulk-image-alt-text-with-yoast/" ), esc_url( "https://wordpress.org/plugins/bulk-image-alt-text-with-yoast/" ) ) ;
            ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  __( 'Mobile-Friendly & responsive design', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>
                            
                            <div class="bialty-column col-8">
                                
                            <label class="bialty-switch bialty-mobi-label">
                                <input type="checkbox" id="bialty-mobilook" name="bialty-mobilook" value="bialty-mobilook" <?php 
            if ( $bialty_options['bialty-mobilook'] ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                <span class="bialty-slider bialty-round"></span>
                            </label>

                                &nbsp; <span><?php 
            echo  __( 'Get dynamic mobile previews of your pages/posts/products + Facebook debugger', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                                
                                <div class="bialty-mobi" <?php 
            
            if ( isset( $bialty_options['bialty-mobilook'] ) && !empty($bialty_options['bialty-mobilook']) ) {
                echo  'style="display: inline;"' ;
            } else {
                echo  'style="display: none;"' ;
            }
            
            ?>>

                                    <div class="bialty-alert bialty-success" style="margin-top: 10px;"><?php 
            echo  sprintf( wp_kses( __( 'Click <a href="%s" target="_blank">HERE</a> to Install <a href="%2s" target="_blank">Mobilook</a> and test your website on Dualscreen format (Galaxy fold)', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( "https://wordpress.org/plugins/mobilook/" ), esc_url( "https://wordpress.org/plugins/mobilook/" ) ) ;
            ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="bialty-row">

                            <div class="bialty-column col-4">
                                <span class="bialty-label"><?php 
            echo  __( 'Looking for FREE unlimited content for SEO?', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                            </div>
                            
                            <div class="bialty-column col-8">
                                
                            <label class="bialty-switch bialty-vidseo-label">
                                <input type="checkbox" id="bialty-vidseo" name="bialty-vidseo" value="bialty-vidseo" <?php 
            if ( $bialty_options['bialty-vidseo'] ) {
                echo  'checked="checked"' ;
            }
            ?> />
                                <span class="bialty-slider bialty-round"></span>
                            </label>

                                &nbsp; <span><?php 
            echo  __( 'Get access to billions of non-indexed content with Video transcripts (Youtube)', 'bulk-image-alt-text-with-yoast' ) ;
            ?></span>
                                
                                <div class="bialty-vidseo" <?php 
            
            if ( isset( $bialty_options['bialty-vidseo'] ) && !empty($bialty_options['bialty-vidseo']) ) {
                echo  'style="display: inline;"' ;
            } else {
                echo  'style="display: none;"' ;
            }
            
            ?>>

                                    <div class="bialty-alert bialty-success" style="margin-top: 10px;"><?php 
            echo  sprintf( wp_kses( __( 'Click <a href="%s" target="_blank">HERE</a> to learn more about <a href="%2s" target="_blank">VidSEO</a> Wordpress plugin & how to skyrocket your SEO', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( "https://wordpress.org/plugins/vidseo/" ), esc_url( "https://wordpress.org/plugins/vidseo/" ) ) ;
            ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="bialty-note border" style="margin: 18px 0;"><p>😎 <i><?php 
            echo  sprintf(
                wp_kses( __( 'NEW - Get INSANE TRAFFIC with your website listed on <a href="%s" target="_blank">Baidu Webmaster Tools</a> for <a href="%s" target="_blank">Baidu.com</a>, the second largest search engine in the WORLD and the most used in China (more than 700 millions users).  <a href="%2s" target="_blank" class="note-link">SEE MORE ➤</a>', 'bulk-image-alt-text-with-yoast' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
                'class'  => array(),
            ),
            ) ),
                esc_url( "https://ziyuan.baidu.com/" ),
                esc_url( "https://baidu.com/" ),
                esc_url( "https://better-robots.com/baidu-webmaster-tools/" )
            ) ;
            ?></i></p></div>


                        <p class="submit"><input type="submit" name="update" class="button-primary" value="<?php 
            echo  esc_html__( 'Save Changes', 'bulk-image-alt-text-with-yoast' ) ;
            ?>" /></p>
                    </form>

        <div class="bialty-note">
            <h3><?php 
            echo  esc_html__( 'How to check your Alt texts now?', 'bulk-image-alt-text-with-yoast' ) ;
            ?></h3>
            <p><?php 
            echo  esc_html__( 'Go to your website, click right on a webpage and select "Show Page Source." (Firefox, Safari, Chrome, Internet Explorer,...). Scroll down to the appropriate section (displaying your content), after header area and before footer area. You will be able to identify your modified Alt Texts with your post title (if selected), your Yoast\'s Focus Keyword (if used) and your site name (if selected), separated with a comma. Please note that BIALTY modifies image Alt texts on Frontend (in your HTML code), not on backend (Media LIbrary, etc.), which would be useless for search engines. Want more details about this? Check our video :', 'bulk-image-alt-text-with-yoast' ) ;
            ?> <a href="https://vimeo.com/306421381">https://vimeo.com/306421381</a></p>
        </div>
                        
        <div class="bialty-alert bialty-info" style="display: block;">
            <?php 
            echo  esc_html__( 'IMPORTANT: BIALTY plugin modifies image alt texts on front-end. Any empty or existing alt text will be replaced according to settings above. About Yoast SEO, please note that it "checks" content in real time inside text editor in Wordpress back-end, so even if Yoast does not display a green bullet for the "image alt attributes" line, BIALTY is still doing the job. For your information, Google Bot and other search engine bots see only image alt attributes on Front-end (not as Yoast reading content inside text editor)', 'bulk-image-alt-text-with-yoast' ) ;
            ?>
        </div>
                    
        <div class="bialty-note">
            <p><?php 
            echo  esc_html__( 'Note 1: BIALTY is fully compatible with most popular page builders (TinyMCE, SiteOrigin, Elementor, Gutenberg)', 'bulk-image-alt-text-with-yoast' ) ;
            ?><br />
            <?php 
            echo  esc_html__( 'Note 2: If you\'ve installed YOAST SEO but did not optimize yet, select "Both Focus Keyword & Post Title"', 'bulk-image-alt-text-with-yoast' ) ;
            ?><br />
            <?php 
            echo  esc_html__( 'Note 3: If you did not install YOAST SEO plugin, please keep default settings. BIALTY will add your post titles to Alt tags.', 'bulk-image-alt-text-with-yoast' ) ;
            ?></p>
        </div>

        <?php 
            include dirname( __FILE__ ) . '/inc/seo-recommendations.php';
            ?>

                </div>
                <!-- end bialty-main -->
            </div>
            <!-- end main settings bialty-column col-8 -->

            <?php 
            include dirname( __FILE__ ) . '/inc/sidebar.php';
        }
        
        if ( $active_tab == 'bialty-faq' ) {
            include dirname( __FILE__ ) . '/inc/faq.php';
        }
        if ( $active_tab == 'bialty-recs' ) {
            include dirname( __FILE__ ) . '/inc/recommendations.php';
        }
        ?>

        </div>


        <?php 
    }

}
// end class
$bialty_settings = new bialty_settings();
// FIELD ON POST TYPE
add_action( 'add_meta_boxes', 'bialty_post_options' );
function bialty_post_options()
{
    $post_types = array( 'post', 'page' );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'bialty_post_options',
            // id, used as the html id att
            __( 'Bulk image Alt texts' ),
            // meta box title
            'bialty_post_alt',
            // callback function, spits out the content
            $post_type,
            // post type or page. This adds to posts only
            'side',
            // context, where on the screen
            'low'
        );
    }
}

// end pro
function bialty_post_alt( $post )
{
    global  $wpdb ;
    $use_bialty_alt = get_post_meta( $post->ID, 'use_bialty_alt', true );
    $bialty_alt_value = get_post_meta( $post->ID, 'bialty_cs_alt', true );
    $disable_bialty = get_post_meta( $post->ID, 'disable_bialty', true );
    ?>

    <div class="misc-pub-section misc-pub-section-last"><span id="timestamp">

        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="alt_text"><?php 
    echo  esc_html__( 'Use Custom Alt Text for all images?*', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label></p>

        <div class="bialty-switch-radio">

            <input type="radio" id="use_bialty_alt_btn1" name="use_bialty_alt" value="use_bialty_alt_yes" <?php 
    if ( isset( $use_bialty_alt ) ) {
        echo  'checked="checked"' ;
    }
    ?> />
            <label for="use_bialty_alt_btn1"><?php 
    echo  esc_html__( 'Yes', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label>

            <input type="radio" id="use_bialty_alt_btn2" name="use_bialty_alt" value="use_bialty_alt_no" <?php 
    if ( empty($use_bialty_alt) ) {
        echo  'checked="checked"' ;
    }
    ?> />
            <label for="use_bialty_alt_btn2"><?php 
    echo  esc_html__( 'No', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label>  

        </div>


        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="alt_text"><?php 
    echo  esc_html__( 'Insert your custom Alt text (other than Yoast Focus Keyword + Page title)', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label></p>

        <input type="text" name="bialty_cs_alt" value="<?php 
    if ( !empty($bialty_alt_value) ) {
        echo  $bialty_alt_value ;
    }
    ?>">


        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="alt_text"><?php 
    echo  esc_html__( 'Disable Bialty?', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label></p>

        <div class="bialty-switch-radio">

            <input type="radio" id="disable_bialty_btn1" name="disable_bialty" value="disable_bialty_yes" <?php 
    if ( isset( $disable_bialty ) ) {
        echo  'checked="checked"' ;
    }
    ?> />
            <label for="disable_bialty_btn1"><?php 
    echo  esc_html__( 'Yes', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label>

            <input type="radio" id="disable_bialty_btn2" name="disable_bialty" value="disable_bialty_no" <?php 
    if ( empty($disable_bialty) ) {
        echo  'checked="checked"' ;
    }
    ?> />
            <label for="disable_bialty_btn2"><?php 
    echo  esc_html__( 'No', 'bulk-image-alt-text-with-yoast' ) ;
    ?></label>  

        </div>

        <p style="margin-top: 20px;"><?php 
    echo  esc_html__( '*If NO, default Bialty settings will be applied', 'bulk-image-alt-text-with-yoast' ) ;
    ?></p>

    </div>

    <?php 
}

add_action( 'save_post', 'bialty_metadata' );
function bialty_metadata( $postid )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return false;
    }
    if ( !current_user_can( 'edit_page', $postid ) ) {
        return false;
    }
    if ( empty($postid) ) {
        return false;
    }
    if ( isset( $_POST['use_bialty_alt'] ) ) {
        $use_custom_alt = sanitize_text_field( $_POST['use_bialty_alt'] );
    }
    if ( isset( $_POST['disable_bialty'] ) ) {
        $disable_bialty_alt = sanitize_text_field( $_POST['disable_bialty'] );
    }
    $alt_safe = array( "use_bialty_alt_yes", "disable_bialty_yes" );
    ( isset( $_POST['use_bialty_alt'] ) && in_array( $use_custom_alt, $alt_safe ) ? update_post_meta( $postid, 'use_bialty_alt', true ) : delete_post_meta( $postid, 'use_bialty_alt' ) );
    ( isset( $_POST['bialty_cs_alt'] ) ? update_post_meta( $postid, 'bialty_cs_alt', $_REQUEST['bialty_cs_alt'] ) : delete_post_meta( $postid, 'bialty_cs_alt' ) );
    ( isset( $_POST['disable_bialty'] ) && in_array( $disable_bialty_alt, $alt_safe ) ? update_post_meta( $postid, 'disable_bialty', true ) : delete_post_meta( $postid, 'disable_bialty' ) );
}
