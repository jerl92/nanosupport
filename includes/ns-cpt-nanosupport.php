<?php
/**
 * CPT 'nanosupport' and Taxonomy
 *
 * Functions to initiate the Custom Post Type 'nanosupport'
 * and Taxonomy 'nanosupport_departments'.
 *
 * @package NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CPT
 * 
 * Creating the 'nanosupport' CPT for tickets.
 * 
 * @return array to register a post type.
 * -----------------------------------------------------------------------
 */
function ns_register_cpt_nanosupport() {

    $labels = array(
        'name'					=> __( 'Tickets', 'nanosupport' ),
        'singular_name'			=> __( 'Ticket', 'nanosupport' ),
        'add_new'				=> __( 'Add New', 'nanosupport' ),
        'add_new_item'			=> __( 'Add New Ticket', 'nanosupport' ),
        'edit_item'				=> __( 'Edit Ticket', 'nanosupport' ),
        'new_item'				=> __( 'New Ticket', 'nanosupport' ),
        'view_item'				=> __( 'View Ticket', 'nanosupport' ),
        'search_items'			=> __( 'Search Ticket', 'nanosupport' ),
        'not_found'				=> __( 'No Ticket found', 'nanosupport' ),
        'not_found_in_trash'	=> __( 'No Ticket found in Trash', 'nanosupport' ),
        'parent_item_colon'		=> __( 'Parent Ticket:', 'nanosupport' ),
        'menu_name'				=> __( 'Supports', 'nanosupport' ),
    );

    $args = array(
        'labels'				=> $labels,
        'hierarchical'			=> false,
        'description'			=> __( 'Get the ticket information', 'nanosupport' ),
        'supports'				=> array( 'title', 'editor' ),
        'taxonomies'            => array(),
        'menu_icon'				=> 'dashicons-universal-access-alt',
        'public'				=> true,
        'show_ui'				=> true,
        'show_in_menu'			=> true,
        'menu_position'			=> 29,
        	'show_in_nav_menus'		=> false,
        'publicly_queryable'	=> true,
        'exclude_from_search'	=> false,
        	'has_archive'			=> false,
        'query_var'				=> true,
        'can_export'			=> true,
        'rewrite'				=> array( 'slug' => 'nanosupport' ),
        'capability_type'       => 'post',
        /*'capabilities'          => array(
                                    'edit_post'             => 'edit_ns',
                                    'edit_posts'            => 'edit_nss',
                                    'edit_others_posts'     => 'edit_other_nss',
                                    'publish_posts'         => 'publish_nss',
                                    'read_post'             => 'read_ns',
                                    'read_private_posts'    => 'read_private_nss',
                                    'delete_post'           => 'delete_ns'
                                ),
        'map_meta_cap'          => true*/
    );

    if( !post_type_exists( 'nanosupport' ) )
        register_post_type( 'nanosupport', $args );

    /**
     * To activate CPT Single page
     * @author  Bainternet
     * @link http://en.bainternet.info/2011/custom-post-type-getting-404-on-permalinks
     * ---
     */
    $set = get_option( 'post_type_rules_flased_nanosupport' );
    if ( $set !== true ){
		flush_rewrite_rules( false );
		update_option( 'post_type_rules_flased_nanosupport', true );
    }

}
add_action( 'init', 'ns_register_cpt_nanosupport' );


/**
 * Declare custom columns
 * @param  array $columns Default columns.
 * @return array          Merged with new columns.
 * -----------------------------------------------------------------------
 */
function ns_set_custom_columns( $columns ) {
    $new_columns = array(
            'ticket_priority'   => __( 'Priority', 'nanosupport' ),
            'ticket_responses'  => '<span class="dashicons dashicons-format-chat" title="Responses"></span>',
            'ticket_status'     => __( 'Ticket Status', 'nanosupport' ),
            'last_response'     => __( 'Last Response by', 'nanosupport' )
        );
    return array_merge( $columns, $new_columns );
} 
add_filter( 'manage_nanosupport_posts_columns', 'ns_set_custom_columns' );


/**
 * Populate columns with respective contents.
 * @param  array $column    Columns.
 * @param  integer $post_id Each of the post ID.
 * @return array            Columns with information.
 * -----------------------------------------------------------------------
 */
