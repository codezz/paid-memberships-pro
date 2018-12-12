<?php
/**
 * Notes in a Pointer dialog box for guiding users in the dashboard interface.
 */
add_action( 'admin_enqueue_scripts', 'pmpro_enqueue_admin_pointer_scripts' );
/**
 * [pmpro_enqueue_admin_pointer_scripts] Enqueue the scripts needed to builder admin pointers.
 * 
 * @return void
 */
function pmpro_enqueue_admin_pointer_scripts() {
	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );
	// hook the pointer
	add_action( 'admin_print_footer_scripts', 'pmpro_prepare_pointer_scripts' );
}
/**
 * [pmpro_prepare_pointer_scripts] Details about PMPro 2.0 that are added to the Admin Pointer
 * 
 * @return void
 */
function pmpro_prepare_pointer_scripts() {
	$show_pointer = true;
	$file_error   = true;

	$id       = '#toplevel_page_pmpro-dashboard';
	$content  = '<h3>' .  __( 'Welcome to New PMPro location.', 'paid-memberships-pro' ) . '</h3>';
	$content .= '<p>'. sprintf( __( 'The Memberships menu has moved. The <a href="%s">Members List is here</a>; Membership Levels and Discount Codes pages can be found under <a href="%s">Settings</a>.', 'paid-memberships-pro' ) , 'admin.php?page=pmpro-memberslist', 'admin.php?page=pmpro-membershiplevels' ). '</p>';

	$options  = array(
		'content'  => $content,
		'position' => array(
			'edge'  => 'top',
			'align' => 'left',
		),
	);
	$function = '';

	$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	if ( ! in_array( 'pmpro_v2_intro', $dismissed_pointers ) ) {
		pmpro_build_pointer_script( $id, $options, __( 'Close', 'paid-memberships-pro' ), $button2, $function );
	}
}

/**
 * [pmpro_build_pointer_script]
 * 
 * @param  [type]  $id
 * 
 * @param  [type]  $options  [description]
 * @param  [type]  $button1  [description]
 * @param  boolean $button2  [description]
 * @param  string  $function [description]
 * @return [type]            [description]
 */
function pmpro_build_pointer_script( $id, $options, $button1, $button2 = false, $function = '' ) {
	?>
<script type="text/javascript">
	(function ($) {
		// Define pointer options
		var wp_pointers_tour_opts = <?php echo json_encode( $options ); ?>, setup;

		wp_pointers_tour_opts = $.extend (wp_pointers_tour_opts, {
			// Add 'Close' button
			buttons: function (event, t) {
				button = jQuery ('<a id="pointer-close" class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');
				button.bind ('click.pointer', function () {
					t.element.pointer ('close');
				});
				return button;
			},
			close: function () {
				// Post to admin ajax to disable pointers when user clicks "Close"
				$.post (ajaxurl, {
					pointer: 'pmpro_v2_tour',
					action: 'dismiss-wp-pointer'
				});
			}
		});

		// This is used for our "button2" value above (advances the pointers)
		setup = function () {
			$('<?php echo $id; ?>').pointer(wp_pointers_tour_opts).pointer('open');

			<?php if ( $button2 ) { ?>
				jQuery ('#pointer-close').after ('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
				jQuery ('#pointer-primary').click (function () {
					<?php echo $function; ?>  // Execute button2 function
				});
				jQuery ('#pointer-close').click (function () {
					// Post to admin ajax to disable pointers when user clicks "Close"
					$.post (ajaxurl, {
						pointer: 'pmpro_v2_tour',
						action: 'dismiss-wp-pointer'
					});
				})
			<?php } ?>
		};

		if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {

			$(window).bind('load.wp-pointers', setup);
		}
		else {
			setup ();
		}
	}) (jQuery);
</script>
	<?php
}