<?php
/**
 * CPT 'nanosupport' and Taxonomy
 *
 * Functions to initiate the Custom Post Type 'nanosupport'
 * and Taxonomy 'nanosupport_Status'.
 *
 * @author      nanodesigns
 * @category    Tickets
 * @package     NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register CPT Tickets
 * 
 * Creating the custom post type 'nanosupport' for tickets.
 *
 * @since  1.0.0
 * 
 * @return array to register a post type.
 * -----------------------------------------------------------------------
 */
function ns_register_cpt_nanosupport() {

    $labels = array(
        'name'					=> _x( 'Tickets', 'NanoSupport Ticket', 'nanosupport' ),
        'singular_name'			=> _x( 'Ticket', 'NanoSupport Ticket', 'nanosupport' ),
        'add_new'				=> _x( 'Add New', 'NanoSupport Ticket', 'nanosupport' ),
        'add_new_item'			=> __( 'Add New Ticket', 'nanosupport' ),
        'edit_item'				=> __( 'Edit Ticket', 'nanosupport' ),
        'new_item'				=> __( 'New Ticket', 'nanosupport' ),
        'view_item'				=> __( 'View Ticket', 'nanosupport' ),
        'search_items'			=> __( 'Search Ticket', 'nanosupport' ),
        'not_found'				=> __( 'No Ticket found', 'nanosupport' ),
        'not_found_in_trash'	=> __( 'No Ticket found in Trash', 'nanosupport' ),
        'parent_item_colon'		=> __( 'Parent Ticket:', 'nanosupport' ),
        'menu_name'				=> _x( 'NanoSupport', 'NanoSupport Ticket', 'nanosupport' ),
        'name_admin_bar'        => _x( 'Support Ticket', 'NanoSupport Ticket', 'nanosupport' ),
    );

    $args = array(
        'labels'				=> $labels,
        'hierarchical'			=> false,
        'description'			=> __( 'Get the ticket information', 'nanosupport' ),
        'supports'				=> array( 'title', 'editor', 'author' ),
        'taxonomies'            => array(),
        'menu_icon'				=> '', //setting this using CSS
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
        'rewrite'				=> array( 'slug' => 'support' ),
        'capability_type'       => 'nanosupport',
        'map_meta_cap'          => true
    );

    if( ! post_type_exists( 'nanosupport' ) ) {
        register_post_type( 'nanosupport', $args );
    }

}

add_action( 'init', 'ns_register_cpt_nanosupport' );

function mw_enqueue_color_picker() {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script('nanosupport-color-picker-script', NS()->plugin_url() .'/assets/js/color-picker.js', array('wp-color-picker'), false, true );
}
add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );

