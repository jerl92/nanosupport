<?php
/**
 * Shortcode: Submit Ticket
 *
 * Showing the functionality for submitting a ticket from the the front end
 * using shortcode [nanosupport_submit_ticket]
 *
 * @author  	nanodesigns
 * @category 	Shortcode
 * @package 	NanoSupport
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ns_submit_support_ticket() {

	//Get the NanoSupport Settings from Database
    $ns_general_settings = get_option( 'nanosupport_settings' );

	global $ns_errors;
    
    //Display error message[s], if any
    if( !empty( $ns_errors ) ){
        foreach( $ns_errors as $error ){
    		echo '<div class="ns-alert ns-alert-danger" role="alert">';
    			/* translators: error message */
            	printf( __( '<strong>Error:</strong> %s', 'nanosupport' ), $error );
        	echo '</div>';
        }
    }

    /**
	 * Show a Redirection Message
	 * while redirected.
	 */
	if( isset($_GET['from']) && 'sd' === $_GET['from'] ) {
		echo '<div class="ns-alert ns-alert-info" role="alert">';
			esc_html_e( 'You are redirected from the Support Desk, because you are not logged in, and have no permission to view any ticket.', 'nanosupport' );
		echo '</div>';
	}

    //Display success message, if any
    if( isset($_GET['ns_success']) && $_GET['ns_success'] == 1 ) {
		echo '<div class="ns-alert ns-alert-success" role="alert">';
			echo wp_kses( __( '<strong>Success!</strong> Your ticket is submitted successfully! It will be reviewed shortly and replied as early as possible.', 'nanosupport' ), array('strong' => array()) );
			echo '&nbsp;<a href="'. get_permalink( $ns_general_settings['support_desk'] ) .'" class="link-to-desk"><i class="ns-icon-tag"></i>&nbsp;';
				esc_html_e( 'Check your tickets', 'nanosupport' );
			echo '</a>';
	    echo '</div>';
	}

	//Clean up request URI from temporary args for alert[s].
	$_SERVER['REQUEST_URI'] = remove_query_arg( 'ns_success', $_SERVER['REQUEST_URI'] );

	ob_start();
	?>

	<div id="nanosupport-add-ticket" class="nano-support-ticket nano-add-ticket ns-no-js">

		<?php
		/**
		 * -----------------------------------------------------------------------
		 * HOOK : ACTION HOOK
		 * nanosupport_before_new_ticket
		 *
		 * To Hook anything before the Add New Ticket Form.
		 *
		 * @since  1.0.0
		 *
		 * 10	- ns_new_ticket_navigation()
		 * -----------------------------------------------------------------------
		 */
		do_action( 'nanosupport_before_new_ticket' );
		?>

		<div class="ns-row">
			<div class="ns-col-md-12">

				<?php if( is_user_logged_in() ) { ?>

					<form class="ns-form-horizontal" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"<?php
						/**
						* -----------------------------------------------------------------------
						* HOOK : ACTION HOOK
						* nanosupport_new_ticket_form_tag
						*
						* Fires inside the Add New Ticket Form tag.
						*
						* @since  1.0.0
						*
						* 10	- ns_change_form_type_for_rich_media()
						* -----------------------------------------------------------------------
						*/
						do_action( 'nanosupport_new_ticket_form_tag' );
						?>>

						<!-- No FACTURE -->
						<div class="ns-form-group">
							<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12">
								
								<div class="ns-col-md-3 ns-col-sm-3 ns-col-xs-12 ">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
										<label for="ns-ticket-inovice-number">
											<?php esc_html_e( 'S.O# or P.O# Number', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
										</label>
										<?php echo ns_tooltip( 'ns-inovice-number', esc_html__( 'Sale order "S.O#" or Porduct order "P.O#" number', 'nanosupport' ), 'bottom' ); ?>
									</div>
									<input type="text" class="ns-form-control" name="ns_ticket_inovice_number" id="ns-ticket-inovice-number" value="<?php echo !empty($_POST['ns_ticket_inovice_number']) ? stripslashes_deep( $_POST['ns_ticket_inovice_number'] ) : ''; ?>" aria-describedby="ns-inovice-number" required>
								</div>
						
								<div class="ns-col-md-3 ns-col-sm-3 ns-col-xs-12">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
										<label for="ns-ticket-internal-reference-number">
											<?php esc_html_e( 'Your Internal Reference Number', 'nanosupport' ); ?>
										</label>
										<?php echo ns_tooltip( 'ns-internal-reference-number', esc_html__( 'Your internal reference number from your establishment', 'nanosupport' ), 'bottom' ); ?>
									</div>
									<input type="text" class="ns-form-control" name="ns_ticket_internal_reference_number" id="ns-internal-reference-number" value="<?php echo !empty($_POST['ns_ticket_internal_reference_number']) ? stripslashes_deep( $_POST['ns_ticket_internal_reference_number'] ) : ''; ?>" aria-describedby="ns-internal-reference-number">
								</div>

								<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
										<label for="ns-ticket-subject">
											<?php esc_html_e( 'Device Brand, Model and from factor', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
										</label>
										<?php echo ns_tooltip( 'ns-computer-model', esc_html__( 'Computer brand, model and from factor EX: Dell Optiplex 990 Tower', 'nanosupport' ), 'bottom' ); ?>
									</div>
									<input type="text" class="ns-form-control" name="ns_ticket_subject" id="ns-ticket-subject" value="<?php echo !empty($_POST['ns_ticket_subject']) ? stripslashes_deep( $_POST['ns_ticket_subject'] ) : ''; ?>" aria-describedby="ns-subject" required>
								</div>

							</div>	
						</div> <!-- /.ns-form-group -->

						<!-- No SÉRIE -->
						<div class="ns-form-group">
							<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12">
								
								<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
										<label for="ns-ticket-serial-number">
											<?php esc_html_e( 'Serial Number', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
										</label>
										<?php echo ns_tooltip( 'ns-computer-serial-number', esc_html__( 'Device serial number' , 'nanosupport' ), 'bottom' ); ?>
									</div>
									<input type="text" class="ns-form-control" name="ns_ticket_serial_number" id="ns-ticket-serial-number" value="<?php echo !empty($_POST['ns_ticket_serial_number']) ? stripslashes_deep( $_POST['ns_ticket_serial_number'] ) : ''; ?>" aria-describedby="ns-serial-number" required>
								</div>	

								<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
										<label for="ns-ticket-issuse">
											<?php esc_html_e( 'Issuse/Defective Part', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
										</label>
										<?php echo ns_tooltip( 'ns-ticket-issuse', esc_html__( 'Device Issuse/Defective part short description' , 'nanosupport' ), 'bottom' ); ?>
									</div>
									<input type="text" class="ns-form-control" name="ns_ticket_issuse" id="ns-ticket-issuse" value="<?php echo !empty($_POST['ns_ticket_issuse']) ? stripslashes_deep( $_POST['ns_ticket_issuse'] ) : ''; ?>" aria-describedby="ns-ticket-issuse" required>
								</div>

							</div> <!-- /.ns-col-md-12 -->
						</div> <!-- /.ns-form-group -->

						<?php
						/**
						* WP Editor array.
						* Declare the array here, so that we can conditionally
						* display tooltip content.
						* @var array
						* ...
						*/
						$wp_editor_array = array(
											'media_buttons'		=> true,
											'drag_drop_upload'	=> true,
											'textarea_name'		=> 'ns_ticket_details',
											'textarea_rows'		=> 5,
											'editor_class'		=> 'ns-form-control',
											'quicktags'			=> false,
											'tinymce'			=> true
										);

						/**
						* -----------------------------------------------------------------------
						* HOOK : FILTER HOOK
						* ns_wp_editor_specs
						* 
						* Hook to moderate the specs of the wp_editor().
						*
						* @since  1.0.0
						* -----------------------------------------------------------------------
						*/
						$wp_editor_specs = apply_filters( 'ns_wp_editor_specs', $wp_editor_array );
						?>

						<!-- TICKET DETAILS -->
						<div class="ns-form-group">
							<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12">
								
								<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label">
									
										<label for="ns-ticket-details">
											<?php esc_html_e( 'Details', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
										</label>

										<?php
										$character_limit = ns_is_character_limit();
										if( $character_limit ) {
											/* translators: character limit to the ticket content, in number */
											$content_tooltip_msg = sprintf( esc_html__( 'Write down your issue in details... At least %s characters is a must.', 'nanosupport' ), $character_limit );
											// allowed HTML tags are not necessary if rich text editor is disabled.
											if( $wp_editor_specs['tinymce'] != true ) {
												$content_tooltip_msg .= '<br><small>';
													/* translators: allowed HTML tags to the plugin */
													$content_tooltip_msg .= sprintf( __( '<strong>Allowed HTML Tags:</strong><br> %s', 'nanosupport' ), ns_get_allowed_html_tags() );
												$content_tooltip_msg .= '</small>';
											}

											echo ns_tooltip( 'ns-details', $content_tooltip_msg, 'bottom' );
										} else {
											$content_tooltip_msg = esc_html__( 'Write down your issue in details...', 'nanosupport' );
											// allowed HTML tags are not necessary if rich text editor is disabled.
											if( $wp_editor_specs['tinymce'] != true ) {
												$content_tooltip_msg .= '<br><small>';
													/* translators: allowed HTML tags to the plugin */
													$content_tooltip_msg .= sprintf( __( '<strong>Allowed HTML Tags:</strong><br> %s', 'nanosupport' ), ns_get_allowed_html_tags() );
												$content_tooltip_msg .= '</small>';
											}

											echo ns_tooltip( 'ns-details', $content_tooltip_msg, 'bottom' );
										}
										?>

										<?php
										$ticket_content = !empty($_POST['ns_ticket_details']) ? $_POST['ns_ticket_details'] : '';

										// initiate the editor.
										wp_editor(
												$content   = $ticket_content,
												$editor_id = 'ns-ticket-details',
												$wp_editor_specs
											);
										?>
									
									</div>

								</div>

								<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
								
									<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-control-label ns-control-label-mobile">
										
										<label id="ns-ticket-retrun-adresse">
											<?php esc_html_e( 'Alternative return Adresse', 'nanosupport' ); ?>
										</label>


										<?php echo ns_tooltip( 'ns-return-adresse', esc_html__( 'Use a alternative return adresse, if blank profil adresse will be used' , 'nanosupport' ), 'bottom' ); ?>

										<i id="retrun-adresse-map-click" class="arrow down"></i>

									</div>

										<div id="ticket-retrun-adresse-div">

											<?php
											$wp_editor_array = array(
																'media_buttons'		=> false,
																'textarea_name'		=> 'ns_ticket_return_adresse',
																'textarea_rows'		=> 4,
																'editor_class'		=> 'ns-form-control',
																'quicktags'			=> false,
																'tinymce'			=> false
															);

											$wp_editor_specs_adresse = apply_filters( 'ns_wp_editor_specs_adresse', $wp_editor_array );
											
									
											function prefix_add_footer_styles() {
												wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyD6e1aNk3fjsJEtmcYXXVRbZmZJlsJyLDM&libraries=places&callback=initMap', array('jquery'));     
											}
											add_action( 'get_footer', 'prefix_add_footer_styles' ); ?>
										
											<div id="ticket-retrun-adresse-text-area">

												<input id="searchInput" class="controls" type="text" placeholder="<?php esc_html_e('Enter place/school name or adresse', 'nanosupport') ?>"></input>
												<div id="map"></div>
											
												<?php 
												$ticket_return_adresse = !empty($_POST['ns_ticket_return_adresse']) ? $_POST['ns_ticket_return_adresse'] : '';
												// initiate the editor.
												wp_editor(
														$content   = $ticket_return_adresse,
														$editor_id = 'ns-ticket-return-adresse',
														$wp_editor_specs_adresse
													);
												?>
											</div>
										
									</div>

									<div class="ns-form-group ns-form-group-submit">
										<div class="ns-col-sm-12 ns-col-sm-12 ns-col-xs-12">
											<button type="submit" name="ns_submit" class="ns-btn ns-btn-primary ns-btn-ticket" onclick="return confirm(' <?php _e('Confirm before send!','nanosupport'); ?> ')">
												<?php esc_html_e( 'Submit', 'nanosupport' ); ?> *
											</button>

											</br>

											<?php if( is_user_logged_in() ) : ?>
												<span class="ns-text-dim ns-small">
													<?php
													$current_user = wp_get_current_user();
													echo esc_html_e( '* By clicking the submit button, you agree to our ', 'nanosupport' ) ; ?>
													
													<a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php esc_html_e('Terms of use', 'nanosupport') ?></a> & <a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php esc_html_e('Return Policy', 'nanosupport') ?></a>
												</span>
											<?php endif; ?>
										</div>
									</div> <!-- /.ns-form-group -->

								</div>
							</div>
						</div>

					</form> <!-- /.ns-form-horizontal -->

				<?php } else { //endif( ! is_user_logged_in() )

					echo wp_safe_redirect( wp_login_url( get_permalink() ) ); 

				} ?>

			</div>
		</div> <!-- /.ns-row -->

		<?php
		/**
		 * -----------------------------------------------------------------------
		 * HOOK : ACTION HOOK
		 * nanosupport_after_new_ticket
		 * 
		 * To Hook anything after the Add New Ticket Form.
		 *
		 * @since  1.0.0
		 * -----------------------------------------------------------------------
		 */
		do_action( 'nanosupport_after_new_ticket' );
		?>

	</div> <!-- /.nano-support-ticket .nano-add-ticket -->
	
	<?php
	return ob_get_clean();
}

add_shortcode( 'nanosupport_submit_ticket', 'ns_submit_support_ticket' );