function ns_populate_custom_columns( $column, $post_id ) {
    $ticket_control = get_post_meta( $post_id, 'ns_control', true );
    switch ( $column ) {
        case 'ticket_priority' :
            $ticket_priority = $ticket_control ? $ticket_control['priority'] : false;
            if( $ticket_priority && 'low' === $ticket_priority ) {
                echo '<strong>'. __( 'Low', 'nanosupport' ) .'</strong>';
            } else if( $ticket_priority && 'medium' === $ticket_priority ) {
                echo '<strong class="text-info">' , __( 'Medium', 'nanosupport' ) , '</strong>';
            } else if( $ticket_priority && 'high' === $ticket_priority ) {
                echo '<strong class="text-warning">' , __( 'High', 'nanosupport' ) , '</strong>';
            } else if( $ticket_priority && 'critical' === $ticket_priority ) {
                echo '<strong class="text-danger">' , __( 'Critical', 'nanosupport' ) , '</strong>';
            }
            break;

        case 'ticket_responses' :
            $responses = wp_count_comments( $post_id );
            $response_count = $responses->approved;

            if( !empty($response_count) ) {
                echo '<span class="responses-count" aria-hidden="true">'. $response_count .'</span>';
                echo '<span class="screen-reader-text">'. sprintf( _n( '%s response', '%s responses', $response_count, 'nanosupport' ), $response_count ) .'</span>';
            } else {
                echo '&mdash;';
            }
            break;

        case 'ticket_status' :
            $ticket_status = $ticket_control ? $ticket_control['status'] : false;
            if( $ticket_status ) {
                if( 'solved' === $ticket_status ) {
                    $status = '<span class="label label-success">'. __( 'Solved', 'nanosupport' ) .'</span>';
                } else if( 'inspection' === $ticket_status ) {
                    $status = '<span class="label label-primary">'. __( 'Under Inspection', 'nanosupport' ) .'</span>';
                } else {
                    $status = '<span class="label label-warning">'. __( 'Open', 'nanosupport' ) .'</span>';
                }
            } else {
                $status = '';
            }
            echo $status;
            break;

        case 'last_response' :
            $last_response = ns_get_last_response( $post_id );
            $last_responder = get_userdata( $last_response['user_id'] );
            if ( $last_responder ) {
                echo $last_responder->display_name, '<br>';
                printf( __( '%s ago', 'nanosupport' ), human_time_diff( strtotime($last_response['comment_date']), current_time('timestamp') ) );
            } else {
                echo '-';
            }
            break;
    }
}
add_action( 'manage_nanosupport_posts_custom_column' , 'ns_populate_custom_columns', 10, 2 );


/**
 * NS Ticket Control Meta Fields.
 * hooked into: 'post_submitbox_misc_actions'
 * -----------------------------------------------------------------------
 */
function ns_control_specifics() {
    global $post;

    if( 'nanosupport' === $post->post_type ) :

        // Use nonce for verification
        wp_nonce_field( basename( __FILE__ ), 'ns_control_nonce' );


        $ns_control_array = get_post_meta( $post->ID, 'ns_control', true );

        if( ! $ns_control_array ) {
            
            //default
            $ns_control_array = array(
                                    'status'    => 'open',
                                    'priority'  => 'low',
                                    'agent'     => ''
                                );

        }
        ?>
        <div class="row ns-control-holder">

            <div class="ns-row misc-pub-section">
                <div class="ns-head-col">
                    <span class="dashicons dashicons-shield"></span> <?php _e( 'Ticket Status', 'nanosupport' ); ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <select name="ns_ticket_status" class="ns-field-item" id="ns-ticket-status">
                            <option value="open" <?php selected( $ns_control_array['status'], 'open' ); ?>><?php _e( 'Open', 'nanosupport' ); ?></option>
                            <option value="inspection"<?php selected( $ns_control_array['status'], 'inspection' ); ?>><?php _e( 'Under Inspection', 'nanosupport' ); ?></option>
                            <option value="solved"<?php selected( $ns_control_array['status'], 'solved' ); ?>><?php _e( 'Solved', 'nanosupport' ); ?></option>
                        </select>
                    </div> <!-- /.ns-field -->                    
                </div>
            </div> <!-- /.ns-row -->

            <div class="ns-row misc-pub-section">
                <div class="ns-head-col">
                    <span class="dashicons dashicons-sort"></span> <?php _e( 'Priority', 'nanosupport' ); ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <select name="ns_ticket_priority" class="ns-field-item" id="ns-ticket-priority">
                            <option value="low" <?php selected( $ns_control_array['priority'], 'low' ); ?>><?php _e( 'Low', 'nanosupport' ); ?></option>
                            <option value="medium" <?php selected( $ns_control_array['priority'], 'medium' ); ?>><?php _e( 'Medium', 'nanosupport' ); ?></option>
                            <option value="high" <?php selected( $ns_control_array['priority'], 'high' ); ?>><?php _e( 'High', 'nanosupport' ); ?></option>
                            <option value="critical" <?php selected( $ns_control_array['priority'], 'critical' ); ?>><?php _e( 'Critical', 'nanosupport' ); ?></option>
                        </select>
                    </div> <!-- /.ns-field -->                    
                </div>
            </div> <!-- /.ns-row -->

            <div class="ns-row misc-pub-section">
                <div class="ns-head-col">
                    <span class="dashicons dashicons-businessman"></span> <?php _e( 'Agent', 'nanosupport' ); ?>
                </div>
                <div class="ns-body-col">
                    <?php
                    $agent_query = new WP_User_Query( array(
                            'meta_key'      => 'ns_make_agent',
                            'meta_value'    => 1,
                            'orderby'       => 'display_name'
                        ) );
                    ?>
                    <div class="ns-field">
                        <select name="ns_ticket_agent" class="ns-field-item" id="ns-ticket-agent">
                            <?php
                            if ( ! empty( $agent_query->results ) ) {
                                echo '<option value="">'. __( 'Assign an agent', 'nanosupport' ) .'</option>';
                                foreach ( $agent_query->results as $user ) {
                                    echo '<option value="'. $user->ID .'" '. selected( $ns_control_array['agent'], $user->ID ) .'>'. $user->display_name .'</option>';
                                }
                            } else {
                                echo '<option value="">'. __( 'No agent found', 'nanosupport' ) .'</option>';
                            }
                            ?>
                        </select>
                    </div> <!-- /.ns-field -->                    
                </div>
            </div> <!-- /.ns-row -->

        </div> <!-- .ns-control-holder -->
        <?php

    endif;
}
add_action('post_submitbox_misc_actions', 'ns_control_specifics');