// A callback function to add a custom field to our "presenters" taxonomy  
function presenters_taxonomy_custom_fields($tag) { 
    // Check for existing taxonomy meta for the term you're editing  
     $t_id = $tag->term_id; // Get the ID of the term you're editing
     $get_term_color = get_term_meta($t_id, 'meta_color', true);
     $get_term_shipping = get_term_meta($t_id, 'meta_shipping', true);
     $get_term_hide_rma = get_term_meta($t_id, 'meta_hide_rma', true);
 ?>  

<tr class="form-field">  
     <th scope="row" valign="top">  
         <label for="presenter_id"><?php _e('Do not show RMA number'); ?></label>  
     </th>  
     <td>  
        <?php if ( $get_term_hide_rma == 1 ) {
            echo '<input type="checkbox" name="meta_hide_rma" checked="checked" value="1" class="my-rma-field" ></input>';
            echo '<p></p>';
        } else {
            echo '<input type="checkbox" name="meta_hide_rma" value="1" class="my-rma-field" ></input>';
            echo '<p></p>';
        } ?>
     </td>  
 </tr>
   
 <tr class="form-field">  
     <th scope="row" valign="top">  
         <label for="presenter_id"><?php _e('Status color'); ?></label>  
     </th>  
     <td>  
        <?php echo '<input type="text" name="meta_color" id="meta_color" value="'. $get_term_color .'" class="my-color-field" ></input>' ?>
     </td>  
 </tr>

 <tr class="form-field">  
     <th scope="row" valign="top">  
         <label for="presenter_id"><?php _e( 'Shipping information', 'nanosupport' ); ?></label>  
     </th>  
     <td>
        <?php if ( $get_term_shipping == 1 ) {
            echo '<input type="radio" name="meta_shipping" value="0">'. __( 'Dont show shipping/return information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" checked="checked" value="1">'. __( 'Show shipping information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" value="2">'. __( 'Show return information', 'nanosupport' ) .'</input>';
            echo '<p></p>';
        } elseif ( $get_term_shipping == 2 ) {
            echo '<input type="radio" name="meta_shipping" value="0">'. __( 'Dont show shipping/return information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" value="1">'. __( 'Show shipping information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" checked="checked" value="2">'. __( 'Show return information', 'nanosupport' ) .'</input>';
            echo '<p></p>';
        } else {
            echo '<input type="radio" name="meta_shipping" checked="checked" value="0">'. __( 'Dont show shipping/return information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" value="1">'. __( 'Show shipping information', 'nanosupport' ) .'</input><br>';
            echo '<input type="radio" name="meta_shipping" value="2">'. __( 'Show return information', 'nanosupport' ) .'</input>';
            echo '<p></p>';
        } ?>
     </td>  
 </tr> 
   
 <?php  
 }  

 // A callback function to save our extra taxonomy field(s)  
 function save_taxonomy_custom_meta( $term_id ) {
    if ( isset($_POST['meta_color']) ) {
        update_term_meta( $term_id, 'meta_color', esc_attr($_POST['meta_color']) );
    }

    if ( isset($_POST['meta_shipping']) ) {
        update_term_meta( $term_id, 'meta_shipping', intval($_POST['meta_shipping']) );
    }

    if ( isset($_POST['meta_hide_rma']) ) {
        update_term_meta( $term_id, 'meta_hide_rma', intval($_POST['meta_hide_rma']) );
    }
} 

// Add the fields to the "presenters" taxonomy, using our callback function  
add_action( 'nanosupport_status_edit_form_fields', 'presenters_taxonomy_custom_fields', 10, 2 );  
add_action( 'nanosupport_status_add_form_fields', 'presenters_taxonomy_custom_fields', 10, 2 ); 

// Save the changes made on the "presenters" taxonomy, using our callback function  
add_action( 'edited_nanosupport_status', 'save_taxonomy_custom_meta', 10, 2 ); 
add_action( 'create_nanosupport_status', 'save_taxonomy_custom_meta', 10, 2 ); 

/**
 * Register columns for our taxonomy
 */
function gwp_register_category_columns( $columns ) {
    $new_columns = array(
            'color' => __( 'Color', 'generatewp' ),
        );
    return array_merge( $columns, $new_columns );
}
add_filter( 'manage_edit-nanosupport_status_columns', 'gwp_register_category_columns' );

function gwp_category_column_display( $string = '', $column, $term_id ) {

    switch ( $column ) {
        case 'color' :   
            $get_term_color = get_term_meta($term_id, 'meta_color', true);
            echo '<div style="padding: 15px; background-color: '. $get_term_color .'"></div>';
            break;

    }

}
add_filter( 'manage_nanosupport_status_custom_column', 'gwp_category_column_display', 10, 3 );

/**
 * Show pending count on menu.
 *
 * @since  1.0.0
 *
 * @see    ns_ticket_status_count()
 * @return integer  Pending count beside menu label.
 * -----------------------------------------------------------------------
 */
function ns_notification_bubble_in_nanosupport_menu() {
    global $menu, $current_user;

    if( ns_is_user( 'agent' ) ) {
        $pending_count = ns_ticket_status_count( 'pending', $current_user->ID );
    } else {
        $pending_items = wp_count_posts( 'nanosupport' );
        $pending_count = $pending_items->pending;
    }

    $menu[29][0] .= ! empty($pending_count) ? " <span class='update-plugins count-1' title='". esc_attr__( 'Pending Tickets', 'nanosupport' ) ."'><span class='update-count'>$pending_count</span></span>" : '';
}

add_action( 'admin_menu', 'ns_notification_bubble_in_nanosupport_menu' );


/**
 * Declare custom columns
 *
 * Made custom columns specific to the Support Tickets.
 *
 * @since  1.0.0
 * 
 * @param  array $columns Default columns.
 * @return array          Merged with new columns.
 * -----------------------------------------------------------------------
 */
function ns_set_custom_columns( $columns ) {
    unset($columns['date']);
    unset($columns['department']);
    unset($columns['author']);
    unset($columns['language']);
    unset($columns['taxonomy-nanosupport_form_factor']);
    unset($columns['taxonomy-nanosupport_status']);
    unset($columns['taxonomy-nanosupport_department']);
    $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'RMA', 'nanosupport' ),
            'number' => __( 'RMA Number', 'nanosupport' ),
            'form_factor' => __( 'From Factor', 'nanosupport' ),
            'sn' => __( 'Serial Number', 'nanosupport' ),
            'issues' => __( 'Issues', 'nanosupport' ),
            'ticket_status'     => __( 'Ticket Status', 'nanosupport' ),
            'internal_note' => __( 'Internal Note', 'nanosupport' ),
            'languages'  => __( 'languages', 'nanosupport' ),
            'ticket_responses'  => '<i class="dashicons dashicons-format-chat" title="'. esc_attr__( 'Responses', 'nanosupport' ) .'"></i>',
            'author'     => __( 'Author', 'nanosupport' ),
            'created_date'     => __( 'Created date', 'nanosupport' ),
        );
    return array_merge( $columns, $new_columns );
}

add_filter( 'manage_nanosupport_posts_columns', 'ns_set_custom_columns' );


/**
 * Populate columns with contents
 *
 * Populate support ticket columns with respective contents.
 *
 * @since  1.0.0
 * 
 * @param  array $column    Columns.
 * @param  integer $post_id Each of the post ID.
 * @return array            Columns with information.
 * -----------------------------------------------------------------------
 */
function ns_populate_custom_columns( $column, $post_id ) {

    $ticket_meta = ns_get_ticket_meta( get_the_ID() );
    $_ns_ticket_form_factor   = get_post_meta( $post_id, '_ns_ticket_form_factor', true );

    $form_factor_terms = get_terms( array(
        'taxonomy' => 'nanosupport_form_factor',
        'hide_empty' => false,
    ) );

    switch ( $column ) {

        case 'number' :
            echo get_post_meta( $post_id, 'ns_internal_rma_number', true );
            break;

        case 'form_factor' :
            if ( $form_factor_terms ) {
                foreach ( $form_factor_terms as $form_factor_term ) {
                    if ( $_ns_ticket_form_factor == $form_factor_term->slug ) {
                        $lang_text_term = qtranxf_use(qtranxf_getLanguage(), $form_factor_term->name);
                        echo $lang_text_term;
                    }
                }
            }
            break;

        case 'sn' :
            echo get_post_meta( $post_id, '_ns_ticket_serial_number', true );
            break;
        
        case 'issues' :
            echo get_post_meta( $post_id, '_ns_ticket_issuse', true );
            break;

        case 'ticket_status' :
            echo $ticket_meta['status']['label'];
            break;

        case 'ticket_responses' :
            $responses = wp_count_comments( $post_id );
            $response_count = $responses->approved;
            $last_response  = ns_get_last_response( $post_id );
            $last_responder = get_userdata( $last_response['user_id'] );

            if( ! empty($response_count) ) {
                echo '<span class="responses-count" aria-hidden="true">'. $response_count .'</span>';
                /* translators: Response count 1. singular 2. plural */
                echo '<span class="screen-reader-text">'. sprintf( _n( '%s response', '%s responses', $response_count, 'nanosupport' ), $response_count ) .'</span>';
            } else {
                echo '&mdash; <span class="screen-reader-text">'. __( 'No response yet', 'nanosupport' ) .'</span>';
            }
            echo '</br>';
            if ( $last_responder ) {
                echo $last_responder->display_name, '<br>';
                /* translators: time difference from current time. eg. 12 minutes ago */
                printf( __( '%s ago', 'nanosupport' ), human_time_diff( strtotime($last_response['comment_date']), current_time('timestamp') ) );
            } else {
                echo '&mdash; <span class="screen-reader-text">'. __( 'No response yet', 'nanosupport' ) .'</span>';
            }
            break;

        case 'internal_note' :
            echo get_post_meta( $post_id, 'ns_internal_note', true );
            break;

        case 'languages' :
            $fr_ = qtranxf_isAvailableIn($post_id, 'fr');
            $en_ = qtranxf_isAvailableIn($post_id, 'en');
            if ($fr_){
               echo  __( 'French', 'nanosupport' );
            }
            echo '</br>';
            if ($en_){
               echo __( 'English', 'nanosupport' );
            }
            break;
        case 'created_date' :
            echo get_the_date();
            break;

    }
}

add_action( 'manage_nanosupport_posts_custom_column' , 'ns_populate_custom_columns', 10, 2 );


/**
 * Add Ticket Filter fields.
 *
 * Display additional filters to tickets.
 * 
 * @author Ohad Raz
 * @author Bainternet
 * @link   https://wordpress.stackexchange.com/a/45447/22728
 * 
 * @return void
 * -----------------------------------------------------------------------
 */
function ns_admin_tickets_filter() {
    $post_type = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
    $post_type = ! empty($post_type) ? $post_type : 'post';

    if ('nanosupport' === $post_type) {

        $status_terms = get_terms( array(
            'taxonomy' => 'nanosupport_status',
            'hide_empty' => true,
        ) );

        $form_factor_terms = get_terms( array(
            'taxonomy' => 'nanosupport_form_factor',
            'hide_empty' => true,
        ) );

        if ( $status_terms ) {
            foreach ( $status_terms as $status_term ) {
                $lang_text_term = qtranxf_use(qtranxf_getLanguage(), $status_term->name);
                $ticket_status_values[$status_term->slug] = $lang_text_term;
            }
        }

        if ( $form_factor_terms ) {
            foreach ( $form_factor_terms as $form_factor_term ) {
                $lang_text_term = qtranxf_use(qtranxf_getLanguage(), $form_factor_term->name);
                $ticket_form_factor_values[$form_factor_term->slug] = $lang_text_term;
            }
        }
        ?>

        <select name="ticket_status">
            <option value=""><?php esc_html_e('All Ticket Status', 'nanosupport'); ?></option>
            <?php
                $status_filter = filter_input(INPUT_GET, 'ticket_status', FILTER_SANITIZE_STRING);
                foreach ($ticket_status_values as $value => $label) :
                    printf (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value === $status_filter ? ' selected="selected"' : '',
                        $label
                    );
                endforeach;
            ?>
        </select>

        <select name="ticket_form_factor">
            <option value=""><?php esc_html_e('All Ticket Form Factor', 'nanosupport'); ?></option>
            <?php
                $status_filter = filter_input(INPUT_GET, 'ticket_form_factor', FILTER_SANITIZE_STRING);
                foreach ($ticket_form_factor_values as $value => $label) :
                    printf (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value === $status_filter ? ' selected="selected"' : '',
                        $label
                    );
                endforeach;
            ?>
        </select>

        <?php
    }
}

add_action( 'restrict_manage_posts', 'ns_admin_tickets_filter' );

add_filter( 'manage_edit-nanosupport_sortable_columns', 'my_sortable_nanosupport_column' );
function my_sortable_nanosupport_column( $columns ) {
    $columns['number'] = 'number';
 
    //To make a column 'un-sortable' remove it from the array
    //unset($columns['date']);
 
    return $columns;
}

add_action( 'pre_get_posts', 'my_number_orderby' );
function my_number_orderby( $query ) {
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby');
 
    if( 'number' == $orderby ) {
        $query->set('meta_key','ns_internal_rma_number');
        $query->set('orderby','meta_value_max');
    }
}

/**
 * Filter Admin Tickets based on Filter.
 *
 * @author Ohad Raz
 * @author Bainternet
 * @link   https://wordpress.stackexchange.com/a/45447/22728
 * 
 * @param  object $query WP_Query object.
 * @return object        Modified query object.
 * -----------------------------------------------------------------------
 */
function ns_admin_tickets_filter_query( $query ){
    global $pagenow;

    $post_type = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);

    if ( is_admin() && 'nanosupport' === $post_type && 'edit.php' === $pagenow ) {

        $status_filter   = filter_input(INPUT_GET, 'ticket_status', FILTER_SANITIZE_STRING);
        $form_factor_filter   = filter_input(INPUT_GET, 'ticket_form_factor', FILTER_SANITIZE_STRING);

        $_meta_query = array();
        $_meta_form_query = array();

        if( $status_filter ) {
            $_meta_query[] = array(
                'key'   => '_ns_ticket_status',
                'value' => $status_filter
            );
        }

        if( $form_factor_filter ) {
            $_meta_form_query[] = array(
                'key'   => '_ns_ticket_form_factor',
                'value' => $form_factor_filter
            );
        }

        // If any of 2 or more filter present at once.
        // @link https://stackoverflow.com/a/39484680/1743124
        if( count( array_filter(array($status_filter)) ) >= 2 ) :
            $_meta_query['relation'] = 'AND';
        endif;

        if( count( array_filter(array($form_factor_filter)) ) >= 2 ) :
            $_meta_form_query['relation'] = 'AND';
        endif;

        if( !empty($_meta_query) ) {
            $query->set( 'meta_query', $_meta_query );
        }

        if( !empty($_meta_form_query) ) {
            $query->set( 'meta_query', $_meta_form_query );
        }

        if( !empty($_meta_query) && !empty($_meta_form_query) ) {
            $query->set( 'meta_query', array($_meta_query, $_meta_form_query) );
        }
        
    }

}

add_filter( 'parse_query', 'ns_admin_tickets_filter_query' );


/**
 * Register Custom Taxonomy
 * 
 * Create Custom Taxonomy 'nanosupport_department' to sort out the tickets.
 *
 * @since  1.0.0
 * 
 * @return array To register the custom taxonomy.
 * -----------------------------------------------------------------------
 */
function ns_create_nanosupport_taxonomies() {

    $labels = array(
        'name'              => __( 'Status', 'nanosupport' ),
        'singular_name'     => __( 'Status', 'nanosupport' ),
        'search_items'      => __( 'Search Status', 'nanosupport' ),
        'all_items'         => __( 'All Status', 'nanosupport' ),
        'parent_item'       => __( 'Parent Status', 'nanosupport' ),
        'parent_item_colon' => __( 'Parent Status:', 'nanosupport' ),
        'edit_item'         => __( 'Edit Status', 'nanosupport' ),
        'update_item'       => __( 'Update Status', 'nanosupport' ),
        'add_new_item'      => __( 'Add New Status', 'nanosupport' ),
        'new_item_name'     => __( 'New Status Name', 'nanosupport' ),
        'menu_name'         => __( 'Status', 'nanosupport' ),
    );

    $args = array(
        'hierarchical'      => false,
        'public'            => false,
        'show_tagcloud'     => false,
        'taxonomies'        => array( 'nanosupport_status' ),
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_quick_edit'=> false,
        'meta_box_cb'       => false,
        'update_count_callback' => '_update_generic_term_count',
    );

    if( ! taxonomy_exists( 'nanosupport_status' ) )
        register_taxonomy( 'nanosupport_status', array( 'nanosupport' ), $args );

    $labels = array(
        'name'              => __( 'Form factor', 'nanosupport' ),
        'singular_name'     => __( 'Form factor', 'nanosupport' ),
        'search_items'      => __( 'Search Form factor', 'nanosupport' ),
        'all_items'         => __( 'All Form factor', 'nanosupport' ),
        'parent_item'       => __( 'Parent Form factor', 'nanosupport' ),
        'parent_item_colon' => __( 'Parent Form factor:', 'nanosupport' ),
        'edit_item'         => __( 'Edit Form factor', 'nanosupport' ),
        'update_item'       => __( 'Update Form factor', 'nanosupport' ),
        'add_new_item'      => __( 'Add New Form factor', 'nanosupport' ),
        'new_item_name'     => __( 'New Form factor Name', 'nanosupport' ),
        'menu_name'         => __( 'Form factor', 'nanosupport' ),
    );

    $args = array(
        'hierarchical'      => false,
        'public'            => false,
        'show_tagcloud'     => false,
        'taxonomies'        => array( 'nanosupport_form_factor' ),
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_quick_edit'=> false,
        'meta_box_cb'       => false,
        'update_count_callback' => '_update_generic_term_count',
    );

    if( ! taxonomy_exists( 'nanosupport_form_factor' ) )
        register_taxonomy( 'nanosupport_form_factor', array( 'nanosupport' ), $args );

}

add_action( 'init', 'ns_create_nanosupport_taxonomies', 0 );

function ns_create_nanosupport_taxonomies_default() {

    $status_terms = get_terms( array(
        'taxonomy' => 'nanosupport_status',
        'hide_empty' => false,
    ) );

    if ( $status_terms ) {
        $open = 0;
        $pending = 0;
        $solved = 0;
        foreach ( $status_terms as $status_term ) {
            if ( $status_term->slug == 'open' ) {
                $open = 1;
            }
            if ( $status_term->slug == 'pending' ) {
                $pending = 1;
            }
            if ( $status_term->slug == 'solved' ) {
                $solved = 1;
            }
        }
        if ( $open == 0 ) {
            wp_insert_term(
                'Open', // the term 
                'nanosupport_status', // the taxonomy
                array(
                  'description'=> 'Open',
                  'slug' => 'open',
                )
            );
        }
        if ( $pending == 0 ) {
            wp_insert_term(
                'Pending', // the term 
                'nanosupport_status', // the taxonomy
                array(
                  'description'=> 'Pending',
                  'slug' => 'pending',
                )
            );
        }
        if ( $solved == 0 ) {
            wp_insert_term(
                'Solved', // the term 
                'nanosupport_status', // the taxonomy
                array(
                  'description'=> 'Solved',
                  'slug' => 'solved',
                )
            );
        }
    } else {
        wp_insert_term(
            'Open', // the term 
            'nanosupport_status', // the taxonomy
            array(
              'description'=> 'Open',
              'slug' => 'open',
            )
        );
        wp_insert_term(
            'Pending', // the term 
            'nanosupport_status', // the taxonomy
            array(
              'description'=> 'Pending',
              'slug' => 'pending',
            )
        );
        wp_insert_term(
            'Solved', // the term 
            'nanosupport_status', // the taxonomy
            array(
              'description'=> 'Solved',
              'slug' => 'solved',
            )
        );
    }

}

add_action( 'init', 'ns_create_nanosupport_taxonomies_default' );

/**
 * Copy Ticket button.
 *
 * Add a 'copy to kb' button to each ticket in admin panel.
 *
 * @since  1.0.0
 * 
 * @param  array $actions  WP Post actions.
 * @param  object $post    WP Post Object.
 * @return array           Modified action buttons.
 * -----------------------------------------------------------------------
 */
function ns_copy_ticket_button( $actions, $post ) {
    if( 'nanosupport' === $post->post_type ) {
        // pass nonce to check and verify false request
        $nonce = wp_create_nonce( 'ns_copy_ticket_nonce' );

        // add our button
        $actions['copy_ticket'] = '<a class="ns-copy-post" data-ticket="'. $post->ID .'" data-nonce="'. $nonce .'" href="javascript:">'. esc_html__( 'Copy to KB', 'nanosupport' ) .'</a>';
    }

    return $actions;
}

// Get Knowledgebase settings from db.
$ns_knowledgebase_settings = get_option( 'nanosupport_knowledgebase_settings' );

if( isset($ns_knowledgebase_settings['isactive_kb']) && $ns_knowledgebase_settings['isactive_kb'] === 1 ) {
    add_filter( 'post_row_actions', 'ns_copy_ticket_button', 10, 2 );
}


/**
 * Add more roles to Ticket Author.
 *
 * Add more user roles to Ticket Author meta box so that ticket
 * on behalf of other user can be added.
 *
 * @since  1.0.0
 * 
 * @param  array $query_args  The query arguments for get_users().
 * @param  array $r           The arguments passed to wp_dropdown_users() combined with the defaults.
 * @return array              Modified array of quey arguments for get_users().
 * -----------------------------------------------------------------------
 */
function ns_ticket_author_dropdown_overrides( $query_args, $r ) {

    global $post;

    if( isset($post) && 'nanosupport' === $post->post_type ) {

        // Make it empty to 'role__in' act.
        $query_args['who'] = '';

        /**
         * -----------------------------------------------------------------------
         * HOOK : FILTER HOOK
         * nanosupport_assigned_user_role
         *
         * The user roles that are passed to generate override HTML.
         * You can add/modify the roles using the hook.
         * 
         * @since  1.0.0
         *
         * @param array  The user roles.
         * -----------------------------------------------------------------------
         */
        $query_args['role__in'] = apply_filters( 'nanosupport_assigned_user_role', array(
                                    'support_seeker',
                                    'administrator',
                                    'author',
                                    'editor',
                                )
                            );
    }

    return $query_args;
 
}

add_filter( 'wp_dropdown_users_args', 'ns_ticket_author_dropdown_overrides', 10, 2 );


/**
 * Add some help text before post author field.
 *
 * @since  1.0.0
 * 
 * @param  string $select_field The core HTML.
 * @return string               Modified HTML with help string.
 * -----------------------------------------------------------------------
 */
function ns_help_text_to_post_author( $output )  {
    global $post;

    if( isset($post) && 'nanosupport' === $post->post_type ) {
        // Prepend some help text before select field.
        $output = '<p>'. esc_html__( 'Add the ticket on behalf of anybody', 'nanosupport' ) .'</p>'. $output;
    }

    return $output;
}

add_filter( 'wp_dropdown_users', 'ns_help_text_to_post_author' );

/**
 * Keep the Submission Date while Publishing Ticket.
 *
 * The date of the primary submission as 'pending', was changed
 * while publishing the ticket as 'private' with the date of
 * publish. With this hook, the issue is resolved.
 *
 * @author Paul 'Sparrow Hawk' Biron
 * @link   https://wordpress.stackexchange.com/a/262306/22728
 *
 * @param  array $data     Post Data array.
 * @param  array $postarr  Post Array.
 * @return array           Modified Post Data array.
 * -----------------------------------------------------------------------
 */
function ns_keep_pending_date_on_publishing($data, $postarr)
{
	if( 'nanosupport' !== $data['post_type'] ) {
		return $data;
	}
	// these checks are the same thing as transition_post_status(private, pending)
	if( 'private' !== $data['post_status'] || 'pending' !== $postarr['original_post_status'] ) {
		return $data;
	}
	$pending_datetime = get_post_field('post_date', $data['ID'], 'raw');
	$data['post_date']     = $pending_datetime ;
	$data['post_date_gmt'] = get_gmt_from_date($pending_datetime);
	return $data;
}
add_filter( 'wp_insert_post_data', 'ns_keep_pending_date_on_publishing', 10, 2 );

/**
 * Remove Ticket Publishing Date filter.
 * Making sure the post date filter trigger only once.
 *
 * @author kaiser
 * @link   https://wordpress.stackexchange.com/a/262328/22728
 *
 * @see    ns_keep_pending_date_on_publishing()
 *
 * @return void.
 */
function ns_remove_onetime_filter()
{
    remove_filter( 'wp_insert_post_data', 'ns_keep_pending_date_on_publishing' );
}
add_action( 'transition_post_status', 'ns_remove_onetime_filter' );

add_filter( 'posts_join', 'segnalazioni_search_join' );
function segnalazioni_search_join ( $join ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
    if ( is_admin() && 'edit.php' === $pagenow && 'nanosupport' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {    
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'segnalazioni_search_where' );
function segnalazioni_search_where( $where ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
    if ( is_admin() && 'edit.php' === $pagenow && 'nanosupport' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where );
    }
    return $where;
}