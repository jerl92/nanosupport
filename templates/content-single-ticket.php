<?php
/**
 * Single Ticket Content
 *
 * Content of Ticket Details page - the single template's content.
 *
 * This template can be overridden by copying it to:
 * your-theme/nanosupport/content-single-ticket.php
 *
 * Template Update Notice:
 * However on occasion NanoSupport may need to update template files, and
 * the theme developers will need to copy the new files to their theme to
 * maintain compatibility.
 *
 * Though we try to do this not very often, but it does happen. And the
 * version below will reflect any changes made to the template file. And
 * for any major changes the Upgrade Notice will inform you pointing this.
 *
 * @author      nanodesigns
 * @category    Content
 * @package     NanoSupport/Templates/
 * @version     1.0.0z
 */
?>

<?php
//Get the NanoSupport Settings from Database
$ns_general_settings = get_option( 'nanosupport_settings' );

global $post;
$author 			= get_user_by( 'id', $post->post_author );
$support_desk 		= $ns_general_settings['support_desk'];
$highlight_choice	= isset($ns_general_settings['highlight_ticket']) ? $ns_general_settings['highlight_ticket'] : 'status';
$meta_data_additional_status = get_post_meta( $post->ID, '_ns_internal_additional_status', true );
$get_internal_reference_number = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_number', true );
$ticket_meta 		= ns_get_ticket_meta( get_the_ID() );

$highlight_class = 'priority' === $highlight_choice ? $ticket_meta['priority']['class'] : $ticket_meta['status']['class'];

$get_ns_ticket_status = get_post_meta( get_the_ID(), '_ns_ticket_status', true );
$term_list = wp_get_post_terms( $post->ID, 'nanosupport_status', array("fields" => "all"));
$get_term_color = get_term_meta( $term_list[0]->term_id, 'meta_color', true);
$get_term_hide_rma = get_term_meta( $term_list[0]->term_id, 'meta_hide_rma', true);

$term_list_form_factor = wp_get_post_terms( $post->ID, 'nanosupport_form_factor', array("fields" => "all"));
$lang_name_form_factor = qtranxf_use(qtranxf_getLanguage(), $term_list_form_factor[0]->name);

$get_internal_reference_number = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_number', true ); 
$get_internal_reference_establishment = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_establishment', true ); 
$get_internal_reference_name = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_name', true );

?>

<div class="content">

<div class="<?php echo esc_attr($highlight_class); ?>" style="padding-top:25px; border-color: <?php echo $get_term_color; ?>">

<div id="primary" class="sidebar-right clearfix">

<div class="ht-container">
	
<div class="inner-content grid-x grid-margin-x grid-padding-x">

<main class="main small-12 large-8 medium-8 cell" role="main">

