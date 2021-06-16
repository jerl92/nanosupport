<?php

 
function myprefix_custom_cron_schedule( $schedules ) {
    $schedules['every_six_hours'] = array(
        'interval' => 21600, // Every 6 hours
        'display'  => __( 'Every 6 hours' ),
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'myprefix_custom_cron_schedule' );

///Hook into that action that'll fire every six hours
add_action( 'myprefix_cron_hook', 'myprefix_cron_function' );

//Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'myprefix_cron_hook' ) ) {
    wp_schedule_event( time(), 'every_six_hours', 'myprefix_cron_hook' );
}

//create your function, that runs on cron
function myprefix_cron_function() {

    $args = array(
        'post_type' => 'nanosupport',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    
    $posts = get_posts( $args );

    foreach ($posts as $post) {
        $ticket_meta = ns_get_ticket_meta( $post->ID );
        if ( strtotime($post->post_modified) <= strtotime(date('Y-m-d', strtotime('-60 days')))) {
            if( 'solved' != $ticket_meta['status']['value'] ) {
                if( 'resolu-faute-dinactivite' != $ticket_meta['status']['value'] ) {
                    wp_set_object_terms($post->ID, 'resolu-faute-dinactivite', 'nanosupport_status');
                    update_post_meta( $post->ID, '_ns_ticket_status',   sanitize_text_field( 'resolu-faute-dinactivite' ) );
                    nanosupport_email_on_ticket_update($post);
                }
            }
       }
    }
}

?>