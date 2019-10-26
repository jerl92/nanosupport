<?php
/**
 * Shortcode: Support Desk
 *
 * Showing the common ticket center of all the support tickets to the respective privileges.
 * Show all the tickets at the front end using shortcode [nanosupport_desk]
 *
 * @author  	nanodesigns
 * @category 	Shortcode
 * @package 	NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ns_support_desk_page() {
	
	ob_start();

	echo '<div id="nanosupport-desk" class="ns-no-js">';
		if( is_user_logged_in() ) :
			//User is Logged in

			global $post, $current_user; ?>

			<?php
			/**
			 * -----------------------------------------------------------------------
			 * HOOK : ACTION HOOK
			 * nanosupport_before_support_desk
			 *
			 * To Hook anything before the Support Desk.
			 *
			 * @since  1.0.0
			 *
			 * 10	- ns_support_desk_navigation()
			 * -----------------------------------------------------------------------
			 */
			do_action( 'nanosupport_before_support_desk' );
			?>
			
			<?php
			if( ns_is_user('manager') ) {
				//Admin users
				$author_id 		= '';
				$ticket_status 	= array('publish', 'private', 'pending');
			} elseif( ns_is_user('agent') ) {
				//Agent
				$author_id 		= $current_user->ID;
				$ticket_status	= array('publish', 'private', 'pending');
			} else {
				//General users
				$author_id		= $current_user->ID;
				$ticket_status 	= array('private', 'pending');
			}

			$posts_per_page = get_option( 'posts_per_page' );
			$paged 			= ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

			if( ns_is_user('agent') ) {
				$meta_query = array(
				                    array(
				                        'key'     => '_ns_ticket_agent',
				                        'value'   => $current_user->ID,
				                        'compare' => '=',
				                    )
				                );
			} else {
				$meta_query = array('');
			}

			$status_terms = get_terms( array(
				'taxonomy' => 'nanosupport_status',
				'hide_empty' => false,
			) );
	
			$args = array(
						'post_type'			=> 'nanosupport',
						'post_status'		=> $ticket_status,
						'posts_per_page'	=> $posts_per_page,
						'author'			=> $author_id,
						'paged'				=> $paged,
						'meta_query'		=> $meta_query
					);

			if( isset( $_GET['orderby'] ) && "post_title" == $_GET['orderby'] ){
				$args['orderby'] = 'name';
				$args['order']  = 'ASC';
			} else if( isset( $_GET['orderby'] ) && "modified" == $_GET['orderby'] ){
				$args['orderby'] = 'modified';
				$args['order']  = 'DESC';
			} else if( isset( $_GET['orderby'] ) && "last_comment" == $_GET['orderby'] ){
				$args['posts_orderby'] = 'comment_count';
				$args['order'] = 'DESC';
			}

			if ( $status_terms ) {
				foreach ( $status_terms as $status_term ) {
					if( isset( $_GET['status'] ) && $status_term->slug == $_GET['status'] ){
						$args['meta_key'] = '_ns_ticket_status';
						$args['meta_value'] = $status_term->slug;
					} 
				}
			}

			if ( !isset( $_GET['status'] ) ) {
				$args['meta_key'] = '_ns_ticket_status';
				$args['meta_value'] = 'solved';
				$args['meta_compare'] = '!=';
			}

			add_filter( 'posts_clauses', 'ns_change_query_to_include_agents_tickets', 10, 2 );

				/**
				 * -----------------------------------------------------------------------
				 * HOOK : FILTER HOOK
				 * ns_filter_support_desk_query
				 *
				 * To alter the Support Desk query arguments.
				 *
				 * @since  1.0.0
				 * -----------------------------------------------------------------------
				 */
				$support_ticket_query = new WP_Query( apply_filters( 'ns_filter_support_desk_query', $args ) );

			remove_filter( 'posts_clauses', 'ns_change_query_to_include_agents_tickets', 10 );

			if( $support_ticket_query->have_posts() ) :

				//Get the NanoSupport Settings from Database
				$ns_general_settings = get_option( 'nanosupport_settings' );
				$highlight_choice    = isset($ns_general_settings['highlight_ticket']) ? $ns_general_settings['highlight_ticket'] : 'status';

				while( $support_ticket_query->have_posts() ) : $support_ticket_query->the_post();

					//Get ticket information
					$ticket_meta 	 = ns_get_ticket_meta( get_the_ID() );

					$term_list = wp_get_post_terms( $post->ID, 'nanosupport_status', array("fields" => "all"));
					$get_term_color = get_term_meta( $term_list[0]->term_id, 'meta_color', true);
					$get_term_hide_rma = get_term_meta( $term_list[0]->term_id, 'meta_hide_rma', true);

					$term_list_form_factor = wp_get_post_terms( $post->ID, 'nanosupport_form_factor', array("fields" => "all"));
					$lang_text_term = qtranxf_use(qtranxf_getLanguage(), $term_list_form_factor[0]->name);

					$highlight_class = 'priority' === $highlight_choice ? $ticket_meta['priority']['class'] : $ticket_meta['status']['class'];
					$meta_data_additional_status = get_post_meta( $post->ID, '_ns_internal_additional_status', true );

					$NSECommerce = new NSECommerce();
					$product_icon = '';
					if( $NSECommerce->ecommerce_enabled() ) {
						$product_info = $NSECommerce->get_product_info($ticket_meta['product'], $ticket_meta['receipt']);
						$product_icon = false !== $product_info ? '&nbsp;<i class="ns-icon-cart ns-small" aria-hidden="true"></i>' : '';
					}
					?>

					<?php if( 'solved' != $ticket_meta['status']['value'] ) { ?>

					<div class="ticket-cards ns-cards <?php echo esc_attr($highlight_class); ?>" style="border-color: <?php echo $get_term_color ?>">
						<div class="ns-row">
							<div class="ns-col-sm-4 ns-col-xs-12">
								<h3 class="ticket-head ticket-title-shadow">

									<?php if( 'pending' === $ticket_meta['status']['value'] ) : ?>
										<?php if( ns_is_user('agent_and_manager') ) : ?>
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" style="color: <?php echo $get_term_color; ?>">
												<?php the_title(); ?>
											</a>
										<?php else : ?>
											<?php the_title(); ?>
										<?php endif; ?>
									<?php else : ?>
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" style="color: <?php echo $get_term_color; ?>">
											<?php the_title(); ?>
										</a>
									<?php endif; ?>

									<?php if( ns_is_user('agent_and_manager') ) : ?>
										<span class="ticket-tools">
											<?php edit_post_link( 'Edit', '', '', get_the_ID() ); ?>
											<a class="ticket-view-link" href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php esc_attr_e( 'Permanent link to the Ticket', 'nanosupport' ); ?>">
												<?php _e( 'View', 'nanosupport' ); ?>
											</a>
										</span> <!-- /.ticket-tools -->
									<?php endif; ?>
								</h3>

								<?php $get_rma_number = get_post_meta( get_the_ID(), 'ns_internal_rma_number', true );

								if ( $get_rma_number && $get_term_hide_rma == 0 ) { ?>
								<div class="text-blocks">
										<strong><?php _e( 'RMA Number', 'nanosupport' ); ?>:</strong>
										<?php echo esc_attr( $get_rma_number ); ?>
								</div>
								<?php } //endif ?>

								<div class="text-blocks">
									<strong><?php _e( 'Inovice Number', 'nanosupport' ); ?>:</strong>
									<?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_inovice_number', true )); ?>
								</div>

								<div class="text-blocks">
									<strong><?php _e( 'Component type', 'nanosupport' ); ?>: </strong><?php echo $lang_text_term; ?>
								</div>
								
								<div class="text-blocks">
									<strong><?php _e( 'Device serial Number', 'nanosupport' ); ?>: </strong><?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_serial_number', true )); ?>
								</div>
								
								<div class="text-blocks">
									<strong><?php _e( 'Issues', 'nanosupport' ); ?>: </strong><?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_issuse', true )); ?>
								</div>

							</div>

							<div class="ns-col-sm-8 ns-col-xs-12 ticket-meta">
								<div class="text-blocks">
									<strong><?php _e( 'Ticket Status:', 'nanosupport' ); ?></strong></br>
									<?php echo $ticket_meta['status']['label']; ?>
									<?php if ($meta_data_additional_status != '') { ?>
										<span class="ns-label ns-label-status-additional">
											<?php echo $meta_data_additional_status; ?>
										</span>
									<?php } //endif ?>
								</div>
							</div>

							<div class="ns-col-sm-4 ns-col-xs-12 ticket-meta">

								<?php $get_term_shipping = get_term_meta($term_list[0]->term_id, 'meta_shipping', true);
								if ( $get_term_shipping == 1 ) { ?>		

									<div class="text-blocks">
										<?php if ( get_post_meta( get_the_ID(), '_ns_ticket_traking_number', true ) != '' ) : ?>
											<strong><p><?php _e( 'Traking Number', 'nanosupport' ); ?>:</p></strong><br>
											<?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_traking_number', true )); ?>
										<?php endif; ?>										
									</div>

								<?php } elseif ( $get_term_shipping == 2 ) { ?>

									<div class="text-blocks">	
										<strong><?php _e( 'Need a pickup', 'nanosupport' ); ?>?</strong><br>
									</div>

								<?php } ?>

								<?php $get_internal_reference_number = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_number', true ); 
								$get_internal_reference_establishment = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_establishment', true ); 
								$get_internal_reference_name = get_post_meta( get_the_ID(), '_ns_ticket_internal_reference_name', true ); ?>

								<div class="text-blocks">
									<strong><?php _e( 'Your internal references', 'nanosupport' ); ?></strong>
								</div>

								<?php if ( $get_internal_reference_number ) { ?>
									<div class="text-blocks">
											<strong><?php _e( 'Request or reference number', 'nanosupport' ); ?>:</strong>
											<?php echo esc_attr( $get_internal_reference_number ); ?>
									</div>
								<?php } //endif ?>

								<?php if ( $get_internal_reference_name ) { ?>
									<div class="text-blocks">
											<strong><?php _e( 'Responsible for the RMA', 'nanosupport' ); ?>:</strong>
											<?php echo esc_attr( $get_internal_reference_name ); ?>
									</div>
								<?php } //endif ?>

								<?php if ( $get_internal_reference_establishment ) { ?>
									<div class="text-blocks">
											<strong><?php _e( 'Facility Name', 'nanosupport' ); ?>:</strong>
											<?php echo esc_attr( $get_internal_reference_establishment ); ?>
									</div>
								<?php } //endif ?>

							</div>

							<div class="toggle-ticket-additional">
								<i class="ns-toggle-icon ns-icon-chevron-circle-down" title="<?php esc_attr_e( 'Load more', 'nanosupport' ); ?>"></i>
							</div>
							<div class="ticket-additional">
								<ticket-cards ns-cards priority-lowdiv class="ns-col-sm-3 ns-col-xs-4 ticket-meta">
									<div class="text-blocks">
										<strong><?php _e( 'Created &amp; Updated:', 'nanosupport' ); ?></strong><br>
										<?php echo date( 'd M Y h:i A', strtotime( $post->post_date ) ); ?><br>
										<?php echo date( 'd M Y h:i A', strtotime( ns_get_ticket_modified_date($post->ID) ) ); ?>
									</div>
									<div class="text-blocks">
										<strong><?php _e( 'Responses:', 'nanosupport' ); ?></strong><br>
										<?php
										$response_count = wp_count_comments( get_the_ID() );
										echo '<span class="responses-count">'. $response_count->approved .'</span>';
										?>
									</div>
										<div class="text-blocks">
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
								</div>
								 <!--	<div class="text-blocks">
										<strong><?php _e( 'Responses:', 'nanosupport' ); ?></strong><br>
										<?php
										$response_count = wp_count_comments( get_the_ID() );
										echo '<span class="responses-count">'. $response_count->approved .'</span>';
										?>
									</div>  -->
								</div> <!-- /.ticket-additional -->
						</div> <!-- /.ns-row -->
					</div> <!-- /.ticket-cards -->

					<?php } else { ?>

						<div class="ticket-cards ns-cards <?php echo esc_attr($highlight_class); ?>" style="border-color: <?php echo $get_term_color ?>">
						<div class="ns-row">
							<div class="ns-col-sm-4 ns-col-xs-12">
								<h3 class="ticket-head">
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" style="color: <?php echo $get_term_color ?>">
											<?php the_title(); ?>	
										</a>
									
									<?php if( ns_is_user('agent_and_manager') ) : ?>
										<span class="ticket-tools">
											<?php edit_post_link( 'Edit', '', '', get_the_ID() ); ?>
											<a class="ticket-view-link" href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php esc_attr_e( 'Permanent link to the Ticket', 'nanosupport' ); ?>">
												<?php _e( 'View', 'nanosupport' ); ?>
											</a>
										</span> <!-- /.ticket-tools -->
									<?php endif; ?>
								</h3>
								<p><strong><?php _e( 'Device serial Number', 'nanosupport' ); ?>: </strong><?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_serial_number', true )); ?></p>
							</div>
							<div class="ns-col-sm-2 ns-col-xs-12">

								<?php $get_rma_number = get_post_meta( get_the_ID(), 'ns_internal_rma_number', true );

								 if ( $get_rma_number ) { ?>
									<div class="text-blocks">
											<strong><?php _e( 'RMA Number', 'nanosupport' ); ?>:</strong></br>
											<?php echo esc_attr( get_post_meta( get_the_ID(), 'ns_internal_rma_number', true )); ?>
									</div>
								<?php } //endif ?>
							</div>
							<div class="ns-col-sm-3 ns-col-xs-4 ticket-meta">
								<div class="text-blocks">
										<strong><?php _e( 'Issuse', 'nanosupport' ); ?>:</strong></br>
										<?php echo esc_attr( get_post_meta( get_the_ID(), '_ns_ticket_issuse', true )); ?>
								</div>
							</div>

							<div class="ns-col-sm-3 ns-col-xs-4 ticket-meta">
								<div class="text-blocks">
									<div class="text-blocks">
										<strong><?php _e( 'Ticket Status:', 'nanosupport' ); ?></strong><br>
										<?php echo $ticket_meta['status']['label']; ?>
										<?php if ($meta_data_additional_status != '') { ?>
											<span class="ns-label ns-label-status-additional">
												<?php echo $meta_data_additional_status; ?>
											</span>
										<?php } //endif ?>
									</div>
								</div> <!-- /.ticket-additional -->
							</div>
						</div> <!-- /.ns-row -->
					</div> <!-- /.ticket-cards -->

					<?php }
				endwhile;


				/**
				 * Pagination
				 * @see  includes/helper-functions.php
				 */
				ns_pagination( $support_ticket_query );

			else :
				echo '<div class="ns-alert ns-alert-info" role="alert">';
					esc_html_e( 'Nice! You do not have any RMA file to display.', 'nanosupport' );
				echo '</div>';
			endif;
			wp_reset_postdata();

		else :
			//User is not logged in
			esc_html_e( 'Sorry, you cannot see your RMA file without being logged in.', 'nanosupport' );
			echo '<br>';
			echo '<a class="ns-btn ns-btn-default ns-btn-sm" href="'. wp_login_url() .'"><i class="ns-icon-lock"></i>&nbsp;';
				esc_html_e( 'Login', 'nanosupport' );
			echo '</a>&nbsp;';
			/* translators: context: login 'or' register */
			esc_html_e( 'or', 'nanosupport' );
			echo '&nbsp;<a class="ns-btn ns-btn-default ns-btn-sm" href="'. wp_registration_url() .'"><i class="ns-icon-lock"></i>&nbsp;';
				esc_html_e( 'Create an account', 'nanosupport' );
			echo '</a>';

		endif; //if( is_user_logged_in() )

		/**
		 * -----------------------------------------------------------------------
		 * HOOK : ACTION HOOK
		 * nanosupport_after_support_desk
		 * 
		 * To Hook anything after the Support Desk.
		 *
		 * @since  1.0.0
		 * -----------------------------------------------------------------------
		 */
		do_action( 'nanosupport_after_support_desk' );
		
	echo '</div> <!-- /#nanosupport-desk -->';
	
	return ob_get_clean();
}

add_shortcode( 'nanosupport_desk', 'ns_support_desk_page' );