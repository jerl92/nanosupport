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
	
	$form_factor_terms = get_terms( array(
		'taxonomy' => 'nanosupport_form_factor',
		'hide_empty' => false,
	) );

    /**
	 * Show a Redirection Message
	 * while redirected.
	 */
	if( isset($_GET['from']) && 'sd' === $_GET['from'] ) {
		wp_redirect( wp_login_url() );
		exit;
	}

    //Display success message, if any
    if( isset($_GET['ns_success']) && $_GET['ns_success'] == 1 ) {
		echo '<div class="ns-alert ns-alert-success" role="alert">';
			echo wp_kses( __( '<strong>Success!</strong> Your RMA has been submitted successfully. Our team is reviewing it now and will be responding to you shortly.', 'nanosupport' ), array('strong' => array()) );
			echo '&nbsp;<a href="'. get_permalink( $ns_general_settings['support_desk'] ) .'" class="link-to-desk"><i class="ns-icon-tag" aria-hidden="true"></i>&nbsp;';
				esc_html_e( 'Consult your RMA', 'nanosupport' );
			echo '</a>';
	    echo '</div>';
	}

	//Clean up request URI from temporary args for alert[s].
	$_SERVER['REQUEST_URI'] = remove_query_arg( 'ns_success', $_SERVER['REQUEST_URI'] );

	ob_start();

	if( !is_user_logged_in() ) {
		wp_redirect( wp_login_url() );
		exit;
	}

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

					<!-- SUBJECT -->
					<div class="ns-form-group">
						<label for="ns-ticket-inovice-number" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'S.O# or P.O# Number', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-inovice-number', esc_html__( 'Sale order "S.O#" or Porduct order "P.O#" number', 'nanosupport' ), 'bottom' ); ?> 
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
                        <input type="text" class="ns-form-control" name="ns_ticket_inovice_number" id="ns-ticket-inovice-number" value="<?php echo !empty($_POST['ns_ticket_inovice_number']) ? stripslashes_deep( $_POST['ns_ticket_inovice_number'] ) : ''; ?>" aria-describedby="ns-inovice-number" required>
						</div>
					</div> <!-- /.ns-form-group -->

                    <div class="ns-form-group">
						<label for="ns-ticket-subject" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'Device Brand and Model', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-computer-model', esc_html__( 'Computer brand and model EX: Dell Optiplex 990', 'nanosupport' ), 'bottom' ); ?>
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
                        <input type="text" class="ns-form-control" name="ns_ticket_subject" id="ns-ticket-subject" value="<?php echo !empty($_POST['ns_ticket_subject']) ? stripslashes_deep( $_POST['ns_ticket_subject'] ) : ''; ?>" aria-describedby="ns-subject" required>
						</div>
					</div> <!-- /.ns-form-group -->

					<div class="ns-form-group">
						<label for="ns-ticket-form-factor" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
								<?php esc_html_e( 'Component type', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
							</label>
							<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
								<?php echo ns_tooltip( 'ns-form-factor', esc_html__( 'Chosse component type', 'nanosupport' ), 'bottom' ); ?>
							</div>
							<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
							<select name="ns_ticket_form_factor" class="ns-field-item" id="ns-ticket-form-factor" aria-describedby="ns-ticket-form-factor-tooltip" required>
								<?php if ( $form_factor_terms ) {
									echo '<option value="0">'. esc_html__( 'Select a component type' , 'nanosupport' ) .'</option>';
									foreach ( $form_factor_terms as $form_factor_term ) {
										$lang_text_term = qtranxf_use(qtranxf_getLanguage(), $form_factor_term->name);
										echo '<option value="'. $form_factor_term->term_id .'">'. $lang_text_term .'</option>';
									}
								} ?>
							</select>
						</div>
					</div> <!-- /.ns-form-group -->

                    <div class="ns-form-group">
						<label for="ns-ticket-serial-number" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'Device serial Number', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-computer-serial-number', esc_html__( 'Device serial Number' , 'nanosupport' ), 'bottom' ); ?>
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
                        <input type="text" class="ns-form-control" name="ns_ticket_serial_number" id="ns-ticket-serial-number" value="<?php echo !empty($_POST['ns_ticket_serial_number']) ? stripslashes_deep( $_POST['ns_ticket_serial_number'] ) : ''; ?>" aria-describedby="ns-serial-number" required>
						</div>
					</div> <!-- /.ns-form-group -->

                    <div class="ns-form-group">
						<label for="ns-ticket-issuse" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'Issues', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-ticket-issuse', esc_html__( 'Device Issuse/Defective part short description' , 'nanosupport' ), 'bottom' ); ?>
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
                        <input type="text" class="ns-form-control" name="ns_ticket_issuse" id="ns-ticket-issuse" value="<?php echo !empty($_POST['ns_ticket_issuse']) ? stripslashes_deep( $_POST['ns_ticket_issuse'] ) : ''; ?>" aria-describedby="ns-ticket-issuse" required>
						</div>
					</div> <!-- /.ns-form-group -->

					<!-- TICKET DETAILS -->
					<div class="ns-form-group">
						<label for="ns-ticket-details" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'Diagnosis and details', 'nanosupport' ); ?> <sup class="ns-required">*</sup>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
							<?php
							/**
							 * WP Editor array.
							 * Declare the array here, so that we can conditionally
							 * display tooltip content.
							 * @var array
							 * ...
							 */
							$wp_editor_array = array(
												'media_buttons'		=> false,
												'textarea_name'		=> 'ns_ticket_details',
												'textarea_rows'		=> 10,
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

							$character_limit = ns_is_character_limit();
							if( $character_limit ) {
								/* translators: character limit to the ticket content, in number */
								$content_tooltip_msg = sprintf( esc_html__( 'Write down your issue in diagnosis and details... At least %s characters is a must.', 'nanosupport' ), $character_limit );
								// allowed HTML tags are not necessary if rich text editor is disabled.
								if( $wp_editor_specs['tinymce'] != true ) {
									$content_tooltip_msg .= '<br><small>';
										/* translators: allowed HTML tags to the plugin */
										$content_tooltip_msg .= sprintf( __( '<strong>Allowed HTML Tags:</strong><br> %s', 'nanosupport' ), ns_get_allowed_html_tags() );
									$content_tooltip_msg .= '</small>';
								}

								echo ns_tooltip( 'ns-details', $content_tooltip_msg, 'bottom' );
							} else {
								$content_tooltip_msg = esc_html_e( 'Write down your issue in details...', 'nanosupport' );
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
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
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
					</div> <!-- /.ns-form-group -->

					<?php
					$NSECommerce = new NSECommerce();
					if( $NSECommerce->ecommerce_enabled() ) {
						$products = $NSECommerce->get_products();

						/**
					     * -----------------------------------------------------------------------
					     * HOOK : FILTER HOOK
					     * ns_mandate_product_fields
					     * 
					     * Hook to moderate the permission for mandating product-specifc fields,
					     * or not.
					     *
					     * @since  1.0.0
					     * -----------------------------------------------------------------------
					     */
						$mandate_product_fields = apply_filters( 'ns_mandate_product_fields', true );
						?>

						<!-- TICKET PRODUCTS -->
						<div class="ns-form-group">
							<label for="ns-ticket-product" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
								<?php esc_html_e( 'Product', 'nanosupport' ); ?>
								<?php if( $mandate_product_fields ) echo '<sup class="ns-required">*</sup>'; ?>
							</label>
							<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
								<?php echo ns_tooltip( 'ns-product', esc_html__( 'Select the product the ticket is about.', 'nanosupport' ), 'bottom' ); ?>
							</div>
							<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12 ns-form-inline">
								<?php $submit_val = !empty($_POST['ns_ticket_product']) ? $_POST['ns_ticket_product'] : ''; ?>
								<select class="ns-form-control" name="ns_ticket_product" id="ns-ticket-product" aria-describedby="ns-product" <?php if( $mandate_product_fields ) echo 'required'; ?>>
									<option value="" <?php selected( $submit_val, '' ); ?>><?php esc_html_e( 'Select a product', 'nanosupport' ); ?></option>
									<?php foreach($products as $id => $product_name) { ?>
								        <option value="<?php echo $id; ?>" <?php selected( $submit_val, $id ); ?>>
								        	<?php echo esc_html($product_name); ?>
								        </option>
								    <?php } ?>
								</select>
							</div>
						</div> <!-- /.ns-form-group -->

						<!-- TICKET PRODUCT RECEIPT -->
						<div class="ns-form-group">
							<label for="ns-ticket-product-receipt" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
								<?php esc_html_e( 'Purchase Receipt', 'nanosupport' ); ?>
								<?php if( $mandate_product_fields ) echo '<sup class="ns-required">*</sup>'; ?>
							</label>
							<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
								<?php echo ns_tooltip( 'ns-product-receipt', esc_html__( 'Enter the receipt number of purchasing the product.', 'nanosupport' ), 'bottom' ); ?>
							</div>
							<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12 ns-form-inline">
								<?php $submit_val = !empty($_POST['ns_ticket_product_receipt']) ? $_POST['ns_ticket_product_receipt'] : ''; ?>
								<input type="number" name="ns_ticket_product_receipt" class="ns-form-control" id="ns-ticket-product-receipt" aria-describedby="ns-product-receipt" value="<?php echo $submit_val; ?>" min="0" <?php if( $mandate_product_fields ) echo 'required'; ?>>
							</div>
						</div> <!-- /.ns-form-group -->

					<?php } // endif( $NSECommerce->ecommerce_enabled ) ?>

					<hr>

					<div class="ns-form-group">

						<h6 class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12 ns-text-center">
							<?php esc_html_e( 'Your internal references', 'nanosupport' ); ?>
						</h6>
					
						<label for="ns-ticket-internal-reference-number" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
							<?php esc_html_e( 'Request or reference number', 'nanosupport' ); ?>
						</label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-internal-reference-number', esc_html__( 'Request or reference number from your establishment.', 'nanosupport' ), 'bottom' ); ?>
						</div>
						<div class="ns-col-md-9 ns-col-sm-9 ns-col-xs-12">
                        <input type="text" class="ns-form-control" name="ns_ticket_internal_reference_number" id="ns-internal-reference-number" value="<?php echo !empty($_POST['ns_ticket_internal_reference_number']) ? stripslashes_deep( $_POST['ns_ticket_internal_reference_number'] ) : ''; ?>" aria-describedby="ns-internal-reference-number">
						</div>
					</div> <!-- /.ns-form-group -->

					<div class="ns-form-group">

						<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
							<label for="ns-ticket-internal-reference-establishment" class="ns-col-md-5 ns-col-sm-5 ns-col-xs-10 ns-control-label">
								<?php esc_html_e( 'Facility Name', 'nanosupport' ); ?>
							</label>
							<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
								<?php echo ns_tooltip( 'ns-internal-reference-establishment', esc_html__( 'The Facility Name, where the computer is assigned.', 'nanosupport' ), 'bottom' ); ?>
							</div>
							<input type="text" class="ns-form-control" name="ns_ticket_internal_reference_establishment" id="ns-internal-reference-establishment" value="<?php echo !empty($_POST['ns_ticket_internal_reference_establishment']) ? stripslashes_deep( $_POST['ns_ticket_internal_reference_establishment'] ) : ''; ?>" aria-describedby="ns-internal-reference-establishment">
						</div>
						<div class="ns-col-md-6 ns-col-sm-6 ns-col-xs-12">
							<label for="ns-ticket-internal-reference-name" class="ns-col-md-5 ns-col-sm-5 ns-col-xs-10 ns-control-label">
								<?php esc_html_e( 'Responsible for the RMA', 'nanosupport' ); ?>
							</label>
							<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
								<?php echo ns_tooltip( 'ns-ticket-internal-reference-name', esc_html__( 'The technician name Responsible for the RMA file.', 'nanosupport' ), 'bottom' ); ?>
							</div>
							<input type="text" class="ns-form-control" name="ns_ticket_internal_reference_name" id="ns-ticket-internal-reference-name" value="<?php echo !empty($_POST['ns_ticket_internal_reference_name']) ? stripslashes_deep( $_POST['ns_ticket_internal_reference_name'] ) : ''; ?>" aria-describedby="ns-ticket-internal-reference-name">
						</div>
				
					</div> <!-- /.ns-form-group -->

					<hr>

                    <div class="ns-form-group">
                        <label for="ns-retrun-adresse" class="ns-col-md-2 ns-col-sm-2 ns-col-xs-10 ns-control-label">
                            <?php esc_html_e( 'Alternative return Adresse', 'nanosupport' ); ?>
                        </label>
						<div class="ns-col-md-1 ns-col-sm-1 ns-col-xs-2 ns-text-center">
                            <?php echo ns_tooltip( 'ns-return-adresse', esc_html__( 'Use a alternative return adresse, if blank profil adresse will be used' , 'nanosupport' ), 'bottom' ); ?>
						</div>
						<div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12">
                            <?php $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true ); ?>
							<div class="ticket-retrun-adresse-button">
									<button type="button" id="clear_alternative_adresse" name="clear_alternative_adresse" class="disabled ns-btn ns-btn-primary ns-btn-alternative-adresse">
										<?php esc_html_e( 'Use account Adresse', 'nanosupport' ); ?>
									</button> 
									<button type="button" class="add_alternative_adresse ns-btn ns-btn-primary ns-btn-alternative-adresse">
										<?php esc_html_e( 'Edit Adresse', 'nanosupport' ); ?>
									</button> 
									<button type="button" class="select_alternative_adresse hide ns-btn ns-btn-primary ns-btn-alternative-adresse">
										<?php esc_html_e( 'Select Adresse', 'nanosupport' ); ?>
									</button> 
								</div>

							<div id="ticket-retrun-adresse-div"> <?php
							
							echo '<table class="ns-ticket-retrun-adresse-table" style="width:100%; border-bottom: 0;">';
							echo '<tbody><tr>
								<th>'. esc_html__( 'Select', 'nanosupport' ) .'</th>
								<th>'. esc_html__( 'Organization', 'nanosupport' ) .'</th>
								<th>'. esc_html__( 'Adresse', 'nanosupport' ) .'</th>
								<th>'. esc_html__( 'City', 'nanosupport' ) .'</th> 
								<th>'. esc_html__( 'Province', 'nanosupport' ) .'</th>
								<th>'. esc_html__( 'Postal code', 'nanosupport' ) .'</th>
								<th>'. esc_html__( 'Country', 'nanosupport' ) .'</th>
							</tr>';
                            if($check_alternative_adresse) {
                                $i = 0;
                                foreach ( $check_alternative_adresse as $adresses ) {
                                    echo '<tr>';
                                        echo '<th>';
                                            echo '<input type="radio" id="this_alternative_adresse" name="this_alternative_adresse" value="' . $i . '">';
                                        echo '</th>';
                                        foreach ( $adresses as $adresse ) {
                                            echo '<th>';
                                                echo $adresse;
                                            echo '</th>';
                                        }
                                    echo '</tr>';
                                    $i++;
                                }
							}
							echo '</tbody></table>';

							do_shortcode( '[nanosupport_alternative_adresse]' );
                            
						?></div>
                    </div> <!-- /.ns-form-group -->

                    <div class="ns-form-group">
                        <div class="ns-col-md-12 ns-col-sm-12 ns-col-xs-12">
							<div class="ns-form-submit">

								<span class="ns-text-dim ns-small">
									<?php
									echo esc_html_e( 'Il faut identifier le numéro de RMA sur la boite ou dans la boite pour avoir un suivi.', 'nanosupport' ) ; ?>
								</span>

								<span class="ns-text-dim ns-small">
									<?php
									echo esc_html_e( 'Il faut identifier le numéro de SO, PO ou Facture ainsi que le numéro de série pour obtenir un statut.', 'nanosupport' ) ; ?>
								</span>

								<span class="ns-text-dim ns-small">
									<?php
									echo esc_html_e( "Les RMA reste ouvert pendant un période 60 jours. Il seront ferme si ceux-ci n'ont pas de suivi.", 'nanosupport' ) ; ?>
								</span>

								<button type="submit" id="ns_submit" name="ns_submit" class="ns-btn ns-btn-primary ns-btn-ticket">
									<?php esc_html_e( 'Submit', 'nanosupport' ); ?> *
								</button>
								<?php if( is_user_logged_in() ) : ?>
									<span class="ns-text-dim ns-small">
										<?php
										$current_user = wp_get_current_user();
										echo esc_html_e( '* By clicking the submit button, you agree to our ', 'nanosupport' ) ; ?>
										
										<a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php esc_html_e('Terms of use', 'nanosupport') ?></a> & <a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php esc_html_e('Return Policy', 'nanosupport') ?></a>
									</span>
								<?php endif; ?>
							</div>
                        </div>
                    </div> <!-- /.ns-form-group -->

				</form> <!-- /.ns-form-horizontal -->

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