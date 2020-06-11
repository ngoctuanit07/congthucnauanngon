<?php
// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
?>

<!-- Speed Booster Pack -->
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '<?php echo $ga_script; ?>';f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_attr($tracking_id); ?>');</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $tracking_id; ?>"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- /Speed Booster Pack -->