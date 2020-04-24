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

                        <div class="ticket-responses-widget"><?php

                            if ( is_user_logged_in() ) {

                                $current_user = wp_get_current_user();

                                if( current_user_can('administrator') || current_user_can('ticket-agent') ) {
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

                                $loop = new WP_Query($query);

                                $i = 0;
                                $y = 0;
                                $z = 0;

                                if ( $loop->have_posts() ) {
                                    while ( $loop->have_posts() ) : $loop->the_post();
                                        if( 0 < get_comments_number(get_the_id()) && 6 > $i  ) {
                                            $html[$i] = get_the_id();
                                            $i++; 
                                        }
                                    endwhile;
                                } else {
                                    $html[$i] = null;
                                }

                                wp_reset_postdata();

                                foreach($html as $postid) {
                                    $args = array(
                                        'post_id'       =>  $postid,
                                        'type'          => 'nanosupport_response',
                                        'post_type' 	=> 'nanosupport',
                                        'status'    	=> 'approve',
                                        'orderby'       => 'comment_date_gmt',
                                        'order'         => 'DESC',
                                        'number'        => '1'
                                    );

                                    $comments[$x] = get_comments($args);

                                    foreach ( $comments as $comment ) {
                                            $comment_dates[$y]['time'] = get_comment_date( 'U', $comment[0]->comment_ID );
                                            $comment_dates[$y]['id'] = $comment[0]->comment_post_ID;
                                            $y++;
                                    }

                                }
                                
                                rsort( $comment_dates );	        
                                
                                array_unique( $comment_dates['id'] );       

                                foreach ( $comment_dates as $postid ) {

                                    $ticket_meta = ns_get_ticket_meta( $postid['id'] );
                                    
                                    if ( $postid['id'] != null ) {
                                        $args = array(
                                            'post_id'       =>  $postid['id'],
                                            'type'          => 'nanosupport_response',
                                            'post_type' 	=> 'nanosupport',
                                            'status'    	=> 'approve',
                                            'order'     	=> 'DESC',
                                            'number'        => '3',
                                            'orderby'       => 'comment_date_gmt'
                                        );

                                        $comments = get_comments($args);
                                    } else {
                                        $comments = null;
                                    }

                                    $found_count = count(  $comments );

                                    if ($comments != null) {

                                        $term_list = wp_get_post_terms( $postid['id'], 'nanosupport_status', array("fields" => "all"));
                                        $get_term_color = get_term_meta( $term_list[0]->term_id, 'meta_color', true);

                                        $ticket_issuse = get_post_meta( $postid['id'], '_ns_ticket_issuse', true );

                                        $ticket_serial_number = get_post_meta( $postid['id'], '_ns_ticket_serial_number', true ); ?>

                                        <div class="ticket-cards-widget" style="background-color: rgba(115, 109, 109, 0.1); border-radius: 10px; box-shadow: 10px 15px 15px rgba(0,0,0,0.25); ">

                                        <h3 style="margin: 10px 15px 0 15px; padding-top: 10px;"><a class="ticket-title-shadow" style="color:<?php echo $get_term_color ?>;" href="<?php echo get_post_permalink($postid['id']) ?>"><?php echo get_the_title($postid['id']) ?></a><div style="float: right;"><?php echo $ticket_meta['status']['label'] ?></a></h3>
                                        <h4 style="margin: 10px 15px 0 15px;"><p style="margin: 0 5px 5px 0;"><?php echo $ticket_serial_number ?></p><p style="margin: 0 5px 5px 0;">RMA #<?php echo $postid['id'] ?></p><p style="margin: 0 5px 5px 0;"><?php echo $ticket_issuse ?></p></h4>

                                        <?php foreach ($comments as $comment ) {

                                            $fresh_response = (isset($_GET['ns_success']) || isset($_GET['ns_cm_success']) ) && $found_count == $counter ? 'new-response' : ''; ?>

                                            <div class="ticket-response-cards ns-cards <?php $fresh_response ?>">
                                            <div class="ns-row">
                                                <div class="ns-col-sm-6">
                                                <div class="response-head">
                                                <h3 class="ticket-head" id="response-<?php $fresh_response ?>">
                                                <?php echo $comment->comment_author; ?>
                                                            </h3>
                                                            </div> <!-- /.response-head -->
                                                            </div>
                                                            <div class="ns-col-sm-6 response-dates">
                                                            <?php echo date( 'd M Y h:iA', strtotime( $comment->comment_date ) ); ?>
                                                    </div>
                                                </div> <!-- /.ns-row -->
                                                <div class="ticket-response">
                                                    <?php echo wpautop( $comment->comment_content ); ?>
                                                </div>
                                            
                                                </div> <!-- /.ticket-response-cards -->

                                            <?php $counter++; 
                                        
                                        } ?>

                                        </div>

                                    <?php } 
                                
                                }
                                
                            } ?>

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