<?php
// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="<?php echo $script_src; ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo esc_attr($tracking_id); ?>');

    <?php
    if ( ! empty( $sbp_options['sbp_disable_display_features'] ) && $sbp_options['sbp_disable_display_features'] == "1" ) {
	    echo "gtag('config', '" . esc_attr($tracking_id) . "', { 'allow_ad_personalization_signals': false });\n";
    }

    //anonymize ip
    if ( ! empty( $sbp_options['sbp_anonymize_ip'] ) && $sbp_options['sbp_anonymize_ip'] == "1" ) {
	    echo "gtag('config', '" . esc_attr($tracking_id) . "', { 'anonymize_ip': true });\n";
    }
    ?>
</script>