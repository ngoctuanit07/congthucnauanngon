<?php
// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
?>
<!-- Speed Booster Pack - Google Analytics -->
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o), m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '<?php echo $ga_script; ?>', 'ga');
    ga('create', '<?php echo esc_attr($sbp_options['sbp_ga_tracking_id']); ?>', 'auto');

    //disable display features
	<?php
	if ( ! empty( $sbp_options['sbp_disable_display_features'] ) && $sbp_options['sbp_disable_display_features'] == "1" ) {
		echo "ga('set', 'allowAdFeatures', false);\n";
	}

	//anonymize ip
	if ( ! empty( $sbp_options['sbp_anonymize_ip'] ) && $sbp_options['sbp_anonymize_ip'] == "1" ) {
		echo "ga('set', 'anonymizeIp', true);\n";
	}
	?>

    ga('send', 'pageview');

	<?php
	//adjusted bounce rate
	if ( ! empty( $sbp_options['sbp_bounce_rate'] ) ) {
		echo 'setTimeout("ga(' . "'send','event','adjusted bounce rate','" . esc_attr($sbp_options['sbp_bounce_rate']) . " seconds')" . '"' . "," . esc_attr($sbp_options['sbp_bounce_rate'] * 1000) . ");\n";
	}
	?>

</script>