/**
 * Save NS Ticket Control Meta Fields.
 * @param  integer $post_id Ticket Post ID.
 * -----------------------------------------------------------------------
 */
function ns_save_control_meta_data( $post_id ) {
     
    // verify nonce
    if (!isset($_POST['ns_control_nonce']) || !wp_verify_nonce($_POST['ns_control_nonce'], basename(__FILE__)))
        return $post_id;
    
    // check autosave
    if ( wp_is_post_autosave( $post_id ) )
        return $post_id;

    //check post revision
    if ( wp_is_post_revision( $post_id ) )
        return $post_id;
    
    // check permissions
    if ( 'nanosupport' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    $ns_ticket_status      = $_POST['ns_ticket_status'];
    $ns_ticket_priority    = $_POST['ns_ticket_priority'];
    $ns_ticket_agent       = $_POST['ns_ticket_agent'];

    $ns_control = array(
            'status'    => sanitize_text_field( $ns_ticket_status ),
            'priority'  => sanitize_text_field( $ns_ticket_priority ),
            'agent'     => absint( $ns_ticket_agent )
        );

    update_post_meta( $post_id, 'ns_control', $ns_control );
}

add_action( 'save_post',        'ns_save_control_meta_data' );
add_action( 'new_to_publish',   'ns_save_control_meta_data' );


/**
 * Register Custom Taxonomy
 * 
 * Create Custom Taxonomy 'nanosupport_departments' to sort out the tickets.
 * 
 * @return array To register the custom taxonomy.
 * -----------------------------------------------------------------------
 */
function ns_create_nanosupport_taxonomies() {

    $labels = array(
        'name'              => __( 'Departments', 'nanosupport' ),
        'singular_name'     => __( 'Department', 'nanosupport' ),
        'search_items'      => __( 'Search Departments', 'nanosupport' ),
        'all_items'         => __( 'All Departments', 'nanosupport' ),
        'parent_item'       => __( 'Parent Department', 'nanosupport' ),
        'parent_item_colon' => __( 'Parent Department:', 'nanosupport' ),
        'edit_item'         => __( 'Edit Departments', 'nanosupport' ),
        'update_item'       => __( 'Update Departments', 'nanosupport' ),
        'add_new_item'      => __( 'Add New Department', 'nanosupport' ),
        'new_item_name'     => __( 'New Department Name', 'nanosupport' ),
        'menu_name'         => __( 'Departments', 'nanosupport' ),
    );

    $args = array(
        'hierarchical'      => true,
        'public'            => false,
        'show_tagcloud'     => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'support-departments' ),
    );

    if( !taxonomy_exists( 'nanosupport_departments' ) )
        register_taxonomy( 'nanosupport_departments', array( 'nanosupport' ), $args );



    /**
     * Insert default term
     *
     * Insert default term 'Support' to the taxonomy 'nanosupport_departments'.
     *
     * Term: Support
     */
    wp_insert_term(
        'Support', // the term 
        'nanosupport_departments', // the taxonomy
        array(
            'description'=> 'Support department is dedicated to provide the necessary support',
            'slug' => 'support'
        )
    );

}
add_action( 'init', 'ns_create_nanosupport_taxonomies', 0 );


/**
 * Make a Default Taxonomy Term for 'nanosupport_departments'
 *
 * @link http://wordpress.mfields.org/2010/set-default-terms-for-your-custom-taxonomies-in-wordpress-3-0/
 *
 * @author    Michael Fields     http://wordpress.mfields.org/
 * @props     John P. Bloch      http://www.johnpbloch.com/
 *
 * @since     2010-09-13
 * @alter     2010-09-14
 *
 * @license   GPLv2
 * -----------------------------------------------------------------------
 */
function ns_set_default_object_terms( $post_id, $post ) {
    if ( 'publish' === $post->post_status ) {
        $defaults = array(
                'nanosupport_departments' => array( 'support' )
            );
        
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
}
add_action( 'save_post', 'ns_set_default_object_terms', 100, 2 );