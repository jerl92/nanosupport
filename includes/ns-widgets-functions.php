<?php
/**
 * ns_comment Widget Class
 */
class ns_comment_widget extends WP_Widget {
 
 
    /** constructor -- name this the same as the class above */
    function ns_comment_widget() {
        parent::WP_Widget(false, $name = 'ns_comment Text Widget');	
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $message 	= $instance['message'];
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>

                        <div class="ticket-responses-widget">
                            <?php
                            /**
                             * Responses
                             * Load all the responses that are denoted to the ticket.
                             */

                            $current_user = wp_get_current_user();

                            if( current_user_can('editor') || current_user_can('administrator') || current_user_can('ticket-agent') ) { 
                                $query = array(
                                    'post_type' 	=> 'nanosupport',
                                    'posts_per_page' => -1,
                                    'order'     	=> 'DESC',
                                    'orderby'       => 'modified',
                                    'comment_count' => array(
                                        array(
                                            'value' => 0,
                                            'compare' => '!=',
                                        ),
                                    )
                                );
                            } else {
                                $query = array(
                                    'post_type' 	=> 'nanosupport',
                                    'posts_per_page' => -1,
                                    'author'        =>  $current_user->ID,
                                    'order'     	=> 'DESC',
                                    'orderby'       => 'modified',
                                    'comment_count' => array(
                                        array(
                                            'value' => 0,
                                            'compare' => '!=',
                                        ),
                                    )
                                );
                            }

                            $i = 0;
                            $loop = new WP_Query($query);

                            if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();
                                    global $post;

                                    $args = array(
                                        'post_id'        => get_the_id(),
                                        'post_type' 	=> 'nanosupport',
                                        'status'    	=> 'approve',
                                        'order'     	=> 'DESC',
                                        'number'        => '3',
                                        'orderby'       => 'comment_date_gmt'
                                    );
                                    $response_array = get_comments( $args );

                                    $found_count = count($response_array);

                                    if( $response_array && 3 > $i ) {

                                        $term_list = wp_get_post_terms( get_the_id(), 'nanosupport_status', array("fields" => "all"));
                                        $get_term_color = get_term_meta( $term_list[0]->term_id, 'meta_color', true);

                                        $ticket_issuse = get_post_meta( get_the_id(), '_ns_ticket_issuse', true );
                                        
                                        $ticket_meta = ns_get_ticket_meta( get_the_ID() );

                                        ?> <div class="ticket-cards-widget" style="background-color: rgba(115, 109, 109, 0.1); border-radius: 10px; box-shadow: 10px 15px 15px rgba(0,0,0,0.25); "> <?php

                                        ?> <h3 style="margin: 10px 15px 0 15px; padding-top: 10px;"><a class="ticket-title-shadow" style="color:<?php echo $get_term_color ?>" href="<?php echo get_post_permalink(get_the_id()) ?>"><?php echo get_the_title(get_the_id()) ?></a><div style="float: right;"><?php echo $ticket_meta['status']['label'] ?></div></h3>
                                        <h4 style="margin: 10px 15px 0 15px;">RMA #<?php echo get_the_id() ?><p style="margin: 0 5px 5px 0;"><?php echo $ticket_issuse ?></p></h4> <?php

                                        $counter = 1;
        
                                        foreach( $response_array as $response ) {
                                        // echo print_r($response);


                                            //highlight the latest response on successful submission of new response
                                            $fresh_response = (isset($_GET['ns_success']) || isset($_GET['ns_cm_success']) ) && $found_count == $counter ? 'new-response' : '';
                                        // echo print_r( $response );
                                            ?>

                                                <div class="ticket-response-cards ns-cards <?php echo esc_attr($fresh_response); ?>">
                                                    <div class="ns-row">
                                                        <div class="ns-col-sm-6">
                                                            <div class="response-head">
                                                                <h3 class="ticket-head" id="response-<?php echo esc_attr($counter); ?>">
                                                                    <?php echo $response->comment_author; ?>
                                                                </h3>
                                                            </div> <!-- /.response-head -->
                                                        </div>
                                                        <div class="ns-col-sm-6 response-dates">
                                                            <a href="#response-<?php echo esc_attr($counter); ?>" class="response-bookmark ns-small"><strong class="ns-hash">#</strong> <?php echo date( 'd M Y h:iA', strtotime( $response->comment_date ) ); ?></a>
                                                        </div>
                                                    </div> <!-- /.ns-row -->
                                                    <div class="ticket-response">
                                                        <?php echo wpautop( $response->comment_content ); ?>
                                                    </div>
                                                    
                                                </div> <!-- /.ticket-response-cards -->

                                                <?php
                                            $counter++;
                                        }
                                        ?> </div> <?php
                                        $i++;
                                    }
                                endwhile;
                                         
                            } else {
                                echo esc_html__( 'No reponse found', 'nanosupport' );
                            }
                        
                            wp_reset_postdata(); ?>

                        </div>

              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
 
        $title 		= esc_attr($instance['title']);
        $message	= esc_attr($instance['message']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('Simple Message'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo $message; ?>" />
        </p>
        <?php 
    }
 
 
} // end class ns_comment_widget
add_action('widgets_init', create_function('', 'return register_widget("ns_comment_widget");'));
?>