/**
 * The contents of this script only gets loaded on the plugin page
 */
(function( $ ) {

	'use strict';

	/**
	 * Function used to handle admin UI postboxes
	 */
	function admin_postboxes() {

		postboxes.add_postbox_toggles( pagenow );

		// set cursor to pointer
		$( '.postbox .hndle' ).css( 'cursor', 'pointer' );
	}



	/**
	 * Handle UI tab switching via jQuery instead of relying on CSS only
	 */
	function admin_tab_switching() {

		var nav_tab_selector = '.nav-tab-wrapper a';
		var initial_tab_href = '';

		// get the first tab href
		if (window.location.hash) {
			initial_tab_href = window.location.hash;
		} else {
			initial_tab_href = localStorage.getItem('sbp-current-page') || $(nav_tab_selector + ':first').attr('href');
		}

		// make all the tabs, except the first one hidden
		$( '.sb-pack-tab' ).each( function( index, value ) {
			if ( '#' + $( this ).attr( 'id' ) !== initial_tab_href ) {
				$( this ).hide();
			}
		} );

		$( nav_tab_selector ).removeClass( 'nav-tab-active' ); // remove class from previous selector
		$( nav_tab_selector + '[href=' + initial_tab_href + ']' ).addClass( 'nav-tab-active' ); // remove class from previous selector
		window.location.hash = initial_tab_href;

		/**
		 * Listen for click events on nav-tab links
		 */
		$( nav_tab_selector ).click( function( event ) {

			$( nav_tab_selector ).removeClass( 'nav-tab-active' ); // remove class from previous selector
			$( this ).addClass( 'nav-tab-active' ).blur(); // add class to currently clicked selector

			var clicked_tab = $( this ).attr( 'href' );
			localStorage.setItem('sbp-current-page', clicked_tab);

			$( '.sb-pack-tab' ).each( function( index, value ) {
				if ( '#' + $( this ).attr( 'id' ) !== clicked_tab ) {
					$( this ).hide();
				}

				$( clicked_tab ).fadeIn();

			} );

			// prevent default behavior
			// event.preventDefault();

		} );
	}

	$( document ).ready( function() {
		admin_postboxes();
		admin_tab_switching();


		if ($('[name*="sbp_ga_tracking_script"]:checked').val() == 'minimal') {
			$('.minimal-analytics-warning').stop().show(0);
		}

		// Local analytics
		$('[name*="sbp_ga_tracking_script"]').on('change', function(e) {
			if (e.target.value === 'minimal') {
				$('.minimal-analytics-warning').stop().slideDown();
			} else {
				$('.minimal-analytics-warning').stop().slideUp();
			}
		});

		var $elems = $('[name*="sbp_settings[sbp_disable_display_features]"], [name*="sbp_settings[sbp_anonymize_ip]"], [name*="sbp_settings[sbp_track_loggedin_admins]"]');

		if ($('[name*="sbp_ga_tracking_script"]:checked').val() == 'minimal' || $('[name*="sbp_ga_tracking_script"]:checked').val() == 'gtm') {
			$elems.parent().parent().parent().stop().hide();
		}

		// Local analytics
		$('[name*="sbp_ga_tracking_script"]').on('change', function(e) {
			if (e.target.value === 'minimal' || e.target.value === 'gtm') {
				$elems.parent().parent().parent().stop().slideUp();
			} else {
				$elems.parent().parent().parent().stop().slideDown();
			}
		});
	} );

})( jQuery );