<section id="content" role="main">

	<?php if ( $ticket_meta['status']['value'] == 'pending' && !ns_is_user('agent_and_manager') ) : ?>

	<?php get_template_part( 'content', 'none' ); ?>

	<?php else : ?>

	<article id="ticket-<?php echo $post->ID; ?>" <?php post_class('ns-single'); ?>>

		<div class="ticket-question-card ns-cards <?php echo esc_attr($highlight_class); ?> " style="border-color: <?php echo $get_term_color ?>">
			<div class="logo-on-print">
				<div style="float:left; width: 65%; padding-top: 5px;">
					<img alt="<?php bloginfo( 'name' ); ?>" src="https://sunterrapc.com/rma/wp-content/uploads/2017/04/logo.png" />
					<div style="font-weight: 600; padding-top: 5px;">https://sunterrapc.com</div>
				</div>
				<div style="float:right; width: 35%; text-align: right;">
					2980 Avenue Watt #10<br>
					Québec, QC, Canada<br>
					G1X 4A6<br>
					1-888-650-5965
				</div>
			</div>
			<div class="ns-row">
				<div class="ns-col-sm-11 ns-col-xs-11 on-print-wide">
					<div class="ticket-head-wrapper">
						<div class="ns-row ticket-meta">
							<div class="ns-col-sm-12 ns-col-xs-12">
								<h1 class="ticket-head ticket-head-title <?php echo esc_attr($highlight_class); ?>-title" style="border-color: <?php echo $get_term_color ?>">
									<span class="ticket-title-shadow" style="color: <?php echo $get_term_color ?>"><?php the_title(); ?> <span style="font-size: 25px;font-weight: 500;"><?php echo $lang_name_form_factor; ?></span></span>
									<div class="ticket-head-status">
										<?php echo $ticket_meta['status']['label']; ?>
										<?php if ($meta_data_additional_status != '') { ?>
											<span class="ns-label ns-label-status-additional">
												<?php echo $meta_data_additional_status; ?>
											</span>
										<?php } //endif ?>
									</div>
								</h1>

								<div class="ticket-head-issuse">
									<div class="ticket-head-sn"><strong><?php _e( 'Device serial Number', 'nanosupport' ); ?>: </strong><?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_serial_number', true )); ?></div>
									<span style="font-size: 20px; float: right; font-weight: 600">
										<?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_issuse', true )); ?>
									</span>	
								</div>

							</div>

						</div>
					</div>

					<div class="ns-row ticket-meta">
						<div class="ns-col-sm-6 ns-col-xs-6">
							<p>
								<strong><?php _e( 'RMA Number', 'nanosupport' ); ?>:</strong>
								<?php if ($get_term_hide_rma == 0) { 
									echo esc_attr( get_post_meta( get_the_ID(), 'ns_internal_rma_number', true ));
								} ?>
								</br>
								<?php _e( 'Il faut identifier le numéro de RMA sur la boite ou dans la boite pour un suivi.', 'nanosupport' ); ?>
							</p>
						</div>
						<div class="ns-col-sm-6 ns-col-xs-6">
							<p>
								<strong><?php _e( 'Inovice Number', 'nanosupport' ); ?>:</strong>
								<?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_inovice_number', true )); ?>
							</p>
						</div>
					</div>

					<div class="ns-row ticket-meta">
						<div class="ns-col-sm-3 ns-col-xs-6">
							<p>
								<strong><?php _e( 'Created', 'nanosupport' ); ?>:</strong><br>
								<span class="ns-small"><?php echo date( 'd M Y h:iA', strtotime( $post->post_date ) ); ?></span>
							</p>
						</div>
						<div class="ns-col-sm-3 ns-col-xs-6">
							<p>
								<strong><?php _e( 'Updated', 'nanosupport' ); ?>:</strong><br>
								<span class="ns-small"><?php echo date( 'd M Y h:iA', strtotime( ns_get_ticket_modified_date($post->ID) ) ); ?></span>
							</p>
						</div>

						<div class="ns-col-sm-3 ns-col-xs-6">
							<p>
								<strong><?php _e( 'Responses:', 'nanosupport' ); ?></strong><br>
								<?php
								$response_count = wp_count_comments( get_the_ID() );
								echo '<span class="responses-count">'. $response_count->approved .'</span>';
								?>
							</p>
						</div>
						<div class="ns-col-sm-3 ns-col-xs-6">
							<p>
								<strong><?php _e( 'Last Replied by:', 'nanosupport' ); ?></strong><br>
								<?php
								$last_response  = ns_get_last_response();
								$last_responder = get_userdata( $last_response['user_id'] );
								if ( $last_responder ) {
									echo $last_responder->display_name, '<br>';
									echo '<small>';
										/* translators: time difference from current time. eg. 12 minutes ago */
										printf( __( '%s ago', 'nanosupport' ), human_time_diff( strtotime($last_response['comment_date']), current_time('timestamp') ) );
									echo '</small>';
								} else {
									echo '-';
								}
								?>
							</p>
						</div>
					</div>

					<div class="ns-row ticket-meta" style="border-top: 0.5px solid rgba(0,0,0,.1); padding: 10px 0 10px 0;">
				
						<div class="ns-col-sm-6 ns-col-xs-11">
							<?php $user = wp_get_current_user(); ?>
							<?php $author = get_user_by( 'id', $post->post_author ); ?>

							<?php if ( in_array( 'administrator', (array) $user->roles ) || in_array( 'ticket-agent', (array) $user->roles ) ) { ?>
								<div class="ticket-author">
									<?php echo '<i class="ns-icon-user"></i> '. $author->display_name; ?></br>
								</div>																			
							<?php } ?>
						</div>

						<div class="ns-col-sm-6 ns-col-xs-11">
							<?php if ( in_array( 'administrator', (array) $user->roles ) || in_array( 'ticket-agent', (array) $user->roles ) ) { ?>
								<div class="ticket-author">
									<?php echo '<i class="ns-icon-users"></i> '. get_user_meta($author->ID, 'company_name', true); ?>
								</div>																			
							<?php } ?>
						</div>

					</div>
					
					<div class="ns-row ticket-meta" style="border-top: 0.5px solid rgba(0,0,0,.1);">
						<div class="ns-col-sm-4 ns-col-xs-4">
								<p>
									<strong><?php _e( 'Request or reference number', 'nanosupport' ); ?>:</strong><br>
									<?php if ( $get_internal_reference_number ) { ?>
										<?php echo esc_attr( $get_internal_reference_number ); ?>
									<?php } //endif ?>
								</p>
						</div>
						
						<div class="ns-col-sm-4 ns-col-xs-4">
								<p>
									<strong><?php _e( 'Facility Name', 'nanosupport' ); ?>:</strong><br>
									<?php if ( $get_internal_reference_establishment ) { ?>
										<?php echo esc_attr( $get_internal_reference_establishment ); ?>
									<?php } //endif ?>
								</p>
						</div>
						
						<div class="ns-col-sm-4 ns-col-xs-4">
								<p>
									<strong><?php _e( 'Responsible for the RMA', 'nanosupport' ); ?>:</strong><br>
									<?php if ( $get_internal_reference_name ) { ?>
										<?php echo esc_attr( $get_internal_reference_name ); ?>
									<?php } //endif ?>
								</p>
						</div>

					</div> <!-- /.ns-row -->

				</div> <!-- /.ns-row -->
				<div class="ns-col-sm-1 ns-right-portion">
					<a class="ns-btn ns-btn-default ns-btn-xs ns-round-btn ticket-link-btn" href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php esc_attr_e('Permanent link to the Ticket', 'nanosupport'); ?>">
						<i class="dashicons dashicons-paperclip"></i> <span class="screen-reader-only"><?php esc_attr_e('Permanent link to the Ticket', 'nanosupport'); ?></span>
					</a>
					<?php edit_post_link( '<i class="dashicons dashicons-edit" title="'. esc_attr__('Edit the Ticket', 'nanosupport') .'"></i> <span class="screen-reader-only">'. esc_attr__('Edit the Ticket', 'nanosupport') .'</span>', '', '', get_the_ID() ); ?>
				</div>

				<div class="ns-col-sm-11 ns-col-xs-11 on-print-wide" style="border-top: 0.5px solid rgba(0,0,0,.1);">
					<div class="ns-row ticket-meta">
						<?php 
							$get_term_shipping = get_term_meta($term_list[0]->term_id, 'meta_shipping', true); ?>
							<div class="ns-col-sm-6 ns-col-xs-6">
							<?php if ( $get_term_shipping == 1 ) { ?>					
								<p>
									<strong><?php _e( 'Tracking Number', 'nanosupport' ); ?>:</strong><br>
									<?php if ( get_post_meta( get_the_ID(), '_ns_ticket_traking_number', true ) ) : ?>
										<?php echo get_post_meta( get_the_ID(), '_ns_ticket_traking_number', true ); ?>
									<?php endif; ?>
								</p>
							<?php } ?>
							</div>

							<div class="ns-col-sm-6 ns-col-xs-6">
								<p>
									<strong><?php _e( 'Return adresse', 'nanosupport' ); ?>:</strong><br>
									<?php echo nl2br( get_post_meta( get_the_ID(), '_ns_ticket_return_adresse', true )); ?>
								</p>
							</div>
					</div> <!-- /.ns-row -->
				</div>
					
			</div> <!-- /.ns-row -->
			<div class="ticket-question">
				<?php the_content(); ?>
			</div>
		</div> <!-- /.ticket-question-card -->


		<!-- +++++++++++++++++++ RESPONSES +++++++++++++++++++ -->


		<div class="ticket-responses">
			<?php
			/**
			 * Responses
			 * Load all the responses that are denoted to the ticket.
			 */
			$args = array(
		        'post_id'   	=> get_the_ID(),
		        'post_type' 	=> 'nanosupport',
		        'status'    	=> 'approve',
		        'order'     	=> 'ASC'
		    );
		    $response_array = get_comments( $args );

		    $found_count = count($response_array);

			if( $response_array ) {

				echo '<div class="ticket-separator ticket-separator-center ns-text-uppercase">'. __('Responses', 'nanosupport') .'</div>';

				$counter = 1;


		        foreach( $response_array as $response ) {
					
				//highlight the latest response on successful submission of new response
	        	$fresh_response = (isset($_GET['ns_success']) || isset($_GET['ns_cm_success']) ) && $found_count == $counter ? 'new-response' : '';
	        	?>

					<div class="ticket-response-cards ns-cards <?php echo esc_attr($fresh_response); ?>">
						<div class="ns-row">
							<div class="ns-col-sm-9">
								<div class="response-head">
									<h3 class="ticket-head" id="response-<?php echo esc_attr($counter); ?>">
										<?php echo $response->comment_author; ?>
									</h3>
								</div> <!-- /.response-head -->
							</div>
							<div class="ns-col-sm-3 response-dates">
								<a href="#response-<?php echo esc_attr($counter); ?>" class="response-bookmark ns-small"><strong class="ns-hash">#</strong> <?php echo date( 'd M Y h:iA', strtotime( $response->comment_date ) ); ?></a>
							</div>
						</div> <!-- /.ns-row -->
						<div class="ticket-response">
							<?php echo wpautop( $response->comment_content ); ?>
						</div>
						
					</div> <!-- /.ticket-response-cards -->

					<?php
		        $counter++;
		        } //endforeach ?>
		    <?php } //endif ?>


		    <!-- ++++++++++++ NEW RESPONSE FORM ++++++++++++ -->

		    <?php get_nanosupport_response_form(); ?>

		<a class="ns-btn ns-btn-sm ns-btn-default single-ticket" href="<?php echo esc_url(get_permalink( $support_desk )); ?>"><i class="ns-icon-chevron-up"></i> <?php _e( 'Back to ticket index', 'nanosupport' ); ?></a>

		</br>

		</div> <!-- /.ticket-responses -->

		</article> <!-- /#ticket-<?php the_ID(); ?> -->

		<?php endif ; ?>

	</section>

	</main>

	<?php get_sidebar(); ?>

	</div>

	</div>

	</div>

	</div>

</div>

</div>