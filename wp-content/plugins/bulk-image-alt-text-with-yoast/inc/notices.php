<?php

// free only notices
// pro notification
/* function bialty_notice_subscribe() {
      if ( ! PAnD::is_admin_notice_active( 'bialty-subcribe-120' ) ) {
		  return;
	   }
            $purchase_url = "options-general.php?page=bialty-pricing";
            $getpro = sprintf( wp_kses( __( 'Boost your Google ranking with <a href="%s" target="_blank">BIALTY - Bulk Image Alt Text with Yoast SEO + WooCommerce PRO</a> | Get 10&#37; OFF if you subscribe here:', 'bialty' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $purchase_url ) );
        ?>
        <div data-dismissible="bialty-subcribe-30" class="notice bialty-notice notice-success is-dismissible">
            <p class="bialty-p"><?php echo $getpro; ?></p>
            <form action="https://Pagup.us14.list-manage.com/subscribe/post?u=a706b8e968389b05725c65849&amp;id=29257f3bf8" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate bialty-form-notice" target="_blank" novalidate>

                <input type="email" value="" name="EMAIL" class="bialty-field-notice" placeholder="<?php echo __( 'Email address', 'bialty' ); ?>" required>

                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a706b8e968389b05725c65849_29257f3bf8" tabindex="-1" value=""></div>
                <div class="clear bialty-clear-notice"><input type="submit" value="<?php echo __( 'Subscribe', 'bialty' ); ?>" name="subscribe" id="mc-embedded-subscribe" class="bialty-btn-notice"></div>

            </form>
        </div>

        <?php
    } */
function bialty_notice_rate()
{
    if ( !PAnD::is_admin_notice_active( 'bialty-rating-120' ) ) {
        return;
    }
    ?>
    
            <div data-dismissible="bialty-rating-30" class="notice bialty-notice notice-success is-dismissible">
                <p class="bialty-p"><?php 
    $rating_url = "https://wordpress.org/support/plugin/bulk-image-alt-text-with-yoast/reviews/?rate=5#new-post";
    $show_support = sprintf( wp_kses( __( 'Show support for BIALTY - Bulk Image Alt Text (Alt tag, Alt Attribute) with Yoast SEO + WooCommerce with a 5-star rating » <a href="%s" target="_blank">Click here</a>', 'bialty' ), array(
        'a' => array(
        'href'   => array(),
        'target' => array(),
    ),
    ) ), esc_url( $rating_url ) );
    echo  $show_support ;
    ?></p>
            </div>
    <?php 
}

add_action( 'admin_init', array( 'PAnD', 'init' ) );
//add_action( 'admin_notices', 'bialty_notice_subscribe' );
add_action( 'admin_notices', 'bialty_notice_rate' );
// end free only