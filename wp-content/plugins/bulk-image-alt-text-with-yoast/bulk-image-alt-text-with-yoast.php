<?php

/*
* Plugin Name: BIALTY - Bulk Image Alt Text (Alt tag, Alt Attribute) with Yoast SEO + WooCommerce
* Description: Auto-add Alt texts, also called Alt Tags or Alt Attributes, from YOAST SEO Focus Keyword field (or page/post/product title) with your page/post/product title, to all images contained on your pages, posts, products, portfolios for better Google Ranking on search engines – Fully compatible with Woocommerce
* Author: Pagup
* Version: 1.4.2.1
* Author URI: https://pagup.com/
* Text Domain: bulk-image-alt-text-with-yoast
* Domain Path: /languages/
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function bialty_fs()
{
    global  $bialty_fs ;
    
    if ( !isset( $bialty_fs ) ) {
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
        $bialty_fs = fs_dynamic_init( array(
            'id'              => '2602',
            'slug'            => 'bulk-image-alt-text-with-yoast',
            'type'            => 'plugin',
            'public_key'      => 'pk_a805c7e6685744c85d7e720fd230d',
            'is_premium'      => false,
            'has_addons'      => false,
            'has_paid_plans'  => true,
            'has_affiliation' => 'selected',
            'menu'            => array(
            'slug'           => 'bialty',
            'override_exact' => true,
            'first-path'     => 'options-general.php?page=bialty',
            'support'        => false,
            'parent'         => array(
            'slug' => 'options-general.php',
        ),
        ),
            'is_live'         => true,
        ) );
    }
    
    return $bialty_fs;
}

// Init Freemius.
bialty_fs();
// Signal that SDK was initiated.
do_action( 'bialty_fs_loaded' );
function bialty_fs_settings_url()
{
    return admin_url( 'options-general.php?page=bialty&tab=bialty-settings' );
}

bialty_fs()->add_filter( 'connect_url', 'bialty_fs_settings_url' );
bialty_fs()->add_filter( 'after_skip_url', 'bialty_fs_settings_url' );
bialty_fs()->add_filter( 'after_connect_url', 'bialty_fs_settings_url' );
bialty_fs()->add_filter( 'after_pending_connect_url', 'bialty_fs_settings_url' );
// freemius opt-in
function bialty_fs_custom_connect_message(
    $message,
    $user_first_name,
    $product_title,
    $user_login,
    $site_link,
    $freemius_link
)
{
    $break = "<br><br>";
    return sprintf( esc_html__( 'Hey %1$s, %2$s Click on Allow & Continue to start optimizing your images with ALT tags :)!  Don\'t spend hours at adding manually alt tags to your images. BIALTY will use your YOAST settings automatically to get better results on search engines and improve your SEO. %2$s Never miss an important update -- opt-in to our security and feature updates notifications. %2$s See you on the other side.', 'bulk-image-alt-text-with-yoast' ), $user_first_name, $break );
}

bialty_fs()->add_filter(
    'connect_message',
    'bialty_fs_custom_connect_message',
    10,
    6
);
class bialty
{
    function __construct()
    {
        // stuff to do on plugin activation/deactivation
        //register_activation_hook(__FILE__, array(&$this, 'bialty_activate'));
        register_deactivation_hook( __FILE__, array( &$this, 'bialty_deactivate' ) );
        //add quick links to plugin settings
        $plugin = plugin_basename( __FILE__ );
        if ( is_admin() ) {
            add_filter( "plugin_action_links_{$plugin}", array( &$this, 'bialty_setting_link' ) );
        }
    }
    
    // end function __construct()
    // quick setting link in plugin section
    function bialty_setting_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=bialty">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
    
    // end function setting_link()
    // register options
    function bialty_options()
    {
        $bialty_options = get_option( 'bialty' );
        return $bialty_options;
    }
    
    // end function bialty_options()
    // removed settings (if checked) on plugin deactivation
    function bialty_deactivate()
    {
        $bialty_options = $this->bialty_options();
        if ( $bialty_options['remove_settings'] ) {
            delete_option( 'bialty' );
        }
    }

}
// end class
$bialty = new bialty();
// Convert image name
function bialty_fileName( $string )
{
    $string = preg_replace( "/[\\s-]+/", " ", $string );
    // clean dashes/whitespaces
    $string = preg_replace( "/[_]/", " ", $string );
    // convert whitespaces/underscore to space
    $string = ucwords( $string );
    // convert first letter of each word to capital
    return $string;
}

add_filter( 'the_content', 'bialty_content', 100 );
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'bialty_content', 100 );
add_filter( 'post_thumbnail_html', 'bialty_content', 100 );
function bialty_content( $content )
{
    
    if ( is_singular( array( 'post', 'page', 'product' ) ) ) {
        global  $bialty, $post ;
        $bialty_options = $bialty->bialty_options();
        // Define empty site title if option not selected
        $site_title = "";
        // Define site title if option is selected
        if ( isset( $bialty_options['add_site_title'] ) && !empty($bialty_options['add_site_title']) ) {
            $site_title = ", " . get_bloginfo( 'name' );
        }
        // Define empty focus keyword if it's not selected or yoast is inactive
        $replace_fkw = "";
        // Get post title
        $post_title = get_the_title( $post->ID ) . $site_title;
        // Post title and site title
        $replace_both = $post_title;
        // Get yoast focus keyword
        
        if ( class_exists( 'WPSEO_Meta' ) ) {
            // define focus keyword yoast
            $focus_keyword = WPSEO_Meta::get_value( 'focuskw', $post->ID );
            // define focus keyword and site title for alt text
            $replace_fkw = $focus_keyword . $site_title;
            // define focus keyword, post title and site title for alt text
            $replace_both = $focus_keyword . ', ' . $post_title . $site_title;
        } elseif ( class_exists( 'RankMath' ) ) {
            // define focus keyword rank math
            $focus_keyword = get_post_meta( $post->ID, 'rank_math_focus_keyword', true );
            $replace_fkw = $focus_keyword . $site_title;
            // define focus keyword, post title and site title for alt text
            $replace_both = $focus_keyword . ', ' . $post_title . $site_title;
        }
        
        // custom alt keyword
        $bialty_custom_alt = get_post_meta( $post->ID, 'use_bialty_alt', true );
        $custom_alt_kw = get_post_meta( $post->ID, 'bialty_cs_alt', true );
        // Create DOM$content,
        $bialty_dom = new DOMDocument( '1.0', 'UTF-8' );
        
        if ( is_singular( 'product' ) ) {
            @$bialty_dom->loadHTML( mb_convert_encoding( "{$content}", 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        } else {
            @$bialty_dom->loadHTML( mb_convert_encoding( "<div class='bialty-container'>{$content}</div>", 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        }
        
        $bialty_html = new DOMXPath( $bialty_dom );
        foreach ( $bialty_html->query( "//img" ) as $img_node ) {
            $img_url = $img_node->getAttribute( "src" );
            $img_path = pathinfo( $img_url );
            $img_name = bialty_fileName( $img_path['filename'] );
            
            if ( is_singular( array( 'post', 'page' ) ) ) {
                $bialty_img_found = true;
                
                if ( empty($img_node->getAttribute( 'alt' )) ) {
                    // REPLACE EMPTY ALT
                    
                    if ( isset( $bialty_options['alt_empty'] ) && !empty($bialty_options['alt_empty']) ) {
                        if ( $bialty_options['alt_empty'] == "alt_empty_title" ) {
                            $img_node->setAttribute( "alt", $post_title );
                        }
                        if ( $bialty_options['alt_empty'] == "alt_empty_fkw" ) {
                            $img_node->setAttribute( "alt", $replace_fkw );
                        }
                        if ( $bialty_options['alt_empty'] == "alt_empty_imagename" ) {
                            $img_node->setAttribute( "alt", $img_name );
                        }
                        if ( $bialty_options['alt_empty'] == "alt_empty_both" ) {
                            $img_node->setAttribute( "alt", $replace_both );
                        }
                        if ( $bialty_options['alt_empty'] == "alt_empty_both" ) {
                            $img_node->setAttribute( "alt", $replace_both );
                        }
                    }
                
                } else {
                    // REPLACE DEFINED ALT
                    
                    if ( isset( $bialty_options['alt_not_empty'] ) && !empty($bialty_options['alt_not_empty']) ) {
                        if ( $bialty_options['alt_not_empty'] == "alt_not_empty_title" ) {
                            $img_node->setAttribute( "alt", $post_title );
                        }
                        if ( $bialty_options['alt_not_empty'] == "alt_not_empty_fkw" ) {
                            $img_node->setAttribute( "alt", $replace_fkw );
                        }
                        
                        if ( $bialty_options['alt_not_empty'] == "alt_not_empty_imagename" ) {
                            $img_url = $img_node->getAttribute( "src" );
                            $img_path = pathinfo( $img_url );
                            $img_name = bialty_fileName( $img_path['filename'] );
                            $img_node->setAttribute( "alt", $img_name );
                        }
                        
                        if ( $bialty_options['alt_not_empty'] == "alt_not_empty_both" ) {
                            $img_node->setAttribute( "alt", $replace_both );
                        }
                        if ( $bialty_options['alt_not_empty'] == "alt_not_empty_both" ) {
                            $img_node->setAttribute( "alt", $replace_both );
                        }
                    }
                
                }
                
                if ( $bialty_custom_alt == true && !empty($custom_alt_kw) ) {
                    $img_node->setAttribute( "alt", $custom_alt_kw );
                }
            }
        
        }
        // end foreach
    }
    
    // Beaver Builder (fl_builder) Compatibility Fixed @since 1.3.2
    if ( is_singular( array( 'post', 'page', 'product' ) ) ) {
        
        if ( empty(get_post_meta( $post->ID, 'disable_bialty', true )) ) {
            // $saveDom condition @since 1.3.4
            $saveDom = true;
            // BuddyPress Profile Image Upload Fix @since 1.3.1
            if ( class_exists( 'BuddyPress' ) ) {
                if ( bp_is_my_profile() ) {
                    $saveDom = false;
                }
            }
            // WCFM - Frontend Manager Compatibility Fixed @since 1.3.2
            if ( class_exists( 'WCFM' ) ) {
                if ( is_wcfm_page() ) {
                    $saveDom = false;
                }
            }
            // Disabled on Woocommerce Checkout @since 1.3.3.1
            if ( class_exists( 'WooCommerce' ) ) {
                if ( is_checkout() ) {
                    $saveDom = false;
                }
            }
            if ( $saveDom ) {
                $content = $bialty_dom->saveHtml();
            }
        }
    
    }
    return $content;
}

// admin notifications
include_once dirname( __FILE__ ) . '/inc/notices.php';
add_action( 'init', 'bialty_textdomain' );
function bialty_textdomain()
{
    load_plugin_textdomain( 'bulk-image-alt-text-with-yoast', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

if ( is_admin() ) {
    include_once dirname( __FILE__ ) . '/bulk-image-alt-text-with-yoast-admin.php';
}