<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?>
<div class="notice sbp-notice is-dismissible" data-notice="welcome" id="sbp-notice">
    <img src="<?php echo esc_url( plugins_url( 'images/logo.png', dirname( __FILE__ ) ) ); ?>" width="80">
    <h1><?php esc_html_e( 'Welcome to Speed Booster Pack', 'speed-booster-pack' ); ?></h1>
    <p><?php printf( esc_html__( 'Thank you for installing Speed Booster Pack! Check out the %sPlugin settings%s for new features that can make your site load faster.',
			'speed-booster-pack' ),
			'<a href="admin.php?page=sbp-options">',
			'</a>' ); ?></p>
    <p>
        <a href="admin.php?page=sbp-options" class="button button-primary button-hero"><?php esc_html_e( 'Get started',
				'speed-booster-pack' ); ?></a>
        <a href="https://optimocha.com/?ref=sbp" class="button button-primary button-hero"
           target="_blank"><?php esc_html_e( 'Pro Optimization Service', 'speed-booster-pack' ); ?></a>
    </p>
</div>
<style>
    .sbp-notice {
        background: #e9eff3;
        border: 10px solid #fff;
        color: #608299;
        padding: 30px;
        text-align: center;
        position: relative;
    }
</style>
